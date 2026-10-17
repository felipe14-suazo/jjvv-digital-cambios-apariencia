@php
    $usuario = Auth::user();
    $esDirectivo = $usuario->hasRole('presidente', 'secretario');
    $esSocio = $usuario->esSocioActivo();

    $accesos = [];

    if ($usuario->hasRole('vecino')) {
        $accesos[] = ['Mi solicitud de socio', 'Envía o revisa tu solicitud de inscripción.', route('socios.create')];
        if (! $esSocio) {
            $accesos[] = ['Hora para firmar el libro', 'Reserva o cancela tu cita presencial de firma.', route('citas.index')];
        }
    }
    if ($esSocio) {
        $accesos[] = ['Mis certificados', 'Solicita y descarga tus certificados.', route('certificados.index')];
    }
    if ($esDirectivo || $esSocio) {
        $accesos[] = ['Calendario', 'Disponibilidad y reservas de la sede.', route('calendario.index')];
    }
    if ($esDirectivo) {
        $accesos[] = ['Socios', 'Solicitudes, registro en ventanilla y bajas.', route('socios.index')];
        $accesos[] = ['Agenda de firmas', 'Horas disponibles para firmar el libro de socios.', route('agenda.index')];
        $accesos[] = ['Certificados', 'Emisión, rechazo, anulación y retiro.', route('admin.certificados.index')];
        $accesos[] = ['Grupos', 'Organizaciones, representantes y horarios fijos.', route('grupos.index')];
        $accesos[] = ['Parámetros', 'Horario de la sede, vigencias, plazos y valores.', route('parametros.index')];
    }
    if ($usuario->hasRole('presidente', 'tesorero')) {
        $accesos[] = ['Ingresos', 'Cuotas, aportes y pagos de certificados.', route('ingresos.index')];
        $accesos[] = ['Egresos', 'Pagos de servicios y mantención.', route('egresos.index')];
    }
    if ($usuario->hasRole('presidente')) {
        $accesos[] = ['Auditoría', 'Quién cambió qué y cuándo.', route('auditoria.index')];
    }
    if ($usuario->hasRole('admin')) {
        $accesos[] = ['Usuarios', 'Cuentas y roles del sistema.', route('usuarios.index')];
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Panel</h2>
            <x-rol-usuario :rol="$usuario->role" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Saludo --}}
            <div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    Hola, {{ \Illuminate\Support\Str::before($usuario->name, ' ') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ config('app.name') }} · {{ ucfirst(now()->translatedFormat('l j \d\e F \d\e Y')) }}
                </p>
            </div>

            {{-- Estado de inscripción (vecinos) --}}
            @if ($usuario->hasRole('vecino'))
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado de tu inscripción como socio</p>
                    <div class="mt-2">
                        @if ($usuario->socio)
                            <x-estado-socio :estado="$usuario->socio->estado" />
                        @else
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aún no has enviado tu solicitud.</span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Resumen (dirigentes) --}}
            @if (! empty($resumen))
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($resumen as $dato)
                        <a href="{{ $dato['ruta'] }}"
                           class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:ring-indigo-400 dark:bg-gray-800 dark:ring-gray-700 dark:hover:ring-indigo-500">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $dato['etiqueta'] }}</p>
                            <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ $dato['valor'] }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Accesos rápidos --}}
            <div>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Accesos rápidos</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($accesos as [$titulo, $descripcion, $url])
                        <a href="{{ $url }}"
                           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:ring-indigo-400 dark:bg-gray-800 dark:ring-gray-700 dark:hover:ring-indigo-500">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $titulo }}</h4>
                                <span class="text-gray-400 transition group-hover:translate-x-0.5 group-hover:text-indigo-500">→</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $descripcion }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>