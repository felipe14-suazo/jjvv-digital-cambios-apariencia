<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $grupo->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <p class="px-4 sm:px-0 text-green-700 dark:text-green-400">Cambios guardados.</p>
            @endif

            {{-- Datos del grupo --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Datos del grupo</h3>
                <form method="POST" action="{{ route('grupos.update', $grupo) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $grupo->nombre)" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="horarios_uso" value="Horarios de uso (texto libre)" />
                        <textarea id="horarios_uso" name="horarios_uso" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-300">{{ old('horarios_uso', $grupo->horarios_uso) }}</textarea>
                        <x-input-error :messages="$errors->get('horarios_uso')" class="mt-2" />
                    </div>
                    <x-primary-button>Guardar datos</x-primary-button>
                </form>
            </div>

            {{-- Representante --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Representante</h3>
                @unless ($grupo->tieneRepresentanteVigente())
                    <p class="mb-4 text-sm text-red-600 dark:text-red-400">
                        Sin representante vigente. El grupo no puede reservar hasta que se asigne uno.
                    </p>
                @endunless

                <form method="POST" action="{{ route('grupos.representante', $grupo) }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <select name="socio_id" required
                            class="rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Elegir socio activo</option>
                        @foreach ($socios as $socio)
                            <option value="{{ $socio->id }}" @selected($grupo->representante_id === $socio->id)>
                                {{ $socio->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button>Asignar representante</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('socio_id')" class="mt-2" />
            </div>

            {{-- Contacto del grupo (copia editable, RFS-40) --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Datos de contacto</h3>
                <form method="POST" action="{{ route('grupos.contacto', $grupo) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="contacto_nombre" value="Nombre" />
                        <x-text-input id="contacto_nombre" name="contacto_nombre" type="text" class="mt-1 block w-full" :value="old('contacto_nombre', $grupo->contacto_nombre)" />
                        <x-input-error :messages="$errors->get('contacto_nombre')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="contacto_correo" value="Correo" />
                        <x-text-input id="contacto_correo" name="contacto_correo" type="email" class="mt-1 block w-full" :value="old('contacto_correo', $grupo->contacto_correo)" />
                        <x-input-error :messages="$errors->get('contacto_correo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="contacto_telefono" value="Teléfono" />
                        <x-text-input id="contacto_telefono" name="contacto_telefono" type="text" class="mt-1 block w-full" :value="old('contacto_telefono', $grupo->contacto_telefono)" />
                        <x-input-error :messages="$errors->get('contacto_telefono')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="contacto_direccion" value="Dirección" />
                        <x-text-input id="contacto_direccion" name="contacto_direccion" type="text" class="mt-1 block w-full" :value="old('contacto_direccion', $grupo->contacto_direccion)" />
                        <x-input-error :messages="$errors->get('contacto_direccion')" class="mt-2" />
                    </div>
                    <x-primary-button>Guardar contacto</x-primary-button>
                </form>
            </div>

            {{-- Horarios fijos (RFU-34, RFS-47, RFS-49) --}}
            @php
                $claseSelect = 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Horarios fijos</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Reservas que se repiten cada semana. Las reservas de cada mes se generan con el botón de más abajo.
                        Editar o eliminar un horario no cambia las reservas ya generadas.
                    </p>
                </div>

                @error('horario')
                    <div class="p-4 bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 rounded-lg text-sm">
                        {{ $message }}
                    </div>
                @enderror

                @if (session('mensaje'))
                    <div class="p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm">
                        {{ session('mensaje') }}
                    </div>
                @endif

                @if (session('omitidas'))
                    <div class="p-4 bg-amber-100 dark:bg-amber-900/40 text-amber-900 dark:text-amber-100 rounded-lg text-sm">
                        <p class="font-medium">No se generaron estos días:</p>
                        <ul class="mt-1 list-disc ps-5">
                            @foreach (session('omitidas') as $omitida)
                                <li>{{ $omitida }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Horarios existentes --}}
                @forelse ($grupo->horariosFijos as $horario)
                    <div class="flex flex-wrap items-end gap-3">
                        <form method="POST" action="{{ route('grupos.horarios.update', $horario) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            @method('PUT')
                            <select name="dia_semana" class="{{ $claseSelect }}">
                                @foreach (\App\Models\HorarioFijo::DIAS as $numero => $nombre)
                                    <option value="{{ $numero }}" @selected((int) $horario->dia_semana === $numero)>{{ $nombre }}</option>
                                @endforeach
                            </select>
                            <select name="hora_inicio" class="{{ $claseSelect }}">
                                @foreach ($horas as $hora)
                                    <option value="{{ $hora }}" @selected(substr($horario->hora_inicio, 0, 5) === $hora)>{{ $hora }}</option>
                                @endforeach
                            </select>
                            <span class="pb-2 text-gray-500 dark:text-gray-400">a</span>
                            <select name="hora_fin" class="{{ $claseSelect }}">
                                @foreach ($horasFin as $hora)
                                    <option value="{{ $hora }}" @selected(substr($horario->hora_fin, 0, 5) === $hora)>{{ $hora }}</option>
                                @endforeach
                            </select>
                            <x-secondary-button type="submit">Guardar</x-secondary-button>
                        </form>

                        <form method="POST" action="{{ route('grupos.horarios.destroy', $horario) }}"
                              onsubmit="return confirm('¿Eliminar este horario fijo? Las reservas ya generadas se mantienen.')">
                            @csrf
                            @method('DELETE')
                            <button class="pb-2 underline text-sm text-red-600 dark:text-red-400">Eliminar</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Este grupo aún no tiene horarios fijos.</p>
                @endforelse

                {{-- Agregar horario --}}
                <form method="POST" action="{{ route('grupos.horarios.store', $grupo) }}"
                      class="flex flex-wrap items-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @csrf
                    <div>
                        <x-input-label for="nuevo_dia" value="Día" />
                        <select id="nuevo_dia" name="dia_semana" class="mt-1 block {{ $claseSelect }}" required>
                            @foreach (\App\Models\HorarioFijo::DIAS as $numero => $nombre)
                                <option value="{{ $numero }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="nuevo_desde" value="Desde" />
                        <select id="nuevo_desde" name="hora_inicio" class="mt-1 block {{ $claseSelect }}" required>
                            @foreach ($horas as $hora)
                                <option value="{{ $hora }}">{{ $hora }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="nuevo_hasta" value="Hasta" />
                        <select id="nuevo_hasta" name="hora_fin" class="mt-1 block {{ $claseSelect }}" required>
                            @foreach ($horasFin as $hora)
                                <option value="{{ $hora }}">{{ $hora }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Agregar horario</x-primary-button>
                </form>

                {{-- Generar reservas del mes --}}
                <form method="POST" action="{{ route('grupos.horarios.generar', $grupo) }}"
                      class="flex flex-wrap items-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @csrf
                    <div>
                        <x-input-label for="mes" value="Generar reservas del mes" />
                        <x-text-input id="mes" name="mes" type="month" class="mt-1 block"
                            :value="now()->addMonth()->format('Y-m')"
                            min="{{ now()->format('Y-m') }}" max="{{ now()->addMonths(6)->format('Y-m') }}" required />
                    </div>
                    <x-primary-button>Generar reservas</x-primary-button>
                </form>
            </div>

            <a href="{{ route('grupos.index') }}" class="underline text-sm text-gray-600 dark:text-gray-400">Volver al listado</a>
        </div>
    </div>
</x-app-layout>