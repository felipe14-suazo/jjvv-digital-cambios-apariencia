@props(['estado'])

@php
    $mapa = [
        'solicitado' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        'emitido'    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        'rechazado'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
        'anulado'    => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $mapa[$estado] ?? $mapa['anulado'] }}">
    {{ \App\Models\Certificado::ESTADOS[$estado] ?? $estado }}
</span>
