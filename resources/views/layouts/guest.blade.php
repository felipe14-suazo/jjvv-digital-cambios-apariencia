<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-b from-indigo-50 to-gray-100 px-4 py-10 dark:from-gray-900 dark:to-gray-900">
            <a href="/" class="flex flex-col items-center gap-3">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/30">
                    <x-application-logo class="h-8 w-8" />
                </span>
                <span class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ config('app.name') }}</span>
            </a>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700 sm:max-w-md sm:rounded-xl">
                {{ $slot }}
            </div>

            <a href="{{ route('verificar.formulario') }}" class="mt-6 text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                Verificar un certificado
            </a>
        </div>
    </body>
</html>
