<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Parametro;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class Certificado extends Model
{
    use Auditable;

    public const TIPOS = [
        'residencia' => 'Certificado de residencia',
        'socio' => 'Certificado de socio',
    ];

    public const ESTADOS = [
        'solicitado' => 'Pendiente de revisión',
        'emitido' => 'Emitido',
        'rechazado' => 'Rechazado',
        'anulado' => 'Anulado',
    ];

    protected $attributes = ['estado' => 'solicitado'];

    protected $fillable = [
        'tipo', 'user_id', 'socio_id', 'nombre_solicitante', 'rut_solicitante',
        'direccion', 'dirigido_a', 'aporte', 'documentos_verificados', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
            'documentos_verificados' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificado $certificado) {
            $certificado->codigo_verificacion ??= self::generarCodigo();
        });
    }

    /** Código aleatorio sin caracteres ambiguos (0/O, 1/I) para verificar el certificado en línea. */
    public static function generarCodigo(): string
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 10; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (static::where('codigo_verificacion', $codigo)->exists());

        return $codigo;
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function otorgante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'otorgado_por');
    }

    public function etiquetaTipo(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function etiquetaEstado(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function codigoFormateado(): string
    {
        return substr($this->codigo_verificacion, 0, 5).'-'.substr($this->codigo_verificacion, 5);
    }

    public function urlVerificacion(): string
    {
        return route('verificar.mostrar', $this->codigo_verificacion);
    }

    /** vigente | vencido | anulado | null (si aún no tiene validez pública). */
    public function estadoPublico(): ?string
    {
        return match ($this->estado) {
            'anulado' => 'anulado',
            'emitido' => $this->fecha_vencimiento->lt(today()) ? 'vencido' : 'vigente',
            default => null,
        };
    }

    /**
     * Asigna folio correlativo por año ("2026-0001"), fechas y emisor.
     * La restricción única (anio, correlativo) evita folios repetidos si dos
     * dirigentes emiten a la vez; en ese caso se reintenta.
     */
    public function emitir(User $por): void
    {
        for ($intento = 1; ; $intento++) {
            try {
                DB::transaction(function () use ($por) {
                    $anio = (int) now()->year;
                    $correlativo = ((int) static::where('anio', $anio)->max('correlativo')) + 1;

                    $this->forceFill([
                        'estado' => 'emitido',
                        'anio' => $anio,
                        'correlativo' => $correlativo,
                        'folio' => sprintf('%d-%04d', $anio, $correlativo),
                        'fecha_emision' => now()->toDateString(),
                        'fecha_vencimiento' => now()->addDays(Parametro::entero('certificado_vigencia_dias'))->toDateString(),
                        'otorgado_por' => $por->id,
                    ])->save();
                });

                return;
            } catch (UniqueConstraintViolationException $e) {
                if ($intento >= 3) {
                    throw $e;
                }
            }
        }
    }

    public function nombreArchivoPdf(): string
    {
        return 'certificado-'.($this->folio ?? 'borrador-'.$this->id).'.pdf';
    }

    public function toPdf()
    {
        return Pdf::loadView('certificados.pdf', ['c' => $this])->setPaper('letter');
    }
}
