<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Presidente y tesorero: registro manual de ingresos (RFS-58, 59, 64). */
class IngresoController extends Controller
{
    public function index()
    {
        return view('finanzas.ingresos.index', [
            'ingresos' => Ingreso::with('registrador')->latest('fecha_pago')->latest('id')->paginate(20),
            'total' => Ingreso::sum('monto'),
        ]);
    }

    public function create()
    {
        return view('finanzas.ingresos.create', [
            'conceptos' => Ingreso::CONCEPTOS,
            'medios' => Ingreso::MEDIOS,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'concepto' => ['required', Rule::in(array_keys(Ingreso::CONCEPTOS))],
            'fecha_pago' => ['required', 'date', 'before_or_equal:today'],
            'monto' => ['required', 'integer', 'min:1', 'max:100000000'],
            'medio_pago' => ['required', Rule::in(array_keys(Ingreso::MEDIOS))],
            'talonario' => ['required_if:medio_pago,efectivo', 'string', 'max:30'],
            'numero_transaccion' => ['required_if:medio_pago,transferencia', 'string', 'max:50'],
            'observaciones' => ['required_if:concepto,otros', 'string', 'max:500'],
        ]);

        $datos['registrado_por'] = $request->user()->id;

        Ingreso::create($datos);

        return redirect()->route('ingresos.index')->with('status', 'ingreso-registrado');
    }
}