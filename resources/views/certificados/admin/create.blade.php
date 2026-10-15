<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Emitir certificado en ventanilla</h2>
            <a href="{{ route('admin.certificados.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">← Volver al listado</a>
        </div>
    </x-slot>

    @php
        $cls = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('admin.certificados.store') }}" class="space-y-6"
                      x-data="{
                          tipo: @js(old('tipo', $socioSel ? 'socio' : 'residencia')),
                          nombre: @js(old('nombre_solicitante', $socioSel?->nombre_completo ?? '')),
                          rut: @js(old('rut_solicitante', $socioSel?->rut ?? '')),
                          direccion: @js(old('direccion', $socioSel?->direccion ?? '')),
                          llenar(e) {
                              const o = e.target.selectedOptions[0];
                              if (!o || !o.value) return;
                              this.nombre = o.dataset.nombre; this.rut = o.dataset.rut; this.direccion = o.dataset.direccion;
                          }
                      }">
                    @csrf

                    <div>
                        <x-input-label for="tipo" value="Tipo de certificado" />
                        <select id="tipo" name="tipo" x-model="tipo" class="{{ $cls }}">
                            @foreach (\App\Models\Certificado::TIPOS as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                    </div>

                    <div>
                        <x-input-label for="socio_id" value="Socio (obligatorio para certificado de socio; opcional en residencia)" />
                        <select id="socio_id" name="socio_id" x-on:change="llenar($event)" class="{{ $cls }}">
                            <option value="">— Sin vincular a un socio —</option>
                            @foreach ($sociosActivos as $s)
                                <option value="{{ $s->id }}" data-nombre="{{ $s->nombre_completo }}" data-rut="{{ $s->rut }}" data-direccion="{{ $s->direccion }}"
                                        @selected((string) old('socio_id', $socioSel?->id) === (string) $s->id)>
                                    {{ $s->nombre_completo }} — {{ $s->rut }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('socio_id')" />
                    </div>

                    <div>
                        <x-input-label for="nombre_solicitante" value="Nombre completo" />
                        <input id="nombre_solicitante" name="nombre_solicitante" type="text" x-model="nombre" class="{{ $cls }}" required>
                        <x-input-error class="mt-2" :messages="$errors->get('nombre_solicitante')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="rut_solicitante" value="RUT" />
                            <input id="rut_solicitante" name="rut_solicitante" type="text" x-model="rut" placeholder="12345678-5" class="{{ $cls }}" required>
                            <x-input-error class="mt-2" :messages="$errors->get('rut_solicitante')" />
                        </div>
                        <div>
                            <x-input-label for="direccion" value="Dirección" />
                            <input id="direccion" name="direccion" type="text" x-model="direccion" class="{{ $cls }}" required>
                            <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="dirigido_a" value="Dirigido a / entidad donde se presentará" />
                        <input id="dirigido_a" name="dirigido_a" type="text" class="{{ $cls }}" required value="{{ old('dirigido_a') }}">
                        <x-input-error class="mt-2" :messages="$errors->get('dirigido_a')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="aporte" value="Aporte voluntario recibido (CLP)" />
                            <input id="aporte" name="aporte" type="number" min="0" step="1" class="{{ $cls }}" value="{{ old('aporte', $aporteSugerido) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('aporte')" />
                        </div>
                    </div>

                    <div x-show="tipo === 'residencia'">
                        <label class="inline-flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="documentos_verificados" value="1" @checked(old('documentos_verificados'))
                                   class="mt-0.5 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span>Verifiqué en persona el carnet de identidad y la boleta de luz o agua de la persona.</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('documentos_verificados')" />
                    </div>

                    <x-primary-button>Emitir certificado</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
