<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Registrar egreso</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700 sm:rounded-xl">
                <form method="POST" action="{{ route('egresos.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="concepto" value="Concepto" />
                        <select id="concepto" name="concepto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar</option>
                            @foreach ($conceptos as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('concepto') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('concepto')" class="mt-2" />
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
                        <x-input-label for="numero_transaccion" value="Número de transacción" />
                        <x-text-input id="numero_transaccion" name="numero_transaccion" type="text" class="mt-1 block w-full" :value="old('numero_transaccion')" />
                        <x-input-error :messages="$errors->get('numero_transaccion')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="pagado_por" value="Quién pagó" />
                        <x-text-input id="pagado_por" name="pagado_por" type="text" class="mt-1 block w-full" :value="old('pagado_por')" />
                        <x-input-error :messages="$errors->get('pagado_por')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="comprobante" value="Copia del comprobante (PDF, JPG o PNG, máx. 5 MB)" />
                        <input id="comprobante" name="comprobante" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-gray-200" />
                        <x-input-error :messages="$errors->get('comprobante')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="observaciones" value="Observaciones (opcional)" />
                        <textarea id="observaciones" name="observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('observaciones') }}</textarea>
                        <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                    </div>

                    <x-primary-button>Registrar</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>