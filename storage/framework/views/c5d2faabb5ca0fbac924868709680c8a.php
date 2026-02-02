<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>イベント管理ツール</title>

    <!-- Viteアセット読み込み -->
    <?php if(app()->environment('local')): ?>
        <!-- ローカル（開発）：Viteホットリロード（第2引数なし） -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php else: ?>
        <!-- 本番：ビルド済みアセット（手動で最新ハッシュに更新） -->
        <?php $version = time(); ?>
        <link rel="stylesheet" href="<?php echo e(asset('build/assets/app-最新ハッシュ.css')); ?>?v=<?php echo e($version); ?>">
        <script type="module" src="<?php echo e(asset('build/assets/app-最新ハッシュ.js')); ?>?v=<?php echo e($version); ?>" defer></script>
    <?php endif; ?>

    <!-- ファビコン -->
    <link rel="icon" href="<?php echo e(asset('fse-logo.ico')); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo e(asset('fse-logo.svg')); ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?php echo e(asset('fse-logo.svg')); ?>">
</head>
<body class="antialiased bg-gray-100 flex items-center justify-center min-h-screen text-center px-4 sm:px-0">
    <div class="w-full max-w-md">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2">
            イベント管理ツール
        </h1>
        <p class="text-sm sm:text-lg md:text-xl text-gray-700 mb-8 whitespace-nowrap">
            株式会社フリースタイルエンターテインメント
        </p>

        <?php if(Route::has('login')): ?>
            <div class="flex justify-center gap-4 w-full max-w-sm mx-auto">
                <a href="<?php echo e(route('login')); ?>" 
                class="flex-1 px-6 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition text-center">
                    ログイン
                </a>

                <?php if(Route::has('register')): ?>
                    <a href="<?php echo e(route('register')); ?>" 
                    class="flex-1 px-6 py-2 border border-1 border-gray-800 text-gray-800 bg-white rounded hover:bg-slate-300 transition text-center">
                        登録
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\project-manager\resources\views/welcome.blade.php ENDPATH**/ ?>