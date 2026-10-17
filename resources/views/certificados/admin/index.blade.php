<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Certificados</h2>
            <a href="{{ route('admin.certificados.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">Emitir en ventanilla</a>
        </div>
    </x-slot>

    @php $cls = 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm'; @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" class="flex flex-wrap gap-2 px-4 sm:px-0">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, RUT o folio" class="{{ $cls }}">
                <select name="estado" class="{{ $cls }}">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Certificado::ESTADOS as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
                <select name="tipo" class="{{ $cls }}">
                    <option value="">Todos los tipos</option>
                    @foreach (\App\Models\Certificado::TIPOS as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
                <x-secondary-button type="submit">Filtrar</x-secondary-button>
            </form>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Folio</th>
                            <th class="px-4 py-3 font-medium">Tipo</th>
                            <th class="px-4 py-3 font-medium">Solicitante</th>
                            <th class="px-4 py-3 font-medium">Presentado ante</th>
                            <th class="px-4 py-3 font-medium">Fecha</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse ($certificados as $c)
                            <tr>
                                <td class="px-4 py-3">{{ $c->folio ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $c->etiquetaTipo() }}</td>
                                <td class="px-4 py-3">{{ $c->nombre_solicitante }}<div class="text-xs text-gray-500">{{ $c->rut_solicitante }}</div></td>
                                <td class="px-4 py-3">{{ $c->dirigido_a }}</td>
                                <td class="px-4 py-3">{{ ($c->fecha_emision ?? $c->created_at)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3"><x-estado-certificado :estado="$c->estado" /></td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('admin.certificados.show', $c) }}" class="underline text-indigo-600 dark:text-indigo-400">{{ $c->estado === 'solicitado' ? 'Revisar' : 'Ver' }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay certificados con esos filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 sm:px-0">{{ $certificados->links() }}</div>
        </div>
    </div>
</x-app-layout>
