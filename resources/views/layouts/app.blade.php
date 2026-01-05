<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
       
        <title>{{ config('app.name', 'Freestyle_Project_manager') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Scripts & Styles -->
        @if (app()->environment('local'))
            {{-- 開発環境：Viteホットリロード --}}
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            {{-- 本番環境：キャッシュ回避のため ?v=タイムスタンプ付き --}}
            <?php
                $version = time(); // 毎回違う値になる（キャッシュ回避）
            ?>
            <link rel="stylesheet" href="/build/assets/app-DTMzEqAA.css?v={{ $version }}">
            <script type="module" src="/build/assets/app-Bf4POITK.js?v={{ $version }}" defer></script>
        @endif

        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-3 sm:py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
        @stack('scripts')
        
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    </body>
</html>