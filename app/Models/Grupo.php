<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grupo extends Model
{
    use Auditable;

    protected $fillable = [
        'nombre', 'horarios_uso', 'representante_id',
        'contacto_nombre', 'contacto_correo', 'contacto_telefono', 'contacto_direccion',
    ];

    public function representante(): BelongsTo
    {
        return $this->belongsTo(Socio::class, 'representante_id');
    }

    /** Representante vigente: socio activo con cuenta de usuario (RFS-39). */
    public function tieneRepresentanteVigente(): bool
    {
        $socio = $this->representante;

        return $socio !== null && $socio->estado === 'activo' && $socio->user_id !== null;
    }
}