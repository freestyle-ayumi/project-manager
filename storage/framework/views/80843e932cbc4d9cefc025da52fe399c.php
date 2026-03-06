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
            場所管理
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 text-gray-900">
                <!-- 新規追加ボタン -->
                <div class="mb-2 text-right">
                    <a href="<?php echo e(route('admin.locations.create')); ?>" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ＋新規場所
                    </a>
                </div>

                <!-- 一覧テーブル -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-left text-gray-600 text-xs font-medium">
                            <tr>
                                <th class="px-2 py-1 uppercase tracking-wider">場所</th>
                                <th class="px-2 py-1 uppercase tracking-wider">緯度</th>
                                <th class="px-2 py-1 uppercase tracking-wider">経度</th>
                                <th class="px-2 py-1 uppercase tracking-wider">許容距離 (m)</th>
                                <th class="px-2 py-1 uppercase tracking-wider">操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-500 text-xs">
                            <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 py-1 whitespace-nowra">
                                        <?php echo e($location->name); ?>

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <?php echo e(number_format($location->latitude, 6)); ?>

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <?php echo e(number_format($location->longitude, 6)); ?>

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <?php echo e($location->allowed_radius); ?><span class="text-slate-400"> m</span>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap flex items-center gap-x-0.5">
                                        <a href="<?php echo e(route('admin.locations.edit', $location)); ?>" class="text-emerald-600 hover:text-emerald-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                <g fill="none"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M13.896 3.03a2 2 0 0 1 2.829 0l4.242 4.242a2 2 0 0 1 0 2.83L10.653 20.415a2 2 0 0 1-1.414.586H3.996a1 1 0 0 1-1-1v-5.243a2 2 0 0 1 .586-1.414zM17 17a1 1 0 0 1 .946.677c.06.177.2.316.377.377a1 1 0 0 1 0 1.892a.6.6 0 0 0-.377.377a1 1 0 0 1-1.892 0a.6.6 0 0 0-.377-.377a1 1 0 0 1 0-1.892c.177-.06.316-.2.377-.377l.062-.146A1 1 0 0 1 17 17M13.584 6.17l4.243 4.243l1.726-1.726l-4.243-4.243zM5 0a1 1 0 0 1 .946.677l.13.378c.3.879.99 1.57 1.87 1.87l.377.129a1 1 0 0 1 0 1.892l-.378.13c-.879.3-1.57.99-1.87 1.87l-.129.377a1 1 0 0 1-1.892 0l-.13-.378a3 3 0 0 0-1.87-1.87l-.377-.129a1 1 0 0 1 0-1.892l.378-.13c.879-.3 1.57-.99 1.87-1.87l.129-.377C4.222.285 4.552 0 5 0m0 3.196A5 5 0 0 1 4.196 4q.449.355.804.803q.356-.447.803-.803A5 5 0 0 1 5 3.196m-.004 15.805H9.24l7.174-7.174l-4.243-4.243l-7.174 7.174z"/></g>
                                            </svg>
                                        </a>
                                        <form action="<?php echo e(route('admin.locations.destroy', $location)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除" onclick="return confirm('本当に削除しますか？')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                    <g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07L4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z"/></g>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        登録場所がありません
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ページネーション -->
                <div>
                    <?php echo e($locations->links()); ?>

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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/admin/locations/index.blade.php ENDPATH**/ ?>