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
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">タスク詳細</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-2 sm:py-4 max-w-4xl mx-auto px-1 sm:px-6 lg:px-8">
        <!-- 上部ボタン -->
        <div class="mb-2 sm:mb-4 flex justify-end space-x-2">
            <a href="<?php echo e(route('tasks.edit', $task->id)); ?>" 
            class="inline-flex items-center px-2 pr-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                編集
            </a>
            <a href="<?php echo e(route('tasks.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm">
                一覧に戻る
            </a>
        </div>

        <!-- メインカード -->
        <div class="bg-white shadow-sm rounded-xl p-6 pt-5 ">
            <!-- イベント -->
            <div class="text-sm text-gray-500 mb-1">
                <?php echo e($task->project->name ?? 'N/A'); ?>

            </div>

            
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo e($task->name); ?></h1>

                    
                    <?php if(!empty($task->due_date)): ?>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                            </svg>
                            <span><?php echo e(\Carbon\Carbon::parse($task->due_date)->format('y.m/d')); ?></span>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(!empty($task->start_time)): ?>
                        <div class="flex items-center text-sm text-gray-600 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6v6l4 2m6 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                            </svg>
                            <span><?php echo e(\Carbon\Carbon::parse($task->start_time)->format('H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-3 text-sm text-gray-700">
                    
                    <span>依頼：<?php echo e($task->creator->name); ?></span>

                    
                    <span>依頼日：<?php echo e(\Carbon\Carbon::parse($task->start_date ?? now())->format('y.m/d')); ?></span>

                    
                    <?php
                        $statusClass = match($task->status) {
                            '完了'   => 'bg-green-300 text-stone-800',
                            '修正'   => 'bg-amber-500 text-white',
                            '無効'   => 'bg-gray-500 text-white',
                            default  => 'bg-red-500 text-white',
                        };

                        $statusOrder = ['未完了', '完了', '修正']; // 次ステータスの順序
                        $currentIndex = array_search($task->status, $statusOrder);
                        $nextStatus = $statusOrder[($currentIndex + 1) % count($statusOrder)];
                    ?>

                    <span 
                        id="task-status" 
                        class="px-2 py-1 rounded <?php echo e($statusClass); ?> cursor-pointer"
                        data-task-id="<?php echo e($task->id); ?>"
                        data-next-status="<?php echo e($nextStatus); ?>"
                    >
                        <?php echo e($task->status); ?>

                    </span>


                    
                    <span class="px-2 py-1 bg-gray-200 rounded"><?php echo e($task->priority ?? '-'); ?></span>
                </div>
            </div>


            <hr class="mb-5 border-gray-200">

            <!-- 詳細 -->
            <p class="text-gray-800 text-base leading-relaxed whitespace-pre-line">
                <?php echo e($task->description ?? '詳細情報は登録されていません。'); ?>

            </p>

            <!-- 関連URL -->
            <?php if (! ($task->urls->isEmpty())): ?>
            <div>
                <ul class="space-y-1 mt-1">
                    <?php $__currentLoopData = $task->urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="group flex items-center gap-1 bg-white rounded-md hover:border-indigo-300 hover:shadow-sm transition-all">
                            <!-- リンクマーク（先頭） -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>

                            <!-- クリック可能なURL（別ウィンドウ） -->
                            <a href="<?php echo e($url->url); ?>" target="_blank" rel="noopener noreferrer"
                            class="text-indigo-600 hover:text-indigo-800 flex-1 break-all text-xs">
                                <?php echo e($url->title ?: $url->url); ?>

                            </a>

                            <!-- メモ（存在する場合） -->
                            <?php if($url->memo): ?>
                                <span class="text-xs text-gray-500 italic ml-auto">
                                    <?php echo e($url->memo); ?>

                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

            <hr class="mt-3 mb-3 border-gray-200">
            <!-- 担当者 -->
            <div class="flex items-center space-x-2 mb-3">
                <strong class="text-gray-500 text-xs">担当者：</strong>
                <?php $__empty_1 = true; $__currentLoopData = $task->assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="inline-block bg-gray-100 px-2 py-0.5 rounded text-gray-700 text-sm"><?php echo e($assignee->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span class="text-gray-700 text-sm">なし</span>
                <?php endif; ?>
            </div>


            <!-- 完了予定日と期日 -->
            <div class="grid grid-cols-2 gap-2 text-sm text-gray-800 mb-2">
                <div>
                    <strong class="block text-gray-500 text-xs">完了予定日</strong>
                    <span><?php echo e(\Carbon\Carbon::parse($task->plans_date ?? now())->format('y/m/d')); ?></span>
                </div>
                <div>
                    <strong class="block text-gray-500 text-xs">期日</strong>
                    <span>
                        <?php echo e($task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('y/m/d') : '-'); ?>

                    </span>
                </div>

            </div>


        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusEl = document.getElementById('task-status');

            statusEl.addEventListener('click', function () {
                const taskId = this.dataset.taskId;
                const currentStatus = this.textContent.trim();
                const nextStatus = this.dataset.nextStatus;

                if (!confirm(`ステータスを「${currentStatus}」→「${nextStatus}」に変更します。よろしいですか？`)) {
                    return;
                }

                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ status: nextStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        statusEl.textContent = data.newStatus;

                        // クラスも更新
                        if (data.newStatus === '未完了') {
                            statusEl.className = 'px-2 py-1 rounded bg-red-500 text-white cursor-pointer';
                            statusEl.dataset.nextStatus = '完了';
                        } else if (data.newStatus === '完了') {
                            statusEl.className = 'px-2 py-1 rounded bg-green-500 text-white cursor-pointer';
                            statusEl.dataset.nextStatus = '未完了';
                        }
                    }
                })
                .catch(err => {
                    alert('ステータス更新に失敗しました');
                    console.error(err);
                });
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\project-manager\resources\views/tasks/show.blade.php ENDPATH**/ ?>