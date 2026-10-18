<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">

        <header class="mx-auto flex max-w-6xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <x-application-logo class="h-5 w-5" />
                </span>
                <span class="font-semibold">{{ config('app.name') }}</span>
            </a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2 text-sm">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-indigo-500">Ir al panel</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-4 py-2 font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Iniciar sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-indigo-500">Registrarse</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main>
            {{-- Hero --}}
            <section class="bg-gradient-to-b from-indigo-50 to-gray-50 dark:from-gray-800/50 dark:to-gray-900">
                <div class="mx-auto max-w-4xl px-4 py-16 text-center sm:px-6 sm:py-24 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-5xl">
                        La gestión de tu junta de vecinos, simple y ordenada
                    </h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base text-gray-600 dark:text-gray-300 sm:text-lg">
                        Inscripción de socios, certificados con folio y verificación en línea, y calendario de la sede, en un solo lugar.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        
                        <a href="{{ route('verificar.formulario') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700">
                            Verificar un certificado
                        </a>
                    </div>
                </div>
            </section>

            {{-- Funciones --}}
            <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:px-8">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">01</p>
                        <h2 class="mt-2 text-lg font-semibold">Inscripción de socios</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Solicita tu inscripción en línea y reserva la hora para firmar el libro de socios.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">02</p>
                        <h2 class="mt-2 text-lg font-semibold">Certificados en línea</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Los socios pueden solicitar su certificado de residencia, y cualquier persona puede comprobar que es válido con su código.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">03</p>
                        <h2 class="mt-2 text-lg font-semibold">Calendario de la sede</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Los socios activos consultan la disponibilidad de la sede y los horarios de uso de los grupos.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 py-6 text-center text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
            © {{ date('Y') }} {{ config('app.name') }}
        </footer>
    </body>
</html>
