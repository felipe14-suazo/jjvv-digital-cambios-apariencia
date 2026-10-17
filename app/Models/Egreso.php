<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Egreso extends Model
{
    use Auditable;

    /** Conceptos de egreso (RFS-65). */
    public const CONCEPTOS = [
        'servicios_basicos' => 'Servicios básicos',
        'mantencion' => 'Mantención',
    ];

    protected $fillable = [
        'concepto', 'fecha_pago', 'monto', 'numero_transaccion',
        'comprobante_path', 'pagado_por', 'observaciones', 'registrado_por',
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
        static::deleting(fn () => throw new \LogicException('Los egresos no se pueden eliminar.'));
    }

    public function scopeVigentes(Builder $query): Builder
    {
        return $query->whereNull('anulado_en');
    }

    public function estaAnulado(): bool
    {
        return $this->anulado_en !== null;
    }

    /** Anulación con motivo obligatorio. Queda en la auditoría por el trait Auditable. */
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
}