<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RutChileno implements ValidationRule
{
    /** Deja el RUT como "12345678-9": sin puntos ni espacios y con K en mayúscula. */
    public static function normalizar(?string $rut): string
    {
        return strtoupper(str_replace(['.', ' '], '', (string) $rut));
    }

    public static function esValido(?string $rut): bool
    {
        $rut = self::normalizar($rut);

        if (! preg_match('/^(\d{7,8})-([\dK])$/', $rut, $m)) {
            return false;
        }

        $suma = 0;
        $multiplo = 2;

        foreach (array_reverse(str_split($m[1])) as $digito) {
            $suma += (int) $digito * $multiplo;
            $multiplo = $multiplo === 7 ? 2 : $multiplo + 1;
        }

        $resto = 11 - ($suma % 11);
        $dv = $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);

        return $dv === $m[2];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::esValido((string) $value)) {
            $fail('El RUT ingresado no es válido (formato 12345678-9).');
        }
    }
}
