<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Rules\RutChileno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SocioController extends Controller
{
    // ---------- Vecino: solicitar inscripción ----------

    public function create(Request $request)
    {
        return view('socios.create', ['socio' => $request->user()->socio]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->socio, 403, 'Ya tienes una solicitud registrada.');

        $request->merge(['rut' => RutChileno::normalizar($request->rut)]);

        $datos = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:12', new RutChileno, 'unique:socios,rut'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'residente_desde' => $this->reglaResidencia(),
            'carnet' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'boleta' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ], $this->mensajes());

        $request->user()->socio()->create([
            'nombre_completo' => $datos['nombre_completo'],
            'rut' => $datos['rut'],
            'email' => $request->user()->email,
            'direccion' => $datos['direccion'],
            'telefono' => $datos['telefono'],
            'residente_desde' => $datos['residente_desde'],
            'carnet_path' => $request->file('carnet')->store('socios/carnets'),
            'boleta_path' => $request->file('boleta')->store('socios/boletas'),
        ]);

        return redirect()->route('socios.create')->with('status', 'solicitud-enviada');
    }

    // ---------- Dirigentes: revisar solicitudes ----------

    public function index(Request $request)
    {
        $socios = Socio::query()
            ->when($request->estado, fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->buscar, function ($q, $texto) {
                $q->where(function ($q) use ($texto) {
                    $q->where('nombre_completo', 'like', "%{$texto}%")
                        ->orWhere('rut', 'like', "%{$texto}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('socios.index', compact('socios'));
    }

    public function show(Socio $socio)
    {
        return view('socios.show', compact('socio'));
    }

    public function update(Request $request, Socio $socio)
    {
        $datos = $request->validate([
            'estado' => ['required', 'in:por_firmar,activo,rechazado'],
            'observaciones' => ['required_if:estado,rechazado', 'nullable', 'string', 'max:500'],
        ]);

        if ($datos['estado'] === 'activo' && ! $socio->fecha_ingreso) {
            $datos['fecha_ingreso'] = now()->toDateString();
        }

        $socio->update($datos + ['revisado_por' => $request->user()->id]);

        return redirect()->route('socios.show', $socio)->with('status', 'socio-actualizado');
    }

    public function documento(Socio $socio, string $tipo)
    {
        abort_unless(in_array($tipo, ['carnet', 'boleta']), 404);

        $ruta = $tipo === 'carnet' ? $socio->carnet_path : $socio->boleta_path;
        abort_unless($ruta && Storage::exists($ruta), 404);

        return Storage::response($ruta);
    }

    // ---------- Dirigentes: registrar y editar socios (RFU-06) ----------

    /** Inscripción en ventanilla: la persona está presente y los documentos se ven en persona. */
    public function crear()
    {
        return view('socios.crear');
    }

    public function guardar(Request $request)
    {
        $request->merge(['rut' => RutChileno::normalizar($request->rut)]);

        $datos = $request->validate([
            ...$this->reglasDatos(),
            'residente_desde' => $this->reglaResidencia(),
            'estado' => ['required', Rule::in(['por_firmar', 'activo'])],
            'fecha_ingreso' => ['nullable', 'required_if:estado,activo', 'date', 'before_or_equal:today'],
            'carnet' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'boleta' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ], $this->mensajes());

        $socio = Socio::create([
            'nombre_completo' => $datos['nombre_completo'],
            'rut' => $datos['rut'],
            'email' => $datos['email'] ?? null,
            'direccion' => $datos['direccion'],
            'telefono' => $datos['telefono'],
            'residente_desde' => $datos['residente_desde'],
            'estado' => $datos['estado'],
            'fecha_ingreso' => $datos['estado'] === 'activo' ? $datos['fecha_ingreso'] : null,
            'carnet_path' => $request->file('carnet')?->store('socios/carnets'),
            'boleta_path' => $request->file('boleta')?->store('socios/boletas'),
            'revisado_por' => $request->user()->id,
        ]);

        return redirect()->route('socios.show', $socio)->with('status', 'socio-creado');
    }

    public function editar(Socio $socio)
    {
        return view('socios.editar', compact('socio'));
    }

    public function actualizar(Request $request, Socio $socio)
    {
        $request->merge(['rut' => RutChileno::normalizar($request->rut)]);

        // La regla de 6 meses solo se exige si el dirigente cambia la fecha de residencia.
        $residencia = $request->residente_desde === $socio->residente_desde->toDateString()
            ? ['required', 'date', 'before_or_equal:today']
            : $this->reglaResidencia();

        $datos = $request->validate([
            ...$this->reglasDatos($socio),
            'residente_desde' => $residencia,
            'carnet' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'boleta' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ], $this->mensajes());

        $cambios = collect($datos)->except(['carnet', 'boleta'])->all();

        foreach (['carnet' => 'socios/carnets', 'boleta' => 'socios/boletas'] as $campo => $carpeta) {
            if ($request->hasFile($campo)) {
                $anterior = $socio->{$campo.'_path'};
                $cambios[$campo.'_path'] = $request->file($campo)->store($carpeta);

                if ($anterior) {
                    Storage::delete($anterior);
                }
            }
        }

        $socio->update($cambios);

        return redirect()->route('socios.show', $socio)->with('status', 'socio-editado');
    }

    // ---------- Reglas compartidas ----------

    private function reglasDatos(?Socio $socio = null): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:12', new RutChileno, Rule::unique('socios', 'rut')->ignore($socio?->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
        ];
    }

    private function reglaResidencia(): array
    {
        return ['required', 'date', 'before_or_equal:'.now()->subMonths(6)->toDateString()];
    }

    private function mensajes(): array
    {
        return ['residente_desde.before_or_equal' => 'Debe residir en el sector hace al menos 6 meses.'];
    }
}
