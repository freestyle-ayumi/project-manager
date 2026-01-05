<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>繧､繝吶Φ繝育ｮ｡逅・ヤ繝ｼ繝ｫ</title>
        <!-- Scripts -->
            @if (app()->environment('local'))
                {{-- 髢狗匱 --}}
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @else
                {{-- 譛ｬ逡ｪ --}}
                <link rel="stylesheet" href="/build/assets/app-DTMzEqAA.css">
                <script type="module" src="/build/assets/app-Bf4POITK.js"></script>
            @endif

</head>
<body class="antialiased bg-gray-100 flex items-center justify-center min-h-screen text-center px-4 sm:px-0">
    <div class="w-full max-w-md">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2">
            繧､繝吶Φ繝育ｮ｡逅・・繝ｫ
        </h1>
        <p class="text-sm sm:text-lg md:text-xl text-gray-700 mb-8 whitespace-nowrap">
            譬ｪ蠑丈ｼ夂､ｾ繝輔Μ繝ｼ繧ｹ繧ｿ繧､繝ｫ繧ｨ繝ｳ繧ｿ繝ｼ繝・う繝ｳ繝｡繝ｳ繝・/p>

        @if (Route::has('login'))
            <div class="flex justify-center gap-4 w-full max-w-sm mx-auto">
                <a href="{{ route('login') }}" 
                class="flex-1 px-6 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition text-center">
                    繝ｭ繧ｰ繧､繝ｳ
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                    class="flex-1 px-6 py-2 border border-1 border-gray-800 text-gray-800 bg-white rounded hover:bg-slate-300 transition text-center">
                        逋ｻ骭ｲ
                    </a>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
