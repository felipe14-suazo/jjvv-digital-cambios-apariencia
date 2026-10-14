<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditorias = Auditoria::with('usuario')
            ->when($request->modelo, fn ($q, $m) => $q->where('auditable_type', Auditoria::MODELOS[$m] ?? '__ninguno__'))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('auditoria.index', compact('auditorias'));
    }
}
