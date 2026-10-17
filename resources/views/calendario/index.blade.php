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

            @php
                // Une bloques consecutivos iguales de un mismo día en una sola celda (rowspan).
                $filas = [];     // [fecha][hora de inicio] => cantidad de bloques
                $cubiertas = []; // [fecha][hora] => true si la celda de arriba ya la cubre
                foreach ($dias as $dia) {
                    $f = $dia->toDateString();
                    $inicio = null;
                    foreach ($horas as $hora) {
                        $celda = $celdas[$f][$hora] ?? null;
                        if ($celda && $inicio && $celda == $celdas[$f][$inicio]) {
                            $filas[$f][$inicio]++;
                            $cubiertas[$f][$hora] = true;
                        } else {
                            $inicio = $celda ? $hora : null;
                            if ($celda) {
                                $filas[$f][$hora] = 1;
                            }
                        }
                    }
                }
            @endphp

            {{-- Navegación entre semanas --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 sm:px-0">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Semana del {{ $dias->first()->translatedFormat('j \d\e F') }}
                    al {{ $dias->last()->translatedFormat('j \d\e F \d\e Y') }}
                </h3>
                <div class="inline-flex divide-x divide-gray-300 overflow-hidden rounded-lg text-sm shadow-sm ring-1 ring-gray-300 dark:divide-gray-600 dark:ring-gray-600">
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->subWeek()->toDateString()]) }}"
                       class="bg-white px-3 py-2 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">← Anterior</a>
                    <a href="{{ route('calendario.index') }}"
                       class="bg-white px-3 py-2 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Esta semana</a>
                    <a href="{{ route('calendario.index', ['semana' => $lunes->copy()->addWeek()->toDateString()]) }}"
                       class="bg-white px-3 py-2 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Siguiente →</a>
                </div>
            </div>

            {{-- Leyenda --}}
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 px-4 text-xs font-medium text-gray-600 dark:text-gray-300 sm:px-0">
                @if ($esDirigente)
                    <div class="flex items-center gap-2">
                        <div class="h-3.5 w-3.5 rounded bg-indigo-600 dark:bg-indigo-500"></div>
                        <span>Reserva</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3.5 w-3.5 rounded bg-amber-500 dark:bg-amber-600"></div>
                        <span>Horario fijo (sin generar)</span>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <div class="h-3.5 w-3.5 rounded bg-gray-500 dark:bg-gray-600"></div>
                        <span>Ocupado</span>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <div class="h-3.5 w-3.5 rounded border border-gray-300 dark:border-gray-600"></div>
                    <span>Libre</span>
                </div>
            </div>

            {{-- Grilla semanal --}}
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700 sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[56rem] table-fixed border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                                <th class="w-20"></th>
                                @foreach ($dias as $dia)
                                    <th class="border-l border-gray-200 dark:border-gray-700 px-3 py-3 text-center {{ $dia->isToday() ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : '' }}">
                                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $dia->translatedFormat('D') }}</div>
                                        <div class="mt-1 text-lg font-bold {{ $dia->isToday() ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $dia->format('j') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($horas as $hora)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="h-12 pr-4 pt-2 text-right align-top text-xs font-medium text-gray-500 dark:text-gray-400">{{ $hora }}</td>
                                    @foreach ($dias as $dia)
                                        @php $f = $dia->toDateString(); @endphp
                                        @continue(isset($cubiertas[$f][$hora]))
                                        @php $celda = $celdas[$f][$hora] ?? null; @endphp

                                        @if (! $celda)
                                            <td class="h-12 border-l border-gray-200 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/40 {{ $dia->isToday() ? 'bg-indigo-50/50 dark:bg-indigo-500/5' : '' }}"></td>
                                        @else
                                            @php
                                                $clase = ! $esDirigente
                                                    ? 'border-gray-500 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
                                                    : ($celda['tipo'] === 'fijo'
                                                        ? 'border-amber-500 bg-amber-50 text-amber-900 dark:bg-amber-900/40 dark:text-amber-100'
                                                        : 'border-indigo-500 bg-indigo-100 text-indigo-900 dark:bg-indigo-900/50 dark:text-indigo-100');
                                            @endphp
                                            <td rowspan="{{ $filas[$f][$hora] }}"
                                                class="border-l-4 px-3 py-2 align-top text-xs font-semibold shadow-sm transition-all hover:opacity-90 {{ $clase }}">
                                                {{ $celda['texto'] }}
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($esDirigente)
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