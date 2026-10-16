<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Parámetros del sistema</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                @if (session('status') === 'parametros-guardados')
                    <p class="mb-4 text-green-700">Parámetros guardados.</p>
                @endif

                <form method="POST" action="{{ route('parametros.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    @foreach ($parametros as $parametro)
                        <div>
                            <x-input-label for="valores_{{ $parametro->clave }}" :value="$parametro->etiqueta" />
                            <x-text-input
                                id="valores_{{ $parametro->clave }}"
                                name="valores[{{ $parametro->clave }}]"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('valores.'.$parametro->clave, $parametro->valor)"
                            />
                            <x-input-error :messages="$errors->get('valores.'.$parametro->clave)" class="mt-2" />
                        </div>
                    @endforeach

                    <x-primary-button>Guardar</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>