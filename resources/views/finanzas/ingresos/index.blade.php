@php
    $claseBoton = 'inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800';
    $claseTextarea = 'block w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
    $claseOk = 'rounded-lg bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200 dark:bg-green-900/30 dark:text-green-200 dark:ring-green-800';
    $claseError = 'rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200 dark:bg-red-900/30 dark:text-red-200 dark:ring-red-800';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Ingresos</h2>
            <a href="{{ route('ingresos.create') }}" class="{{ $claseBoton }}">Registrar ingreso</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-4 px-4 sm:px-6 lg:px-8">

            @if (session('status') === 'ingreso-anulado')
                <div class="{{ $claseOk }}">Ingreso anulado.</div>
            @endif
            @if (session('status') === 'devolucion-registrada')
                <div class="{{ $claseOk }}">Devolución registrada.</div>
            @endif
            @error('ingreso')
                <div class="{{ $claseError }}">{{ $message }}</div>
            @enderror
            @error('devolucion')
                <div class="{{ $claseError }}">{{ $message }}</div>
            @enderror

            {{-- Saldo de caja --}}
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo de caja</p>
                    <p class="mt-1 text-3xl font-semibold tabular-nums {{ $saldo < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                        ${{ number_format($saldo, 0, ',', '.') }}
                    </p>
                </div>
                <p class="max-w-xs text-xs text-gray-500 dark:text-gray-400 sm:text-right">
                    Ingresos menos egresos y devoluciones, sin contar los registros anulados.
                </p>
            </div>

            {{-- Tabla --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3 text-right">Monto</th>
                                <th class="px-4 py-3">Medio</th>
                                <th class="px-4 py-3">Comprobante</th>
                                <th class="px-4 py-3">Registrado por</th>
                                <th class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($ingresos as $ingreso)
                                <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 dark:text-gray-300">{{ $ingreso->fecha_pago->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        <span class="{{ $ingreso->estaAnulado() ? 'line-through opacity-60' : '' }}">
                                            {{ \App\Models\Ingreso::CONCEPTOS[$ingreso->concepto] ?? $ingreso->concepto }}
                                        </span>
                                        @if ($ingreso->tipo_cuota)
                                            <span class="ml-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                {{ \App\Models\Ingreso::TIPOS_CUOTA[$ingreso->tipo_cuota] }}
                                            </span>
                                        @endif
                                        @if ($ingreso->estaAnulado())
                                            <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                <span class="font-semibold">Anulado</span> el {{ $ingreso->anulado_en->format('d-m-Y') }}: {{ $ingreso->motivo_anulacion }}
                                            </div>
                                        @endif
                                        @if ($ingreso->devolucion)
                                            <div class="mt-1 text-xs text-amber-700 dark:text-amber-400">
                                                <span class="font-semibold">Devuelto</span> el {{ $ingreso->devolucion->fecha_devolucion->format('d-m-Y') }}: {{ $ingreso->devolucion->motivo }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-medium tabular-nums text-gray-900 dark:text-gray-100 {{ $ingreso->estaAnulado() ? 'line-through opacity-60' : '' }}">
                                        ${{ number_format($ingreso->monto, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \App\Models\Ingreso::MEDIOS[$ingreso->medio_pago] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $ingreso->talonario ?? $ingreso->numero_transaccion }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $ingreso->registrador?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @unless ($ingreso->estaAnulado() || $ingreso->devolucion)
                                            <details>
                                                <summary class="inline-block cursor-pointer text-xs font-medium text-red-600 hover:underline dark:text-red-400">Anular</summary>
                                                <form method="POST" action="{{ route('ingresos.anular', $ingreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo_anulacion" rows="2" required placeholder="Motivo obligatorio" class="{{ $claseTextarea }}"></textarea>
                                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">
                                                        Confirmar anulación
                                                    </button>
                                                </form>
                                            </details>
                                        @endunless

                                        @if ($ingreso->puedeDevolverse())
                                            <details class="mt-2">
                                                <summary class="inline-block cursor-pointer text-xs font-medium text-amber-700 hover:underline dark:text-amber-400">Devolver</summary>
                                                <form method="POST" action="{{ route('ingresos.devolver', $ingreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo" rows="2" required placeholder="Motivo obligatorio" class="{{ $claseTextarea }}"></textarea>
                                                    <button type="submit" class="rounded-md bg-amber-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-500">
                                                        Confirmar devolución
                                                    </button>
                                                </form>
                                            </details>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Sin ingresos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $ingresos->links() }}
        </div>
    </div>
</x-app-layout>