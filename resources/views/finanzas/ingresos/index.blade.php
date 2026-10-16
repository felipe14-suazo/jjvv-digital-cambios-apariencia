<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ingresos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status') === 'ingreso-anulado')
                <p class="text-green-700">Ingreso anulado.</p>
            @endif
            @error('ingreso')
                <p class="text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-between items-center">
                <p class="text-gray-700">Saldo de caja: <strong>${{ number_format($saldo, 0, ',', '.') }}</strong></p>
                <a href="{{ route('ingresos.create') }}" class="underline">Registrar ingreso</a>
            </div>

            <div class="bg-white p-6 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Fecha</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Medio</th>
                            <th>Comprobante</th>
                            <th>Registrado por</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ingresos as $ingreso)
                            <tr class="border-b align-top">
                                <td class="py-2">{{ $ingreso->fecha_pago->format('d-m-Y') }}</td>
                                <td>
                                    {{ Ingreso::CONCEPTOS[$ingreso->concepto] ?? $ingreso->concepto }}
                                    @if ($ingreso->tipo_cuota)
                                        <span class="text-xs text-gray-500">({{ Ingreso::TIPOS_CUOTA[$ingreso->tipo_cuota] }})</span>
                                    @endif
                                    @if ($ingreso->estaAnulado())
                                        <div class="text-xs text-red-600">
                                            Anulado el {{ $ingreso->anulado_en->format('d-m-Y') }}: {{ $ingreso->motivo_anulacion }}
                                        </div>
                                    @endif
                                </td>
                                <td>${{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                                <td>{{ Ingreso::MEDIOS[$ingreso->medio_pago] }}</td>
                                <td>{{ $ingreso->talonario ?? $ingreso->numero_transaccion }}</td>
                                <td>{{ $ingreso->registrador?->name ?? '—' }}</td>
                                <td>
                                    @unless ($ingreso->estaAnulado())
                                        <details>
                                            <summary class="cursor-pointer underline text-red-600">Anular</summary>
                                            <form method="POST" action="{{ route('ingresos.anular', $ingreso) }}" class="mt-2 space-y-2">
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
                            <tr><td colspan="7" class="py-4 text-center text-gray-500">Sin ingresos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $ingresos->links() }}
        </div>
    </div>
</x-app-layout>