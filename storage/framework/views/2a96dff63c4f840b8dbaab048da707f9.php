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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(isset($user) ? $user->name . ' の勤怠log' : '勤怠log'); ?>

            </h2>
            <a href="<?php echo e(route('admin.summary.index')); ?>" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded text-xs">
                勤務集計に戻る
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500">
                            <th class="px-2 py-1 uppercase tracking-wider">日時</th>
                            <?php if (! (isset($user))): ?>
                                <th class="px-2 py-1 uppercase tracking-wider">氏名</th>
                            <?php endif; ?>
                            <th class="px-2 py-1 uppercase tracking-wider text-center">状態</th>
                            <th class="px-2 py-1 uppercase tracking-wider">種別</th>
                            <th class="px-2 py-1 uppercase tracking-wider">場所</th>
                            <th class="px-2 py-1 uppercase tracking-wider">備考</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-gray-500 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <!-- 日時 -->
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <?php echo e($log->created_at->format('m/d')); ?>

                                    <?php
                                        $days = ['日', '月', '火', '水', '木', '金', '土'];
                                        $dayNum = $log->created_at->format('w'); // 0 (日) から 6 (土) を取得
                                    ?>
                                    <span class="<?php echo e($dayNum == 0 ? 'text-red-500' : ($dayNum == 6 ? 'text-blue-500' : 'text-gray-500')); ?>">
                                        (<?php echo e($days[$dayNum]); ?>)
                                    </span>
                                    <?php echo e($log->created_at->format('H:i:s')); ?>

                                </td>
                                <!-- 氏名 -->
                                <?php if (! (isset($user))): ?>
                                    <td class="px-2 py-1 whitespace-nowrap text-blue-600">
                                        <a href="<?php echo e(route('admin.attendance.log.show', $log->user_id)); ?>" class="hover:underline">
                                            <?php echo e($log->user->name ?? '不明'); ?>

                                        </a>
                                    </td>
                                <?php endif; ?>
                                <!-- 状態 -->
                                <td class="px-2 py-1 whitespace-nowrap text-center">
                                    <?php if($log->is_valid): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-green-100 text-green-800">
                                            有効
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-sm bg-red-100 text-red-800">
                                            無効
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <!-- 種別 -->
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <?php
                                        $typeLabels = [
                                            'check_in'            => '出勤',
                                            'check_out'           => '退勤',
                                            'break_start'         => '中抜け',
                                            'break_end'           => '戻り',
                                            'business_trip_start' => '出張開始',
                                            'business_trip_end'   => '出張終了',
                                        ];
                                    ?>

                                    <div class="flex items-center">
                                        
                                        <?php if(str_contains($log->type, 'business_trip')): ?>
                                            <span class="mr-1 text-purple-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="M0 0h24v24H0z"/><path fill="currentColor" d="M12.868 5a3 3 0 0 1 2.572 1.457l2.167 3.611l2.641.33A2 2 0 0 1 22 12.383V15a3 3 0 0 1-2.128 2.872A3.001 3.001 0 0 1 14.17 18H9.829a3.001 3.001 0 0 1-5.7-.128A3 3 0 0 1 2 15v-3.807a2 2 0 0 1 .143-.743l1.426-3.564A3 3 0 0 1 6.354 5zM7 16a1 1 0 1 0 0 2a1 1 0 0 0 0-2m10 0a1 1 0 1 0 0 2a1 1 0 0 0 0-2m-4.132-9H11v3h4.234l-1.509-2.514A1 1 0 0 0 12.868 7M9 7H6.354a1 1 0 0 0-.928.629L4.477 10H9z"/></g></svg>
                                            </span>
                                        <?php endif; ?>

                                        
                                        <span><?php echo e($typeLabels[$log->type] ?? $log->type); ?></span>
                                    </div>
                                </td>
                                <!-- 場所 -->
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <?php if(str_contains($log->type, 'business_trip') || !$log->is_valid): ?>
                                        
                                        <?php if($log->latitude && $log->longitude): ?>
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo e($log->latitude); ?>,<?php echo e($log->longitude); ?>" 
                                                target="_blank" 
                                                class="<?php echo e($log->is_valid ? 'text-blue-600' : 'text-red-600 font-bold'); ?> underline underline-offset-2 flex items-center hover:opacity-70 transition-opacity duration-200">
                                                
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" class="mr-1 flex-shrink-0">
                                                    <g fill="none" fill-rule="evenodd">
                                                        <path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/>
                                                        <path fill="currentColor" d="M12 2a9 9 0 0 1 9 9c0 3.074-1.676 5.59-3.442 7.395a20.4 20.4 0 0 1-2.876 2.416l-.426.29l-.2.133l-.377.24l-.336.205l-.416.242a1.87 1.87 0 0 1-1.854 0l-.416-.242l-.52-.32l-.192-.125l-.41-.273a20.6 20.6 0 0 1-3.093-2.566C4.676 16.589 3 14.074 3 11a9 9 0 0 1 9-9m0 2a7 7 0 0 0-7 7c0 2.322 1.272 4.36 2.871 5.996a18 18 0 0 0 2.222 1.91l.458.326q.222.155.427.288l.39.25l.343.209l.289.169l.455-.269l.367-.23q.293-.186.627-.417l.458-.326a18 18 0 0 0 2.222-1.91C17.728 15.361 19 13.322 19 11a7 7 0 0 0-7-7m0 3a4 4 0 1 1 0 8a4 4 0 0 1 0-8m0 2a2 2 0 1 0 0 4a2 2 0 0 0 0-4"/>
                                                    </g>
                                                </svg>
                                                <span>
                                                    <?php if(!$log->is_valid): ?> [Error地点] <?php endif; ?>
                                                    <?php echo e($log->note ?? ($log->is_business_trip ? '出張先' : ($log->location->name ?? '地点不明'))); ?> 
                                                    (<?php echo e(round($log->latitude, 4)); ?>, <?php echo e(round($log->longitude, 4)); ?>)
                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span class="<?php echo e($log->is_valid ? '' : 'text-red-500'); ?>">
                                                <?php echo e($log->note ?? '位置情報なし'); ?>

                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        
                                        <?php echo e($log->location->name ?? $log->location_id ?? '-'); ?>

                                    <?php endif; ?>
                                </td>
                                <!-- 備考 -->
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <?php if(str_contains($log->note, '手入力')): ?>
                                        <span class="text-red-500 flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                <g fill="none"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="m13.414 2.808l7.778 7.778a2 2 0 0 1 0 2.829l-7.778 7.778a2 2 0 0 1-2.828 0l-7.778-7.778a2 2 0 0 1 0-2.829l7.778-7.778a2 2 0 0 1 2.828 0M12 4.222L4.222 12L12 19.78L19.778 12zM12.002 15a1 1 0 0 1 .117 1.993l-.117.007a1 1 0 0 1-.119-1.993zM12 8c.867 0 1.538.76 1.43 1.62l-.438 3.504a1 1 0 0 1-1.984 0L10.57 9.62A1.44 1.44 0 0 1 12 8"/></g>
                                            </svg>
                                            手入力
                                        </span>
                                    <?php else: ?>
                                        
                                        <?php echo e($log->note); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-2 py-1 text-center text-gray-500">ログがありません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div>
                    <?php echo e($logs->links()); ?>

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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/admin/attendance/log.blade.php ENDPATH**/ ?>