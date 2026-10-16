<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DescargaCertificado extends Model
{
    protected $table = 'descargas_certificado';

    public $timestamps = false;

    protected $fillable = ['certificado_id', 'user_id', 'ip', 'descargado_en'];

    protected $casts = ['descargado_en' => 'datetime'];

    protected static function booted(): void
    {
        // Registro de trazabilidad: no se edita ni se elimina (RFS-86).
        static::updating(fn () => throw new \LogicException('Las descargas no se pueden modificar.'));
        static::deleting(fn () => throw new \LogicException('Las descargas no se pueden eliminar.'));
    }

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}