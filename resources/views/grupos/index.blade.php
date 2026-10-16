<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Grupos y organizaciones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'grupo-creado')
                <p class="px-4 sm:px-0 text-green-700 dark:text-green-400">Grupo creado.</p>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Grupo</th>
                            <th class="px-4 py-3 font-medium">Representante</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse ($grupos as $grupo)
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('grupos.show', $grupo) }}" class="underline">{{ $grupo->nombre }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $grupo->representante?->nombre_completo ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($grupo->tieneRepresentanteVigente())
                                        Con representante
                                    @else
                                        <span class="text-red-600 dark:text-red-400">Sin representante vigente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Aún no hay grupos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Crear grupo</h3>
                <form method="POST" action="{{ route('grupos.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="horarios_uso" value="Horarios de uso (texto libre)" />
                        <textarea id="horarios_uso" name="horarios_uso" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-300">{{ old('horarios_uso') }}</textarea>
                        <x-input-error :messages="$errors->get('horarios_uso')" class="mt-2" />
                    </div>
                    <x-primary-button>Crear grupo</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>