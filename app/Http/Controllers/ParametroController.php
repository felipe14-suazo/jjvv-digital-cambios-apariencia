<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;

/** Presidente y secretario: edición de los parámetros del sistema (RFS-88). */
class ParametroController extends Controller
{
    public function index()
    {
        return view('parametros.index', [
            'parametros' => Parametro::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $reglas = [
            'sede_hora_inicio' => ['required', 'date_format:H:i'],
            'sede_hora_fin' => ['required', 'date_format:H:i'],
            'sede_duracion_bloque_min' => ['required', 'integer', 'in:30,60'],
            'certificado_vigencia_dias' => ['required', 'integer', 'min:1', 'max:365'],
            'certificado_plazo_descarga_dias' => ['required', 'integer', 'min:1', 'max:30'],
            'certificado_residencia_valor' => ['required', 'integer', 'min:0'],
            'arriendo_plazo_confirmacion_horas' => ['required', 'integer', 'min:1', 'max:336'],
            'arriendo_valor' => ['required', 'integer', 'min:0'],
            'arriendo_garantia' => ['required', 'integer', 'min:0'],
        ];

        $datos = $request->validate(
            collect($reglas)->mapWithKeys(fn ($regla, $clave) => ["valores.$clave" => $regla])->all()
        );

        $valores = $datos['valores'];

        if ($valores['sede_hora_fin'] <= $valores['sede_hora_inicio']) {
            return back()->withInput()->withErrors([
                'valores.sede_hora_fin' => 'La hora de cierre debe ser posterior a la de apertura.',
            ]);
        }

        foreach ($valores as $clave => $valor) {
            $parametro = Parametro::where('clave', $clave)->firstOrFail();

            // Solo se guarda si cambió: así cada cambio queda en la auditoría.
            if ((string) $parametro->valor !== (string) $valor) {
                $parametro->forceFill([
                    'valor' => (string) $valor,
                    'actualizado_por' => $request->user()->id,
                ])->save();
            }
        }

        return redirect()->route('parametros.index')->with('status', 'parametros-guardados');
    }
}