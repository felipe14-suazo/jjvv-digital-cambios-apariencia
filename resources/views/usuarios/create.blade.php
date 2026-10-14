<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Crear usuario</h2>
            <a href="{{ route('usuarios.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">← Volver al listado</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-6">
                    @csrf

                    @include('usuarios._form', ['usuario' => null])

                    <div class="flex items-center gap-4">
                        <x-primary-button>Crear usuario</x-primary-button>
                        <a href="{{ route('usuarios.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>