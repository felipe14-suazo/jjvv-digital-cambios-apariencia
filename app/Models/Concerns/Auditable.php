<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * Registra quién y cuándo creó, modificó o eliminó un registro (RFS-04).
 * Uso: agregar "use Auditable;" al modelo.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($modelo) {
            $cambios = [];
            foreach ($modelo->getAttributes() as $campo => $valor) {
                if (! $modelo->auditoriaIgnora($campo)) {
                    $cambios[$campo] = ['antes' => null, 'despues' => $modelo->auditoriaValor($campo, $valor)];
                }
            }
            $modelo->registrarAuditoria('creado', $cambios);
        });

        static::updated(function ($modelo) {
            $cambios = [];
            foreach ($modelo->getChanges() as $campo => $nuevo) {
                if ($modelo->auditoriaIgnora($campo)) {
                    continue;
                }
                $cambios[$campo] = [
                    'antes' => $modelo->auditoriaValor($campo, $modelo->getOriginal($campo)),
                    'despues' => $modelo->auditoriaValor($campo, $nuevo),
                ];
            }

            if ($cambios) {
                $modelo->registrarAuditoria('modificado', $cambios);
            }
        });

        static::deleted(function ($modelo) {
            $cambios = [];
            foreach ($modelo->getOriginal() as $campo => $valor) {
                if (! $modelo->auditoriaIgnora($campo)) {
                    $cambios[$campo] = ['antes' => $modelo->auditoriaValor($campo, $valor), 'despues' => null];
                }
            }
            $modelo->registrarAuditoria('eliminado', $cambios);
        });
    }

    public function auditoriaIgnora(string $campo): bool
    {
        return in_array($campo, ['created_at', 'updated_at', 'remember_token'], true);
    }

    public function auditoriaValor(string $campo, mixed $valor): mixed
    {
        return in_array($campo, ['password'], true) ? '[oculto]' : $valor;
    }

    public function registrarAuditoria(string $evento, array $cambios): void
    {
        $autor = Auth::id();

        // Si alguien elimina su propia cuenta, el usuario ya no existe para la clave foránea.
        if ($evento === 'eliminado' && $this instanceof Authenticatable && $autor === $this->getKey()) {
            $autor = null;
        }

        Auditoria::create([
            'user_id' => $autor,
            'evento' => $evento,
            'auditable_type' => $this->getMorphClass(),
            'auditable_id' => $this->getKey(),
            'cambios' => $cambios,
            'ip' => request()?->ip(),
        ]);
    }
}
