<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'auditorias';

    protected $guarded = [];

    /** Filtros disponibles en la pantalla de auditoría. */
    public const MODELOS = [
        'socio' => Socio::class,
        'certificado' => Certificado::class,
        'usuario' => User::class,
    ];

    protected function casts(): array
    {
        return ['cambios' => 'array'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function etiquetaRegistro(): string
    {
        return class_basename($this->auditable_type).' #'.$this->auditable_id;
    }
}
