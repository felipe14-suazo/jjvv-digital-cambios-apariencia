@php
    $claseSelect = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Calendario de la sede</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'reserva-creada')
                <div class="p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm">
                    Reserva guardada.
                </div>
            @endif

            {{-- Navegación entre semanas --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <h3 class="font-medium text-gray-900 dark:text-gray-100">
                    Semana del {{ $dias->first()->translatedFormat('j \d\e F') }}
                    al {{ $dias->last()->translatedFormat('j \d\e F \d\e Y') }}
                </h3>
                <div class="flex gap-4 text-sm">
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->subWeek()->toDateString()]) }}"
                       class="underline text-gray-600 dark:text-gray-400">← Anterior</a>
                    <a href="{{ route('calendario.index') }}"
                       class="underline text-gray-600 dark:text-gray-400">Esta semana</a>
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->addWeek()->toDateString()]) }}"
                       class="underline text-gray-600 dark:text-gray-400">Siguiente →</a>
                </div>
            </div>

            {{-- Grilla semanal --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-3 py-2 w-16"></th>
                            @foreach ($dias as $dia)
                                <th class="px-3 py-2 font-medium text-left {{ $dia->isToday() ? 'text-indigo-600 dark:text-indigo-400' : '' }}">
                                    {{ ucfirst($dia->translatedFormat('D j')) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($horas as $hora)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $hora }}</td>
                                @foreach ($dias as $dia)
                                    @php
                                        $celda = $celdas[$dia->toDateString()][$hora] ?? null;
                                        if (! $celda) {
                                            $clase = 'text-gray-400 dark:text-gray-500';
                                        } elseif (! $esDirigente) {
                                            $clase = 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200';
                                        } elseif ($celda['tipo'] === 'fijo') {
                                            $clase = 'bg-amber-100 dark:bg-amber-900/40 text-amber-900 dark:text-amber-100';
                                        } else {
                                            $clase = 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-900 dark:text-indigo-100';
                                        }
                                    @endphp
                                    <td class="px-2 py-2 border-l border-gray-200 dark:border-gray-700 {{ $clase }}">
                                        {{ $celda['texto'] ?? 'Libre' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($esDirigente)
                <p class="px-4 sm:px-0 text-xs text-gray-500 dark:text-gray-400">
                    Azul: reserva. Ámbar: horario fijo de grupo aún sin generar para esa semana.
                </p>

                {{-- Ingreso de reservas --}}
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                     x-data="{ tipo: '{{ old('tipo', 'grupo') }}' }">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ingresar reserva</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Una reserva ocupa uno o más bloques consecutivos dentro de un mismo día.
                    </p>

                    @error('reserva')
                        <div class="mt-4 p-4 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 rounded-lg text-sm">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('reservas.store') }}" class="mt-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
                        @csrf

                        <div>
                            <x-input-label for="fecha" value="Fecha" />
                            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                                :value="old('fecha')" min="{{ now()->toDateString() }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha')" />
                        </div>

                        <div>
                            <x-input-label for="hora_inicio" value="Desde" />
                            <select id="hora_inicio" name="hora_inicio" class="{{ $claseSelect }}" required>
                                @foreach ($horas as $hora)
                                    <option value="{{ $hora }}" @selected(old('hora_inicio') === $hora)>{{ $hora }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('hora_inicio')" />
                        </div>

                        <div>
                            <x-input-label for="hora_fin" value="Hasta" />
                            <select id="hora_fin" name="hora_fin" class="{{ $claseSelect }}" required>
                                @foreach ($horasFin as $hora)
                                    <option value="{{ $hora }}" @selected(old('hora_fin') === $hora)>{{ $hora }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('hora_fin')" />
                        </div>

                        <div>
                            <x-input-label for="tipo" value="Tipo" />
                            <select id="tipo" name="tipo" x-model="tipo" class="{{ $claseSelect }}">
                                <option value="grupo">Uso de grupo</option>
                                <option value="actividad">Actividad de la junta</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                        </div>

                        <div class="sm:col-span-2" x-show="tipo === 'grupo'">
                            <x-input-label for="grupo_id" value="Grupo" />
                            <select id="grupo_id" name="grupo_id" class="{{ $claseSelect }}">
                                <option value="">Elegir grupo</option>
                                @foreach ($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" @selected((string) old('grupo_id') === (string) $grupo->id)>
                                        {{ $grupo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($grupos->isEmpty())
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    No hay grupos con representante vigente.
                                </p>
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('grupo_id')" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-input-label for="motivo" value="Motivo" />
                            <x-text-input id="motivo" name="motivo" type="text" class="mt-1 block w-full"
                                :value="old('motivo')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('motivo')" />
                        </div>

                        <div class="sm:col-span-4">
                            <x-primary-button>Guardar reserva</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>