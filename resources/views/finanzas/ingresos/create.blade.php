<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrar ingreso</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('ingresos.store') }}" class="space-y-4"
                      x-data="{ concepto: @js(old('concepto', '')) }">
                    @csrf

                    <div>
                        <x-input-label for="concepto" value="Concepto" />
                        <select id="concepto" name="concepto" x-model="concepto" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Seleccionar</option>
                            @foreach ($conceptos as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('concepto') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('concepto')" class="mt-2" />
                    </div>

                    <div x-show="concepto === 'cuota_socios'" x-cloak>
                        <x-input-label for="tipo_cuota" value="Tipo de cuota" />
                        <select id="tipo_cuota" name="tipo_cuota" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Seleccionar</option>
                            @foreach ($tiposCuota as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('tipo_cuota') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo_cuota')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="fecha_pago" value="Fecha de pago" />
                        <x-text-input id="fecha_pago" name="fecha_pago" type="date" class="mt-1 block w-full" :value="old('fecha_pago')" />
                        <x-input-error :messages="$errors->get('fecha_pago')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="monto" value="Monto (CLP)" />
                        <x-text-input id="monto" name="monto" type="number" min="1" class="mt-1 block w-full" :value="old('monto')" />
                        <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="medio_pago" value="Medio de pago" />
                        <select id="medio_pago" name="medio_pago" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach ($medios as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('medio_pago') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('medio_pago')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="talonario" value="Número de talonario (si es efectivo)" />
                        <x-text-input id="talonario" name="talonario" type="text" class="mt-1 block w-full" :value="old('talonario')" />
                        <x-input-error :messages="$errors->get('talonario')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="numero_transaccion" value="Número de transacción (si es transferencia)" />
                        <x-text-input id="numero_transaccion" name="numero_transaccion" type="text" class="mt-1 block w-full" :value="old('numero_transaccion')" />
                        <x-input-error :messages="$errors->get('numero_transaccion')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="observaciones" value="Observaciones (obligatorias si el concepto es Otros)" />
                        <textarea id="observaciones" name="observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('observaciones') }}</textarea>
                        <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                    </div>

                    <x-primary-button>Registrar</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>