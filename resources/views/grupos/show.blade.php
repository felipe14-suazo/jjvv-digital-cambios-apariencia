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

            <a href="{{ route('grupos.index') }}" class="underline text-sm text-gray-600 dark:text-gray-400">Volver al listado</a>
        </div>
    </div>
</x-app-layout>