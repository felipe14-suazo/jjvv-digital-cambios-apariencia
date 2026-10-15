<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Mis certificados</h2>
    </x-slot>

    @php
        $cls = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
        $esSocioActivo = $socio && $socio->estado === 'activo';
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'certificado-solicitado')
                <div class="p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm">
                    Tu solicitud fue enviada. Cuando la directiva la apruebe podrás descargar el certificado desde esta página.
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Solicitar un certificado</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Para el certificado de residencia debes presentar en la sede tu carnet de identidad y una boleta de luz o agua.
                    El aporte voluntario es de ${{ number_format(config('jjvv.aporte_residencia'), 0, ',', '.') }}.
                </p>

                <form method="POST" action="{{ route('certificados.store') }}" class="mt-6 space-y-6"
                      x-data="{ tipo: @js(old('tipo', 'residencia')) }">
                    @csrf

                    <div>
                        <x-input-label for="tipo" value="Tipo de certificado" />
                        <select id="tipo" name="tipo" x-model="tipo" class="{{ $cls }}">
                            <option value="residencia">Certificado de residencia</option>
                            <option value="socio" @disabled(! $esSocioActivo)>Certificado de socio{{ $esSocioActivo ? '' : ' (solo socios activos)' }}</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                    </div>

                    <div x-show="tipo === 'residencia'" class="space-y-6">
                        <div>
                            <x-input-label for="nombre_solicitante" value="Nombre completo" />
                            <input id="nombre_solicitante" name="nombre_solicitante" type="text" class="{{ $cls }}"
                                   x-bind:disabled="tipo !== 'residencia'"
                                   value="{{ old('nombre_solicitante', $socio?->nombre_completo ?? auth()->user()->name) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('nombre_solicitante')" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="rut_solicitante" value="RUT" />
                                <input id="rut_solicitante" name="rut_solicitante" type="text" class="{{ $cls }}" placeholder="12345678-5"
                                       x-bind:disabled="tipo !== 'residencia'" value="{{ old('rut_solicitante', $socio?->rut) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('rut_solicitante')" />
                            </div>
                            <div>
                                <x-input-label for="direccion" value="Dirección" />
                                <input id="direccion" name="direccion" type="text" class="{{ $cls }}"
                                       x-bind:disabled="tipo !== 'residencia'" value="{{ old('direccion', $socio?->direccion) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                            </div>
                        </div>
                    </div>

                    <p x-show="tipo === 'socio'" class="text-sm text-gray-600 dark:text-gray-400">
                        Se usarán los datos de tu ficha de socio ({{ $socio?->nombre_completo }}).
                    </p>

                    <div>
                        <x-input-label for="dirigido_a" value="¿Dónde o ante quién lo presentarás?" />
                        <input id="dirigido_a" name="dirigido_a" type="text" class="{{ $cls }}" required
                               placeholder="Ej.: Registro Social de Hogares, Municipalidad" value="{{ old('dirigido_a') }}">
                        <x-input-error class="mt-2" :messages="$errors->get('dirigido_a')" />
                    </div>

                    <x-primary-button>Enviar solicitud</x-primary-button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Certificado</th>
                            <th class="px-4 py-3 font-medium">Presentado ante</th>
                            <th class="px-4 py-3 font-medium">Folio</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse ($certificados as $c)
                            <tr>
                                <td class="px-4 py-3">{{ $c->etiquetaTipo() }}<div class="text-xs text-gray-500">{{ $c->created_at->format('d-m-Y') }}</div></td>
                                <td class="px-4 py-3">{{ $c->dirigido_a }}</td>
                                <td class="px-4 py-3">{{ $c->folio ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <x-estado-certificado :estado="$c->estado" />
                                    @if ($c->estado === 'emitido')
                                        <div class="text-xs text-gray-500 mt-1">Vence {{ $c->fecha_vencimiento->format('d-m-Y') }}</div>
                                    @elseif ($c->observaciones && in_array($c->estado, ['rechazado', 'anulado']))
                                        <div class="text-xs text-gray-500 mt-1">{{ $c->observaciones }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($c->estado === 'emitido')
                                        <a href="{{ route('certificados.descargar', $c) }}" target="_blank" class="underline text-indigo-600 dark:text-indigo-400">Descargar PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Aún no has solicitado certificados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
