<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Registrar socio en ventanilla</h2>
            <a href="{{ route('socios.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">← Volver al listado</a>
        </div>
    </x-slot>

    @php
        $cls = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Para personas que se inscriben presencialmente. Requisitos: residir en el sector hace al menos 6 meses,
                    carnet de identidad y boleta de un servicio básico con la dirección. Marca "Activo" solo si ya firmó el libro de registro.
                </p>

                <form method="POST" action="{{ route('socios.guardar') }}" enctype="multipart/form-data"
                      class="mt-6 space-y-6" x-data="{ estado: @js(old('estado', 'activo')) }">
                    @csrf

                    @include('socios._datos', ['socio' => null])

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="estado" value="Estado de la inscripción" />
                            <select id="estado" name="estado" x-model="estado" class="{{ $cls }}">
                                <option value="activo">Activo (ya firmó el libro)</option>
                                <option value="por_firmar">Pendiente de firma</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                        </div>
                        <div x-show="estado === 'activo'">
                            <x-input-label for="fecha_ingreso" value="Fecha de ingreso (firma del libro)" />
                            <input id="fecha_ingreso" name="fecha_ingreso" type="date" class="{{ $cls }}"
                                   max="{{ now()->toDateString() }}" value="{{ old('fecha_ingreso', now()->toDateString()) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('fecha_ingreso')" />
                        </div>
                    </div>

                    <x-primary-button>Registrar socio</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
