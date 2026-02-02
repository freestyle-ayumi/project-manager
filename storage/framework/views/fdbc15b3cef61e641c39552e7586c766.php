<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            <?php echo e(__('ダッシュボード')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
    <style>
        @keyframes blink-bg {
            0%, 100% { background-color: rgb(252 165 165, 0.5); } /* 薄赤 */
            50% { background-color: rgb(254 202 202); } /* 濃い赤 */
        }
        .blink-red-bg {
            animation: blink-bg 2s ease-in-out infinite;
        }
    </style>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 1. イベント -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">イベント</h3>
                            <?php if($upcomingProjects->isEmpty()): ?>
                                <p class="text-gray-600">開催前・開催中のイベントはありません。</p>
                            <?php else: ?>
                                <ul class="space-y-1">
                                    <?php $__currentLoopData = $upcomingProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex justify-between items-center bg-gray-50 p-2 rounded-md">
                                            <div class="text-xs">
                                                <a href="<?php echo e(route('projects.show', $project)); ?>" class="text-sm text-blue-600 hover:underline">
                                                    <?php echo e($project->name); ?>

                                                </a>
                                                <p class="text-gray-500">
                                                    <?php echo e(\Carbon\Carbon::parse($project->start_date)->format('m/d')); ?>

                                                    <?php if($project->end_date): ?>
                                                        〜 <?php echo e(\Carbon\Carbon::parse($project->end_date)->format('m/d')); ?>

                                                    <?php endif; ?>
                                                    （<?php echo e($project->client->abbreviation ?? '未設定'); ?>）
                                                </p>
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                <?php echo e($project->start_date > $today ? 'bg-red-200 text-red-800' : 'bg-amber-200 text-amber-800'); ?>">
                                                <?php echo e($project->start_date > $today ? '開催前' : '開催中'); ?>

                                            </span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                            <div class="mt-4 text-right">
                                <a href="<?php echo e(route('projects.index')); ?>" class="text-sm text-indigo-600 hover:underline">すべて見る →</a>
                            </div>
                        </div>

                        <!-- 2. 未承認の経費 -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">未承認の経費</h3>
                            <?php if($pendingExpenses->isEmpty()): ?>
                                <p class="text-gray-600">未承認の経費申請はありません。</p>
                            <?php else: ?>
                                <ul class="space-y-3">
                                    <?php $__currentLoopData = $pendingExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="rounded-md overflow-hidden
                                            <?php echo e($expense->status->name === '差し戻し' ? 'blink-red-bg' : 'bg-gray-50'); ?>">
                                            <a href="<?php echo e(route('expenses.show', $expense)); ?>" class="block p-2 hover:bg-gray-100 transition-colors">
                                                <div class="flex justify-between items-center">
                                                    <div class="text-xs">
                                                        <div class="text-sm text-blue-600">
                                                            <?php echo e($expense->project->name ?? '未設定'); ?> <span class="text-[11px] text-gray-500 font-normal">(<?php echo e(\Carbon\Carbon::parse($expense->date)->format('m/d')); ?>)</span>
                                                        </div>
                                                        <p class="text-gray-500">
                                                            ¥<?php echo e(number_format($expense->amount)); ?>

                                                        </p>
                                                    </div>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                        <?php echo e($expense->status->name === '差し戻し' ? 'bg-red-600 text-white' : 'bg-amber-200 text-amber-800'); ?>">
                                                        <?php echo e($expense->status->name); ?>

                                                        <?php if($expense->status->name === '差し戻し'): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                            <div class="mt-4 text-right">
                                <a href="<?php echo e(route('expenses.index')); ?>" class="text-sm text-indigo-600 hover:underline">すべて見る →</a>
                            </div>
                        </div>

                        <!-- 3. 割り当てられたタスク -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-xs">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">あなたのタスク</h3>
                            <?php if($assignedTasks->isEmpty()): ?>
                                <p class="text-gray-600">現在タスクはありません。</p>
                            <?php else: ?>
                                <ul class="space-y-1">
                                    <?php $__currentLoopData = $assignedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="bg-gray-50 p-2 rounded-md text-xs text-gray-500">

                                            
                                            <p class="flex justify-between items-center">
                                                <?php echo e($task->project->name); ?>

                                                <span class="text-[11px] text-gray-500 font-normal">
                                                    期限 : <?php echo e($task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('m/d') : '未設定'); ?>

                                                </span>
                                            </p>

                                            
                                            <p>
                                                <a href="<?php echo e(route('tasks.show', $task)); ?>" class="text-blue-600 hover:underline block text-sm">
                                                    <?php echo e($task->name); ?>

                                                </a>
                                            </p>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                            <div class="mt-4 text-right">
                                <a href="<?php echo e(route('tasks.index')); ?>" class="text-sm text-indigo-600 hover:underline">すべてのタスク →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/dashboard.blade.php ENDPATH**/ ?>