<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Presidente y tesorero: ingresos, cuotas y anulaciones (RFS-58, 59, 60, 64, 67, 68). */
class IngresoController extends Controller
{
    public function index()
    {
        return view('finanzas.ingresos.index', [
            'ingresos' => Ingreso::with('registrador')->latest('fecha_pago')->latest('id')->paginate(20),
            'saldo' => Ingreso::saldoCaja(),
        ]);
    }

    public function create()
    {
        return view('finanzas.ingresos.create', [
            'conceptos' => Ingreso::CONCEPTOS,
            'tiposCuota' => Ingreso::TIPOS_CUOTA,
            'medios' => Ingreso::MEDIOS,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'concepto' => ['required', Rule::in(array_keys(Ingreso::CONCEPTOS))],
            'tipo_cuota' => ['required_if:concepto,cuota_socios', Rule::in(array_keys(Ingreso::TIPOS_CUOTA))],
            'fecha_pago' => ['required', 'date', 'before_or_equal:today'],
            'monto' => ['required', 'integer', 'min:1', 'max:100000000'],
            'medio_pago' => ['required', Rule::in(array_keys(Ingreso::MEDIOS))],
            'talonario' => ['required_if:medio_pago,efectivo', 'string', 'max:30'],
            'numero_transaccion' => ['required_if:medio_pago,transferencia', 'string', 'max:50'],
            'observaciones' => ['required_if:concepto,otros', 'string', 'max:500'],
        ]);

        // El tipo de cuota solo aplica a cuotas de socios.
        if ($datos['concepto'] !== 'cuota_socios') {
            $datos['tipo_cuota'] = null;
        }

        $datos['registrado_por'] = $request->user()->id;

        Ingreso::create($datos);

        return redirect()->route('ingresos.index')->with('status', 'ingreso-registrado');
    }

    public function anular(Request $request, Ingreso $ingreso)
    {
        $datos = $request->validate([
            'motivo_anulacion' => ['required', 'string', 'max:500'],
        ]);

        if ($ingreso->estaAnulado()) {
            return back()->withErrors(['ingreso' => 'Este ingreso ya está anulado.']);
        }

        $ingreso->anular($datos['motivo_anulacion'], $request->user());

        return redirect()->route('ingresos.index')->with('status', 'ingreso-anulado');
    }
}