<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>イベント管理ツール</title>
        <!-- Scripts -->
            @if (app()->environment('local'))
                {{-- 開発環境用 --}}
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @else
                {{-- 本番環境用 --}}
                <link rel="stylesheet" href="/build/assets/app-DQk-URVn.css">
                <script type="module" src="/build/assets/app-Bf4POITK.js"></script>
            @endif

</head>
<body class="antialiased bg-gray-100 flex items-center justify-center min-h-screen text-center px-4 sm:px-0">
    <div class="w-full max-w-md">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2">
            イベント管理ツール
        </h1>
        <p class="text-sm sm:text-lg md:text-xl text-gray-700 mb-8 whitespace-nowrap">
            株式会社フリースタイルエンターテインメント
        </p>

        @if (Route::has('login'))
            <div class="flex justify-center gap-4 w-full max-w-sm mx-auto">
                <a href="{{ route('login') }}" 
                class="flex-1 px-6 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition text-center">
                    ログイン
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                    class="flex-1 px-6 py-2 border border-1 border-gray-800 text-gray-800 bg-white rounded hover:bg-slate-300 transition text-center">
                        登録
                    </a>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
