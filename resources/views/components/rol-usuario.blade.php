@props(['rol'])

@php
    $mapa = [
        'admin'      => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
        'presidente' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
        'secretario' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        'tesorero'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        'vecino'     => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $mapa[$rol] ?? $mapa['vecino'] }}">
    {{ \App\Models\User::ROLES[$rol] ?? $rol }}
</span>