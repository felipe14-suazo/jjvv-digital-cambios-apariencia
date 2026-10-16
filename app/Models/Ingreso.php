<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    use Auditable;

    /** Conceptos de ingreso (RFS-58). "Arriendo" se agrega en el bloque 3b. */
    public const CONCEPTOS = [
        'cuota_socios' => 'Cuota de socios',
        'aporte_grupos' => 'Aporte de grupos',
        'certificados' => 'Certificados',
        'otros' => 'Otros',
    ];

    public const TIPOS_CUOTA = [
        'ordinaria' => 'Ordinaria',
        'extraordinaria' => 'Extraordinaria',
    ];

    public const MEDIOS = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
    ];

    protected $fillable = [
        'concepto', 'fecha_pago', 'monto', 'medio_pago',
        'talonario', 'numero_transaccion', 'observaciones',
        'tipo_cuota', 'certificado_id', 'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'date',
            'anulado_en' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Los movimientos no se borran; se anulan con motivo (RFS-67).
        static::deleting(fn () => throw new \LogicException('Los ingresos no se pueden eliminar.'));
    }

    public function scopeVigentes(Builder $query): Builder
    {
        return $query->whereNull('anulado_en');
    }

    /** Saldo de caja (RFS-68). En el bloque 2b se le restarán egresos y devoluciones. */
    public static function saldoCaja(): int
    {
        return (int) static::vigentes()->sum('monto');
    }

    public function estaAnulado(): bool
    {
        return $this->anulado_en !== null;
    }

    /** Anulación con motivo obligatorio. Queda registrada en la auditoría por el trait Auditable. */
    public function anular(string $motivo, User $por): void
    {
        $this->forceFill([
            'anulado_en' => now(),
            'motivo_anulacion' => $motivo,
            'anulado_por' => $por->id,
        ])->save();
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class);
    }
}