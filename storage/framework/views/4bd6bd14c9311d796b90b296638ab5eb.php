<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e(config('app.name', 'Freestyle_Project_manager')); ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Viteアセット読み込み -->
    <?php if(app()->environment('local')): ?>
        <!-- ローカル（開発）：Viteホットリロード（第2引数なし） -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js'], 'build/.vite'); ?>
    <?php else: ?>
        <!-- 本番：ビルド済みアセット -->
        <?php $version = time(); ?>
        <link rel="stylesheet" href="<?php echo e(asset('build/assets/app-dKeCdml8.css')); ?>?v=<?php echo e($version); ?>">
        <script type="module" src="<?php echo e(asset('build/assets/app-DRVRqlT5.js')); ?>?v=<?php echo e($version); ?>" defer></script>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!-- Page Heading -->
            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-3 sm:py-4 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>
            <!-- Page Content -->
            <main>
                <?php echo e($slot ?? ''); ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
        <?php echo $__env->yieldPushContent('scripts'); ?>
        
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    </body>
</html>

<?php /**PATH C:\xampp\htdocs\project-manager\resources\views/layouts/app.blade.php ENDPATH**/ ?>