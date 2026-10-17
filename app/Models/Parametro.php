<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use Auditable;

    protected $table = 'parametros';

    protected $guarded = [];

    public static function valor(string $clave): string
    {
        return static::where('clave', $clave)->value('valor')
            ?? throw new \RuntimeException("Falta el parámetro {$clave}.");
    }

    public static function entero(string $clave): int
    {
        return (int) static::valor($clave);
    }
}