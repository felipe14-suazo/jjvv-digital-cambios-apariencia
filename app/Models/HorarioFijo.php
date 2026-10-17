<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Horario fijo semanal de un grupo: reserva recurrente (RFS-47, RFS-49). */
class HorarioFijo extends Model
{
    protected $table = 'horarios_fijos';

    protected $fillable = ['grupo_id', 'dia_semana', 'hora_inicio', 'hora_fin'];

    public const DIAS = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
        5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
    ];

    public function nombreDia(): string
    {
        return self::DIAS[$this->dia_semana] ?? '';
    }

    /** Valida un horario fijo: sede, bloques y superposición con otros horarios fijos (RFS-45, RFS-46). */
    public static function errorDeConflicto(int $dia, string $horaInicio, string $horaFin, ?int $ignorarId = null): ?string
    {
        if ($error = Reserva::errorDeBloques($horaInicio, $horaFin)) {
            return $error;
        }

        $choca = static::where('dia_semana', $dia)
            ->when($ignorarId, fn ($q, $id) => $q->where('id', '!=', $id))
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();

        return $choca ? 'Se superpone con otro horario fijo.' : null;
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}