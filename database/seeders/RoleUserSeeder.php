<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->sembrarAdministradorProduccion();

            return;
        }

        // Solo desarrollo / pruebas: usuarios de ejemplo con contraseña conocida.
        $this->crear('Administrador', 'admin@jjvv.cl', 'admin', 'password');
        $this->crear('Presidente', 'presidente@jjvv.cl', 'presidente', 'password');
        $this->crear('Secretario', 'secretario@jjvv.cl', 'secretario', 'password');
        $this->crear('Tesorero', 'tesorero@jjvv.cl', 'tesorero', 'password');
        $this->crear('Vecino de Prueba', 'vecino@jjvv.cl', 'vecino', 'password');
    }

    private function sembrarAdministradorProduccion(): void
    {
        $email = env('SEED_ADMIN_EMAIL');
        $password = env('SEED_ADMIN_PASSWORD');

        if (! $email || ! $password || strlen($password) < 12) {
            throw new RuntimeException(
                'En producción define SEED_ADMIN_EMAIL y SEED_ADMIN_PASSWORD (mínimo 12 caracteres) en el .env.'
            );
        }

        $this->crear('Administrador', $email, 'admin', $password);
    }

    /**
     * Crea el usuario si no existe. No pisa la contraseña de uno ya existente.
     * Se usa forceFill porque "role" no es asignable en masa.
     */
    private function crear(string $nombre, string $email, string $rol, string $password): void
    {
        $usuario = User::firstOrNew(['email' => $email]);

        if ($usuario->exists) {
            return;
        }

        $usuario->forceFill([
            'name' => $nombre,
            'role' => $rol,
            'password' => $password, // el cast "hashed" la encripta
            'email_verified_at' => now(),
        ])->save();
    }
}
