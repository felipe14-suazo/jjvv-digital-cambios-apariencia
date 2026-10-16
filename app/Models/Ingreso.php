<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    use Auditable;

    /** En el sprint 1 solo se usan estos conceptos; el resto llega en el sprint 2. */
    public const CONCEPTOS = [
        'certificados' => 'Certificados',
        'otros' => 'Otros',
    ];

    public const MEDIOS = [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
    ];

    protected $fillable = [
        'concepto', 'fecha_pago', 'monto', 'medio_pago',
        'talonario', 'numero_transaccion', 'observaciones',
        'certificado_id', 'registrado_por',
    ];

    protected function casts(): array
    {
        return ['fecha_pago' => 'date'];
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