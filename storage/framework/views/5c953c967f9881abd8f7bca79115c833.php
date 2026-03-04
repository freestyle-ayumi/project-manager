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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            打刻履歴
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    <!-- 月選択フォーム -->
                    <form method="GET" action="<?php echo e(route('attendance.history')); ?>" class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                            <div>
                                <select name="month" id="month" class="block w-full sm:w-48 rounded-md border-gray-300 px-2 py-1 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600">
                                    <?php $__currentLoopData = $months ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e(($selectedMonth ?? '') === $value ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 active:bg-indigo-600 transition">
                                    表示
                                </button>

                                <a href="<?php echo e(route('attendance.history')); ?>" class="inline-flex items-center px-4 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition">
                                    今月
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- テーブル -->
                    <table class="min-w-full divide-y divide-gray-200 text-left text-gray-500 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">日付</th>
                                <th class="w-[28%] px-2 py-1 uppercase tracking-wider">場所</th>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">出社</th>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">中抜け</th>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">戻り</th>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">退社</th>
                                <th class="w-[12%] px-2 py-1 uppercase tracking-wider">勤務</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-xs">
                            <?php $__currentLoopData = $dailyRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateKey => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $carbonDate = \Carbon\Carbon::parse($dateKey);
                                    // 土日の背景色
                                    $rowBg = '';
                                    if($carbonDate->isSaturday()) $rowBg = 'bg-blue-50';
                                    if($carbonDate->isSunday()) $rowBg = 'bg-red-50';
                                ?>
                                <tr class="<?php echo e($rowBg); ?>">
                                    <td class="px-2 py-1 whitespace-nowrap font-medium">
                                        <?php echo e($carbonDate->format('m/d')); ?> 
                                        <span class="text-[10px]"><?php echo e(['日','月','火','水','木','金','土'][$carbonDate->dayOfWeek]); ?></span>
                                    </td>
                                    <td class="px-2 py-1">
                                        <?php if($records['is_business_trip']): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-purple-200 text-[10px] bg-purple-100 text-purple-800 mr-1">出張</span>
                                            <span class="text-gray-600"><?php echo e($records['note'] ?? '出張先未入力'); ?></span>
                                        <?php elseif($records['location']): ?>
                                            <span class="text-gray-600"><?php echo e($records['location']); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-300">---</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['check_in']); ?></td>
                                    <td class="px-2 py-1 whitespace-nowrap text-gray-400"><?php echo e($records['break_start']); ?></td>
                                    <td class="px-2 py-1 whitespace-nowrap text-gray-400"><?php echo e($records['break_end']); ?></td>
                                    <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['check_out']); ?></td>
                                    <td class="px-2 py-1 whitespace-nowrap font-bold text-indigo-600">
                                        <?php echo e($records['work_hours'] != '0:00' ? $records['work_hours'] : '---'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/attendance/history.blade.php ENDPATH**/ ?>