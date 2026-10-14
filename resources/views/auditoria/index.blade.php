<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Registro de auditoría</h2>
    </x-slot>

    @php
        $filtros = ['' => 'Todo', 'socio' => 'Socios', 'certificado' => 'Certificados', 'usuario' => 'Usuarios'];
        $actual = request('modelo', '');
        $eventos = ['creado' => 'Creado', 'modificado' => 'Modificado', 'eliminado' => 'Eliminado'];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex flex-wrap gap-2 px-4 sm:px-0">
                @foreach ($filtros as $valor => $etiqueta)
                    <a href="{{ route('auditoria.index', $valor ? ['modelo' => $valor] : []) }}"
                       class="px-3 py-1.5 rounded-md text-sm border
                              {{ $actual === $valor
                                  ? 'bg-gray-800 text-white border-gray-800 dark:bg-gray-200 dark:text-gray-900 dark:border-gray-200'
                                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}">
                        {{ $etiqueta }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-left text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">Fecha</th>
                            <th class="px-4 py-3 font-medium">Autor</th>
                            <th class="px-4 py-3 font-medium">Evento</th>
                            <th class="px-4 py-3 font-medium">Registro</th>
                            <th class="px-4 py-3 font-medium">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200 align-top">
                        @forelse ($auditorias as $a)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $a->created_at?->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $a->usuario?->name ?? 'Sistema / autoregistro' }}</td>
                                <td class="px-4 py-3">{{ $eventos[$a->evento] ?? $a->evento }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $a->etiquetaRegistro() }}</td>
                                <td class="px-4 py-3">
                                    @if ($a->evento === 'modificado')
                                        <ul class="space-y-1">
                                            @foreach ($a->cambios ?? [] as $campo => $v)
                                                <li><span class="font-medium">{{ $campo }}:</span>
                                                    {{ $v['antes'] ?? '—' }} → {{ $v['despues'] ?? '—' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ count($a->cambios ?? []) }} campos</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Sin registros todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 sm:px-0">{{ $auditorias->links() }}</div>
        </div>
    </div>
</x-app-layout>
