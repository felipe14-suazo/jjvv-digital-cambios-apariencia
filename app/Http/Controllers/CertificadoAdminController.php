<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Socio;
use App\Rules\RutChileno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Dirigentes: revisan solicitudes, emiten certificados presenciales y los anulan. */
class CertificadoAdminController extends Controller
{
    public function index(Request $request)
    {
        $certificados = Certificado::query()
            ->when($request->estado, fn ($q, $v) => $q->where('estado', $v))
            ->when($request->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->buscar, function ($q, $texto) {
                $q->where(function ($q) use ($texto) {
                    $q->where('nombre_solicitante', 'like', "%{$texto}%")
                        ->orWhere('rut_solicitante', 'like', "%{$texto}%")
                        ->orWhere('folio', 'like', "%{$texto}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('certificados.admin.index', compact('certificados'));
    }

    public function create(Request $request)
    {
        return view('certificados.admin.create', [
            'sociosActivos' => Socio::where('estado', 'activo')->orderBy('nombre_completo')->get(),
            'socioSel' => $request->socio ? Socio::where('estado', 'activo')->find($request->socio) : null,
            'aporteSugerido' => config('jjvv.aporte_residencia'),
        ]);
    }

    /** Emisión directa en ventanilla (trámite presencial). */
    public function store(Request $request)
    {
        $request->merge(['rut_solicitante' => RutChileno::normalizar($request->rut_solicitante)]);

        $datos = $request->validate([
            'tipo' => ['required', Rule::in(array_keys(Certificado::TIPOS))],
            'socio_id' => ['nullable', 'exists:socios,id'],
            ...$this->reglasComunes($request->tipo),
        ]);

        if ($datos['tipo'] === 'socio') {
            $socio = isset($datos['socio_id']) ? Socio::find($datos['socio_id']) : null;
            if (! $socio || $socio->estado !== 'activo') {
                return back()->withInput()->withErrors([
                    'socio_id' => 'Para un certificado de socio debes elegir un socio activo.',
                ]);
            }
        }

        $certificado = Certificado::create([
            ...$datos,
            'documentos_verificados' => $request->boolean('documentos_verificados'),
        ]);
        $certificado->emitir($request->user());

        return redirect()->route('admin.certificados.show', $certificado)->with('status', 'certificado-emitido');
    }

    public function show(Certificado $certificado)
    {
        $certificado->load(['socio', 'solicitante', 'otorgante']);

        return view('certificados.admin.show', [
            'certificado' => $certificado,
            'aporteSugerido' => config('jjvv.aporte_residencia'),
        ]);
    }

    /** Valida manualmente una solicitud y la emite (RFU-10). */
    public function emitir(Request $request, Certificado $certificado)
    {
        abort_unless($certificado->estado === 'solicitado', 422, 'Solo se pueden emitir solicitudes pendientes.');

        $request->merge(['rut_solicitante' => RutChileno::normalizar($request->rut_solicitante)]);

        $datos = $request->validate($this->reglasComunes($certificado->tipo));

        $certificado->fill([
            ...$datos,
            'documentos_verificados' => $request->boolean('documentos_verificados'),
        ])->save();

        $certificado->emitir($request->user());

        return redirect()->route('admin.certificados.show', $certificado)->with('status', 'certificado-emitido');
    }

    public function rechazar(Request $request, Certificado $certificado)
    {
        abort_unless($certificado->estado === 'solicitado', 422, 'Solo se pueden rechazar solicitudes pendientes.');

        $datos = $request->validate(['observaciones' => ['required', 'string', 'max:500']]);

        $certificado->forceFill(['estado' => 'rechazado', 'observaciones' => $datos['observaciones']])->save();

        return redirect()->route('admin.certificados.show', $certificado)->with('status', 'certificado-rechazado');
    }

    public function anular(Request $request, Certificado $certificado)
    {
        abort_unless($certificado->estado === 'emitido', 422, 'Solo se pueden anular certificados emitidos.');

        $datos = $request->validate(['observaciones' => ['required', 'string', 'max:500']]);

        $certificado->forceFill(['estado' => 'anulado', 'observaciones' => $datos['observaciones']])->save();

        return redirect()->route('admin.certificados.show', $certificado)->with('status', 'certificado-anulado');
    }

    /** PDF para revisión (borrador con marca de agua) o definitivo si ya fue emitido (RFS-01). */
    public function pdf(Certificado $certificado)
    {
        return $certificado->toPdf()->stream($certificado->nombreArchivoPdf());
    }

    private function reglasComunes(?string $tipo): array
    {
        return [
            'nombre_solicitante' => ['required', 'string', 'max:255'],
            'rut_solicitante' => ['required', 'string', 'max:12', new RutChileno],
            'direccion' => ['required', 'string', 'max:255'],
            'dirigido_a' => ['required', 'string', 'max:255'],
            'aporte' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            // RO-02: para residencia se exige ver físicamente carnet y boleta.
            'documentos_verificados' => $tipo === 'residencia' ? ['accepted'] : ['nullable'],
        ];
    }
}
