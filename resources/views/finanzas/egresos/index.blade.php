@php
    $claseBoton = 'inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800';
    $claseTextarea = 'block w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
    $claseOk = 'rounded-lg bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200 dark:bg-green-900/30 dark:text-green-200 dark:ring-green-800';
    $claseError = 'rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200 dark:bg-red-900/30 dark:text-red-200 dark:ring-red-800';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Egresos</h2>
            <a href="{{ route('egresos.create') }}" class="{{ $claseBoton }}">Registrar egreso</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-4 px-4 sm:px-6 lg:px-8">

            @if (session('status') === 'egreso-registrado')
                <div class="{{ $claseOk }}">Egreso registrado.</div>
            @endif
            @if (session('status') === 'egreso-anulado')
                <div class="{{ $claseOk }}">Egreso anulado.</div>
            @endif
            @error('egreso')
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
                                <th class="px-4 py-3">N° transacción</th>
                                <th class="px-4 py-3">Pagó</th>
                                <th class="px-4 py-3">Comprobante</th>
                                <th class="px-4 py-3">Registrado por</th>
                                <th class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($egresos as $egreso)
                                <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700 dark:text-gray-300">{{ $egreso->fecha_pago->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        <span class="{{ $egreso->estaAnulado() ? 'line-through opacity-60' : '' }}">
                                            {{ \App\Models\Egreso::CONCEPTOS[$egreso->concepto] ?? $egreso->concepto }}
                                        </span>
                                        @if ($egreso->estaAnulado())
                                            <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                <span class="font-semibold">Anulado</span> el {{ $egreso->anulado_en->format('d-m-Y') }}: {{ $egreso->motivo_anulacion }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-medium tabular-nums text-gray-900 dark:text-gray-100 {{ $egreso->estaAnulado() ? 'line-through opacity-60' : '' }}">
                                        ${{ number_format($egreso->monto, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $egreso->numero_transaccion }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $egreso->pagado_por }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('egresos.comprobante', $egreso) }}" target="_blank"
                                           class="text-indigo-600 hover:underline dark:text-indigo-400">Ver</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $egreso->registrador?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @unless ($egreso->estaAnulado())
                                            <details>
                                                <summary class="inline-block cursor-pointer text-xs font-medium text-red-600 hover:underline dark:text-red-400">Anular</summary>
                                                <form method="POST" action="{{ route('egresos.anular', $egreso) }}" class="mt-2 w-56 space-y-2">
                                                    @csrf
                                                    <textarea name="motivo_anulacion" rows="2" required placeholder="Motivo obligatorio" class="{{ $claseTextarea }}"></textarea>
                                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">
                                                        Confirmar anulación
                                                    </button>
                                                </form>
                                            </details>
                                        @endunless
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Sin egresos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $egresos->links() }}
        </div>
    </div>
</x-app-layout>