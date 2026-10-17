<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar datos de {{ $socio->nombre_completo }}</h2>
            <a href="{{ route('socios.show', $socio) }}" class="text-sm underline text-gray-600 dark:text-gray-400">← Volver a la ficha</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Cada modificación queda registrada con tu nombre y la fecha. El estado de la inscripción se cambia desde la ficha del socio.
                </p>

                <form method="POST" action="{{ route('socios.actualizar', $socio) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @include('socios._datos', ['socio' => $socio])

                    <x-primary-button>Guardar cambios</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
