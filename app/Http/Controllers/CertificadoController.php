<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Rules\RutChileno;
use App\Models\DescargaCertificado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Vecino: solicita certificados y descarga los que ya fueron emitidos. */
class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        return view('certificados.index', [
            'socio' => $usuario->socio,
            'certificados' => Certificado::where('user_id', $usuario->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $usuario = $request->user();
        $socio = $usuario->socio;

        $pendientes = Certificado::where('user_id', $usuario->id)->where('estado', 'solicitado')->count();
        if ($pendientes >= 3) {
            return back()->withInput()->withErrors([
                'tipo' => 'Ya tienes 3 solicitudes pendientes. Espera a que la directiva las revise.',
            ]);
        }

        $tipo = $request->validate([
            'tipo' => ['required', Rule::in(array_keys(Certificado::TIPOS))],
        ])['tipo'];

        if ($tipo === 'socio') {
            if (! $socio || $socio->estado !== 'activo') {
                return back()->withInput()->withErrors([
                    'tipo' => 'El certificado de socio solo está disponible para socios activos.',
                ]);
            }

            $datos = $request->validate(['dirigido_a' => ['required', 'string', 'max:255']]);
            $datos += [
                'nombre_solicitante' => $socio->nombre_completo,
                'rut_solicitante' => $socio->rut,
                'direccion' => $socio->direccion,
                'socio_id' => $socio->id,
            ];
        } else {
            // Residencia: pago obligatorio por transferencia con comprobante (RFS-19).
            $request->merge(['rut_solicitante' => RutChileno::normalizar($request->rut_solicitante)]);

            $datos = $request->validate([
                'nombre_solicitante' => ['required', 'string', 'max:255'],
                'rut_solicitante' => ['required', 'string', 'max:12', new RutChileno],
                'direccion' => ['required', 'string', 'max:255'],
                'dirigido_a' => ['required', 'string', 'max:255'],
                'numero_transaccion' => ['required', 'string', 'max:50'],
                'comprobante' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);

            $datos['comprobante_path'] = $request->file('comprobante')->store('comprobantes', 'local');
            unset($datos['comprobante']);
            $datos['socio_id'] = $socio?->id;
        }

        Certificado::create($datos + ['tipo' => $tipo, 'user_id' => $usuario->id]);

        return redirect()->route('certificados.index')->with('status', 'certificado-solicitado');
    }

    public function descargar(Request $request, Certificado $certificado)
    {
        abort_unless(
            $certificado->user_id === $request->user()->id && $certificado->estado === 'emitido',
            404
        );

        if ($certificado->tipo === 'residencia') {
            abort_unless(
                $certificado->puedeDescargarse(),
                410,
                'El plazo para descargar este certificado venció. Puedes retirarlo en la sede.'
            );

            // Solo se registran las descargas del certificado de residencia (decisión del proyecto).
            DescargaCertificado::create([
                'certificado_id' => $certificado->id,
                'user_id' => $request->user()->id,
                'ip' => $request->ip(),
                'descargado_en' => now(),
            ]);
        }

        return $certificado->toPdf()->stream($certificado->nombreArchivoPdf());
    }
}
