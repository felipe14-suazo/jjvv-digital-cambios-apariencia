<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $certificado->etiquetaTipo() }} {{ $certificado->folio ? '· Folio '.$certificado->folio : '' }}
            </h2>
            <a href="{{ route('admin.certificados.index') }}" class="text-sm underline text-gray-600 dark:text-gray-400">← Volver al listado</a>
        </div>
    </x-slot>

    @php
        $cls = 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
        $c = $certificado;
        $mensajes = [
            'certificado-emitido' => 'El certificado fue emitido.',
            'certificado-rechazado' => 'La solicitud fue rechazada.',
            'certificado-anulado' => 'El certificado fue anulado.',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (isset($mensajes[session('status')]))
                <div class="p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 rounded-lg text-sm">{{ $mensajes[session('status')] }}</div>
            @endif

            {{-- ===== Resumen ===== --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Datos del certificado</h3>
                    <x-estado-certificado :estado="$c->estado" />
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Solicitante</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->nombre_solicitante }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">RUT</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->rut_solicitante }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Dirección</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->direccion }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Dirigido a</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->dirigido_a }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Solicitud registrada</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->created_at->format('d-m-Y H:i') }}</dd></div>
                    @if ($c->socio)
                        <div><dt class="text-gray-500 dark:text-gray-400">Ficha de socio</dt><dd><a class="underline text-indigo-600 dark:text-indigo-400" href="{{ route('socios.show', $c->socio) }}">{{ $c->socio->nombre_completo }}</a></dd></div>
                    @endif
                    @if ($c->estado !== 'solicitado' && $c->estado !== 'rechazado')
                        <div><dt class="text-gray-500 dark:text-gray-400">Emitido el</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->fecha_emision?->format('d-m-Y') }}</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Vence el</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->fecha_vencimiento?->format('d-m-Y') }}</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Otorgado por</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->otorgante?->name }} ({{ $c->otorgante?->nombreRol() }})</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Aporte</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->aporte !== null ? '$'.number_format($c->aporte, 0, ',', '.') : '—' }}</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Código de verificación</dt><dd class="text-gray-900 dark:text-gray-100 font-mono">{{ $c->codigoFormateado() }}</dd></div>
                    @endif
                    @if ($c->observaciones)
                        <div class="sm:col-span-2"><dt class="text-gray-500 dark:text-gray-400">Observaciones</dt><dd class="text-gray-900 dark:text-gray-100">{{ $c->observaciones }}</dd></div>
                    @endif
                </dl>

                <div class="mt-4">
                    <a href="{{ route('admin.certificados.pdf', $c) }}" target="_blank" class="underline text-sm text-indigo-600 dark:text-indigo-400">
                        {{ $c->estado === 'solicitado' ? 'Ver PDF de revisión (borrador)' : 'Ver PDF' }}
                    </a>
                </div>
            </div>

            {{-- ===== Historial de Descargas ===== --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Descargas</h3>
                @forelse ($c->descargas as $descarga)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $descarga->descargado_en->format('d-m-Y H:i') }}
                        · {{ $descarga->usuario?->name ?? 'Usuario eliminado' }}
                        · IP {{ $descarga->ip }}
                    </p>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin descargas registradas.</p>
                @endforelse
            </div>

            {{-- ===== Revisión y emisión ===== --}}
            @if ($c->estado === 'solicitado')
                <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Revisión</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Corrige los datos si no coinciden con el carnet. El folio se asigna automáticamente al emitir.
                    </p>

                    <form method="POST" action="{{ route('admin.certificados.emitir', $c) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="nombre_solicitante" value="Nombre completo" />
                            <input id="nombre_solicitante" name="nombre_solicitante" type="text" class="{{ $cls }}" required value="{{ old('nombre_solicitante', $c->nombre_solicitante) }}">
                            <x-input-error class="mt-2" :messages="$errors->get('nombre_solicitante')" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="rut_solicitante" value="RUT" />
                                <input id="rut_solicitante" name="rut_solicitante" type="text" class="{{ $cls }}" required value="{{ old('rut_solicitante', $c->rut_solicitante) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('rut_solicitante')" />
                            </div>
                            <div>
                                <x-input-label for="direccion" value="Dirección" />
                                <input id="direccion" name="direccion" type="text" class="{{ $cls }}" required value="{{ old('direccion', $c->direccion) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="dirigido_a" value="Dirigido a" />
                                <input id="dirigido_a" name="dirigido_a" type="text" class="{{ $cls }}" required value="{{ old('dirigido_a', $c->dirigido_a) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('dirigido_a')" />
                            </div>
                            <div>
                                <x-input-label for="aporte" value="Aporte voluntario recibido (CLP)" />
                                <input id="aporte" name="aporte" type="number" min="0" step="1" class="{{ $cls }}" value="{{ old('aporte', $aporteSugerido) }}">
                                <x-input-error class="mt-2" :messages="$errors->get('aporte')" />
                            </div>
                        </div>

                        @if ($c->tipo === 'residencia')
                            <div>
                                <label class="inline-flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="documentos_verificados" value="1" @checked(old('documentos_verificados'))
                                           class="mt-0.5 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span>Verifiqué en persona el carnet de identidad y la boleta de luz o agua.</span>
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('documentos_verificados')" />
                            </div>
                        @endif
                        
                        @if ($c->tipo === 'residencia')
                            {{-- Datos del comprobante subido por el socio --}}
                            @if ($c->comprobante_path)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        <a href="{{ route('admin.certificados.comprobante', $c) }}" target="_blank" class="underline text-indigo-600 dark:text-indigo-400 font-medium">Ver comprobante</a>
                                        · N° de transacción: <span class="font-mono font-semibold">{{ $c->numero_transaccion }}</span>
                                    </p>
                                </div>
                            @endif

                            <div>
                                <label class="inline-flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="pago_verificado" value="1" required @checked(old('pago_verificado'))
                                           class="mt-0.5 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span>Confirmo que el pago de $1.000 figura en la cuenta.</span>
                                </label>
                                <x-input-error class="mt-2" :messages="$errors->get('pago_verificado')" />
                            </div>
                        @endif

                        <x-primary-button>Validar y emitir certificado</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('admin.certificados.rechazar', $c) }}" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                        @csrf
                        <x-input-label for="obs_rechazo" value="Motivo del rechazo" />
                        <textarea id="obs_rechazo" name="observaciones" rows="2" required class="{{ $cls }}">{{ old('observaciones') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('observaciones')" />
                        <x-danger-button>Rechazar solicitud</x-danger-button>
                    </form>
                </div>
            @endif

            {{-- ===== Anulación ===== --}}
            @if ($c->estado === 'emitido')
                <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Anular certificado</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Úsalo si se emitió con errores. En la verificación en línea aparecerá como anulado.</p>
                    <form method="POST" action="{{ route('admin.certificados.anular', $c) }}" class="space-y-3"
                          onsubmit="return confirm('¿Anular este certificado? Esta acción no se puede deshacer.');">
                        @csrf
                        <textarea name="observaciones" rows="2" required placeholder="Motivo de la anulación" class="{{ $cls }}">{{ old('observaciones') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('observaciones')" />
                        <x-danger-button>Anular certificado</x-danger-button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
