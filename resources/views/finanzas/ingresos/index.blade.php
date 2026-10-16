<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ingresos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex justify-between items-center">
                <p class="text-gray-700">Total registrado: <strong>${{ number_format($total, 0, ',', '.') }}</strong></p>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ingresos as $ingreso)
                            <tr class="border-b">
                                <td class="py-2">{{ $ingreso->fecha_pago->format('d-m-Y') }}</td>
                                <td>{{ Ingreso::CONCEPTOS[$ingreso->concepto] ?? $ingreso->concepto }}</td>
                                <td>${{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                                <td>{{ Ingreso::MEDIOS[$ingreso->medio_pago] }}</td>
                                <td>{{ $ingreso->talonario ?? $ingreso->numero_transaccion }}</td>
                                <td>{{ $ingreso->registrador?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-500">Sin ingresos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $ingresos->links() }}
        </div>
    </div>
</x-app-layout>