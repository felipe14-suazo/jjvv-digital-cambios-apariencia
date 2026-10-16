<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Egresos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status') === 'egreso-registrado')
                <p class="text-green-700">Egreso registrado.</p>
            @endif
            @if (session('status') === 'egreso-anulado')
                <p class="text-green-700">Egreso anulado.</p>
            @endif
            @error('egreso')
                <p class="text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-between items-center">
                <p class="text-gray-700">Saldo de caja: <strong>${{ number_format($saldo, 0, ',', '.') }}</strong></p>
                <a href="{{ route('egresos.create') }}" class="underline">Registrar egreso</a>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Fecha</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>N° transacción</th>
                            <th>Pagó</th>
                            <th>Comprobante</th>
                            <th>Registrado por</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($egresos as $egreso)
                            <tr class="border-b align-top">
                                <td class="py-2">{{ $egreso->fecha_pago->format('d-m-Y') }}</td>
                                <td>
                                    {{ \App\Models\Egreso::CONCEPTOS[$egreso->concepto] ?? $egreso->concepto }}
                                    @if ($egreso->estaAnulado())
                                        <div class="text-xs text-red-600">
                                            Anulado el {{ $egreso->anulado_en->format('d-m-Y') }}: {{ $egreso->motivo_anulacion }}
                                        </div>
                                    @endif
                                </td>
                                <td>${{ number_format($egreso->monto, 0, ',', '.') }}</td>
                                <td>{{ $egreso->numero_transaccion }}</td>
                                <td>{{ $egreso->pagado_por }}</td>
                                <td>
                                    <a href="{{ route('egresos.comprobante', $egreso) }}" target="_blank" class="underline">Ver</a>
                                </td>
                                <td>{{ $egreso->registrador?->name ?? '—' }}</td>
                                <td>
                                    @unless ($egreso->estaAnulado())
                                        <details>
                                            <summary class="cursor-pointer underline text-red-600">Anular</summary>
                                            <form method="POST" action="{{ route('egresos.anular', $egreso) }}" class="mt-2 space-y-2">
                                                @csrf
                                                <textarea name="motivo_anulacion" rows="2" required placeholder="Motivo obligatorio"
                                                          class="block w-full rounded-md border-gray-300 text-sm"></textarea>
                                                <button type="submit" class="px-3 py-1 rounded-md bg-red-600 text-white text-xs">
                                                    Confirmar anulación
                                                </button>
                                            </form>
                                        </details>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-4 text-center text-gray-500">Sin egresos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $egresos->links() }}
        </div>
    </div>
</x-app-layout>