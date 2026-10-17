<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Ingreso;
use Illuminate\Http\Request;

/** Presidente y tesorero: devolución de una cuota con motivo obligatorio (RFS-66). */
class DevolucionController extends Controller
{
    public function store(Request $request, Ingreso $ingreso)
    {
        $datos = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        if (! $ingreso->puedeDevolverse()) {
            return back()->withErrors(['devolucion' => 'Este ingreso no se puede devolver.']);
        }

        Devolucion::create([
            'ingreso_id' => $ingreso->id,
            'fecha_devolucion' => now()->toDateString(),
            'monto' => $ingreso->monto,            // devolución total, una sola vez
            'motivo' => $datos['motivo'],
            'registrado_por' => $request->user()->id,
        ]);

        return redirect()->route('ingresos.index')->with('status', 'devolucion-registrada');
    }
}