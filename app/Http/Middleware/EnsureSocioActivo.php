<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Permite el paso solo a usuarios con ficha de socio en estado activo (sin importar su rol). */
class EnsureSocioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            return redirect('login');
        }

        if (! $usuario->esSocioActivo()) {
            abort(403, 'Esta sección es solo para socios con inscripción activa.');
        }

        return $next($request);
    }
}