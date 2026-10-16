<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Socio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'modelo' => ['nullable', Rule::in(array_keys(Auditoria::MODELOS))],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'autor' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $auditorias = Auditoria::with('usuario')
            ->when($request->modelo, fn ($q, $m) => $q->whereIn('auditable_type', (array) Auditoria::MODELOS[$m]))
            ->when($request->desde, fn ($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->hasta, fn ($q, $f) => $q->whereDate('created_at', '<=', $f))
            ->when($request->autor, fn ($q, $a) => $q->where('user_id', $a))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('auditoria.index', [
            'auditorias' => $auditorias,
            'autores' => User::orderBy('name')->get(),
            // Solo vista, sin filtro (decisión del proyecto).
            'bajas' => Socio::where('estado', 'inactivo')->orderByDesc('fecha_baja')->get(),
        ]);
    }
}