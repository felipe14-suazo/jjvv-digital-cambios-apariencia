<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Socio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Grupos y organizaciones de la sede (RFU-29, RFU-30, RFS-38 a RFS-40). */
class GrupoController extends Controller
{
    public function index()
    {
        return view('grupos.index', [
            'grupos' => Grupo::with('representante')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:grupos,nombre'],
            'horarios_uso' => ['nullable', 'string', 'max:1000'],
        ]);

        $grupo = Grupo::create($datos);

        return redirect()->route('grupos.show', $grupo)->with('status', 'grupo-creado');
    }

    public function show(Grupo $grupo)
    {
        return view('grupos.show', [
            'grupo' => $grupo->load('representante'),
            'socios' => Socio::where('estado', 'activo')
                ->whereNotNull('user_id')
                ->orderBy('nombre_completo')
                ->get(),
        ]);
    }

    public function update(Request $request, Grupo $grupo)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('grupos', 'nombre')->ignore($grupo->id)],
            'horarios_uso' => ['nullable', 'string', 'max:1000'],
        ]);

        $grupo->update($datos);

        return redirect()->route('grupos.show', $grupo)->with('status', 'grupo-actualizado');
    }

    /** RFS-39 y RFS-40: el representante debe ser socio activo con cuenta; sus datos se copian al grupo. */
    public function asignarRepresentante(Request $request, Grupo $grupo)
    {
        $datos = $request->validate([
            'socio_id' => ['required', 'exists:socios,id'],
        ]);

        $socio = Socio::findOrFail($datos['socio_id']);

        if ($socio->estado !== 'activo' || $socio->user_id === null) {
            return back()->withErrors([
                'socio_id' => 'El representante debe ser socio activo con cuenta de usuario.',
            ]);
        }

        $grupo->update([
            'representante_id' => $socio->id,
            'contacto_nombre' => $socio->nombre_completo,
            'contacto_correo' => $socio->email,
            'contacto_telefono' => $socio->telefono,
            'contacto_direccion' => $socio->direccion,
        ]);

        return redirect()->route('grupos.show', $grupo)->with('status', 'representante-asignado');
    }

    /** RFS-40: editar el contacto del grupo sin alterar la ficha del representante. */
    public function actualizarContacto(Request $request, Grupo $grupo)
    {
        $datos = $request->validate([
            'contacto_nombre' => ['nullable', 'string', 'max:255'],
            'contacto_correo' => ['nullable', 'email', 'max:255'],
            'contacto_telefono' => ['nullable', 'string', 'max:20'],
            'contacto_direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $grupo->update($datos);

        return redirect()->route('grupos.show', $grupo)->with('status', 'contacto-actualizado');
    }
}