<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    use Auditable;

    protected $table = 'devoluciones';

    protected $fillable = ['ingreso_id', 'fecha_devolucion', 'monto', 'motivo', 'registrado_por'];

    protected function casts(): array
    {
        return ['fecha_devolucion' => 'date'];
    }

    protected static function booted(): void
    {
        // Las devoluciones no se anulan ni se borran (decisión del bloque 2b).
        static::deleting(fn () => throw new \LogicException('Las devoluciones no se pueden eliminar.'));
    }

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}