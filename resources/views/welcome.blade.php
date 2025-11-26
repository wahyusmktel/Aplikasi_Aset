<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Manajemen Aset') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-white selection:bg-blue-500 selection:text-white font-sans">

    <div
        class="absolute inset-0 -z-10 h-full w-full bg-white dark:bg-gray-950 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]">
        <div
            class="absolute left-0 right-0 top-0 -z-10 m-auto h-[310px] w-[310px] rounded-full bg-blue-500 opacity-20 blur-[100px] dark:opacity-10">
        </div>
    </div>

    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5 flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight text-gray-900 dark:text-white">Stella<span
                            class="text-blue-600">Log</span></span>
                </a>
            </div>

            <div class="flex flex-1 justify-end gap-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 py-2">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="rounded-full bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <main class="isolate">
        <div class="relative px-6 pt-14 lg:px-8">
            <div class="mx-auto max-w-3xl py-32 sm:py-48 lg:py-40 text-center">

                <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                    <div
                        class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 dark:text-gray-400 ring-1 ring-gray-900/10 dark:ring-white/10 hover:ring-gray-900/20 dark:hover:ring-white/20 transition">
                        Sistem Inventaris Sekolah Terpadu
                    </div>
                </div>

                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                    Manajemen Aset <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">SMK Telkom
                        Lampung</span>
                </h1>

                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Platform digital untuk memantau, mengelola, dan melaporkan seluruh aset sekolah dengan efisien,
                    akurat, dan transparan.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="rounded-full bg-blue-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:scale-105">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="rounded-full bg-blue-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:scale-105">
                            Mulai Sekarang
                        </a>
                        <a href="#fitur" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white group">
                            Pelajari Fitur <span aria-hidden="true"
                                class="inline-block transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div id="fitur" class="mx-auto max-w-7xl px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div
                    class="relative rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 p-6 backdrop-blur-sm hover:border-blue-500/50 transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold leading-7 text-gray-900 dark:text-white">Pendataan Lengkap
                    </h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">Catat detail aset mulai dari
                        spesifikasi, lokasi, hingga kondisi terkini dengan mudah.</p>
                </div>

                <div
                    class="relative rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 p-6 backdrop-blur-sm hover:border-blue-500/50 transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold leading-7 text-gray-900 dark:text-white">Monitoring
                        Real-time</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">Pantau pergerakan dan status
                        peminjaman aset sekolah secara real-time.</p>
                </div>

                <div
                    class="relative rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 p-6 backdrop-blur-sm hover:border-blue-500/50 transition-colors">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold leading-7 text-gray-900 dark:text-white">Laporan
                        Pemeliharaan</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">Jadwalkan maintenance rutin dan
                        lacak riwayat perbaikan aset.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-gray-200 dark:border-gray-800 py-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} SMK Telkom Lampung. Dibuat dengan Laravel
            v{{ Illuminate\Foundation\Application::VERSION }}.
        </p>
    </footer>

</body>

</html>
