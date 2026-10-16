<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\HorarioFijo;
use App\Models\Parametro;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Calendario de la sede e ingreso de reservas (RFU-31 a RFU-33, RFS-43, RFS-44, RFS-55, RFS-56). */
class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $esDirigente = $usuario->hasRole('presidente', 'secretario');

        abort_unless(
            $esDirigente || $usuario->esSocioActivo(),
            403,
            'El calendario es solo para socios con inscripción activa.'
        );

        $lunes = $this->lunesDe($request->query('semana'));
        $dias = collect(range(0, 6))->map(fn ($i) => $lunes->copy()->addDays($i));
        $bloque = Parametro::entero('sede_duracion_bloque_min');
        ['inicios' => $horas, 'fines' => $horasFin] = Reserva::bloquesDeLaSede();

        $celdas = []; // [fecha][hora] => ['tipo' => 'reserva'|'fijo', 'texto' => ...]

        $reservas = Reserva::with('grupo')
            ->whereIn('estado', Reserva::ESTADOS_OCUPAN)
            ->where('inicio', '>=', $lunes)
            ->where('inicio', '<', $lunes->copy()->addDays(7))
            ->get();

        foreach ($reservas as $r) {
            for ($c = $r->inicio->copy(); $c->lt($r->fin); $c->addMinutes($bloque)) {
                $celdas[$c->toDateString()][$c->format('H:i')] = [
                    'tipo' => 'reserva',
                    'texto' => $esDirigente
                        ? trim(($r->grupo ? $r->grupo->nombre.' · ' : '').$r->motivo)
                        : 'Ocupado',
                ];
            }
        }

        // Los horarios fijos ocupan el espacio aunque su mes aún no se haya generado (RFS-48).
        foreach (HorarioFijo::with('grupo')->get() as $hf) {
            $fecha = $lunes->copy()->addDays($hf->dia_semana - 1)->toDateString();
            $fin = Carbon::parse("{$fecha} {$hf->hora_fin}");

            for ($c = Carbon::parse("{$fecha} {$hf->hora_inicio}"); $c->lt($fin); $c->addMinutes($bloque)) {
                $celdas[$fecha][$c->format('H:i')] ??= [
                    'tipo' => 'fijo',
                    'texto' => $esDirigente ? $hf->grupo->nombre.' · horario fijo' : 'Ocupado',
                ];
            }
        }

        // Solo grupos con representante vigente pueden tener reservas nuevas (RFS-42).
        $grupos = $esDirigente
            ? Grupo::with('representante')->orderBy('nombre')->get()
                ->filter(fn ($g) => $g->tieneRepresentanteVigente())->values()
            : collect();

        return view('calendario.index', compact(
            'esDirigente', 'lunes', 'dias', 'horas', 'horasFin', 'celdas', 'grupos'
        ));
    }

    /** RFU-33: el presidente o el secretario ingresa una reserva (el arriendo llega en el 3b). */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addMonths(6)->toDateString()],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'tipo' => ['required', 'in:grupo,actividad'],
            'motivo' => ['required', 'string', 'max:255'],
            'grupo_id' => ['nullable', 'required_if:tipo,grupo', 'exists:grupos,id'],
        ], [
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'fecha.before_or_equal' => 'Solo puedes reservar con hasta 6 meses de anticipación.',
            'hora_fin.after' => 'La hora de término debe ser posterior a la de inicio.',
            'grupo_id.required_if' => 'Elige el grupo.',
        ]);

        $inicio = Carbon::parse("{$datos['fecha']} {$datos['hora_inicio']}");
        $fin = Carbon::parse("{$datos['fecha']} {$datos['hora_fin']}");

        if ($inicio->isPast()) {
            return $this->rechazar('No se puede reservar un horario que ya comenzó.');
        }

        $grupoId = null;

        if ($datos['tipo'] === 'grupo') {
            $grupo = Grupo::findOrFail($datos['grupo_id']);

            if (! $grupo->tieneRepresentanteVigente()) {
                return $this->rechazar('El grupo no tiene representante vigente y no puede recibir reservas nuevas.');
            }

            $grupoId = $grupo->id;
        }

        if ($error = Reserva::errorDeConflicto($inicio, $fin)) {
            return $this->rechazar($error);
        }

        Reserva::create([
            'tipo' => $datos['tipo'],
            'estado' => 'confirmada',
            'inicio' => $inicio,
            'fin' => $fin,
            'motivo' => $datos['motivo'],
            'grupo_id' => $grupoId,
            'creado_por' => $request->user()->id,
        ]);

        return redirect()
            ->route('calendario.index', ['semana' => $inicio->copy()->startOfWeek()->toDateString()])
            ->with('status', 'reserva-creada');
    }

    private function rechazar(string $mensaje)
    {
        return back()->withInput()->withErrors(['reserva' => $mensaje]);
    }

    private function lunesDe(?string $semana): Carbon
    {
        try {
            $fecha = $semana ? Carbon::createFromFormat('Y-m-d', $semana) : now();
        } catch (\Throwable) {
            $fecha = now();
        }

        return $fecha->copy()->startOfWeek();
    }
}