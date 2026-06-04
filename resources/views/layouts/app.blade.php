<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Set encoding karakter -->
        <meta charset="utf-8">

        <!-- Responsive viewport -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Token CSRF untuk keamanan form -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Judul aplikasi (default: Laravel kalau APP_NAME belum di-set di .env) -->
        <title>{{ config('app.name', 'Klinik Sehat') }}</title>
        <link rel="icon" href="{{ Vite::asset('public/images/logo.png') }}" type="png">
        <!-- Fonts (pakai Bunny.net, lebih cepat dan bebas tracking) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Load CSS & JS via Vite (Laravel 9+ default) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <!-- Background + min height (tailwind) -->
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

            <!-- Navigation bar (header menu), biasanya ada di layouts/navigation.blade.php -->
            @include('layouts.navigation')

            <!-- Page Heading (opsional, ditampilkan kalau ada slot $header) -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <!-- Slot header dari child view -->
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content utama -->
            <main>
                <!-- Slot isi halaman, semua konten dari view lain ditaruh di sini -->
                {{ $slot }}
            </main>
        </div>

        <!-- AlpineJS untuk interaktivitas ringan (dropdown, modal, dll) -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- Stack scripts: child view bisa @push('scripts') untuk tambah JS -->
        @stack('scripts')
    </body>
</html>
