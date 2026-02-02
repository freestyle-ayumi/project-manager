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
            <?php echo e(__('納入書一覧')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">
                    <?php if(session('success')): ?>
                    <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded text-sm">
                        <?php echo e(session('success')); ?>

                    </div>
                    <?php endif; ?>

                    
                    <form action="<?php echo e(route('deliveries.index')); ?>" method="GET" class="mb-2 p-2 rounded-md shadow-sm bg-white">
                        <div class="grid grid-cols-12 gap-2">
                            <!-- 検索入力 -->
                            <div class="col-span-12 sm:col-span-6 md:col-span-9 relative flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="absolute left-2 w-5 h-5 text-gray-400 pointer-events-none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>

                                <input type="text" name="search" value="<?php echo e($search); ?>"
                                    placeholder="納入番号・件名・顧客名・イベント名 など"
                                    class="w-full h-8 pl-8 py-0 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>

                            <!-- イベントフィルター -->
                            <select name="project_filter"
                                class="mt-0.5 col-span-12 sm:col-span-3 md:col-span-1 block w-full py-0 border border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-xs">
                                <option value="all" <?php echo e(($projectFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>すべて</option>
                                <option value="before" <?php echo e(($projectFilter ?? '') === 'before' ? 'selected' : ''); ?>>開催前</option>
                                <option value="current" <?php echo e(($projectFilter ?? '') === 'current' ? 'selected' : ''); ?>>開催中</option>
                                <option value="past" <?php echo e(($projectFilter ?? '') === 'past' ? 'selected' : ''); ?>>終了</option>
                            </select>

                            <!-- ボタン -->
                            <div class="col-span-12 sm:col-span-3 md:col-span-2 grid grid-cols-2 md:flex space-x-2 mt-0.5">
                                <button type="submit"
                                    class="h-8 w-full flex rounded-md pt-2 pb-1.5 items-center justify-center text-white text-xs bg-indigo-600 hover:bg-indigo-700">
                                    検索
                                </button>
                                <a href="<?php echo e(route('deliveries.index')); ?>"
                                    class="h-8 w-full flex rounded-md pt-2 pb-1.5 mr-5 sm:mr-0 items-center justify-center text-white text-xs bg-gray-400 hover:bg-gray-500">
                                    クリア
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="mb-2 text-right">
                        <a href="<?php echo e(route('deliveries.create')); ?>" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ＋新規
                        </a>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs font-medium text-gray-600 text-center">
                                <tr>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">納入番号</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-4/12">イベント名</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">顧客</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-3/12">件名</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">納入予定 / 場所</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">支払条件</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">合計金額</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">ステータス</th>
                                    <th class="px-2 py-2 whitespace-nowrap w-1/12">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs text-gray-500" style="height:10px;">
                                <?php $__currentLoopData = $deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-2 py-1 whitespace-nowrap text-center">
                                        <a href="<?php echo e(route('deliveries.show', $delivery)); ?>" class="text-blue-600 hover:text-fuchsia-600 hover:underline">
                                            <?php echo e($delivery->delivery_number); ?>

                                        </a>
                                    </td>
                                    <td class="px-2 py-1">
                                        <?php echo e($delivery->project->name ?? 'N/A'); ?>

                                        <?php if($delivery->project): ?>
                                            <a href="<?php echo e(route('projects.show', $delivery->project)); ?>" class="text-blue-600 hover:text-fuchsia-600 ml-1 inline-block align-middle" title="イベント詳細へ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-center">
                                        <?php echo e($delivery->client->abbreviation ?? 'N/A'); ?>

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <?php echo e($delivery->subject); ?>

                                    </td>
                                    <td class="p-1 whitespace-nowrap text-center">
                                        <?php echo e($delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('Y/m/d') : '未設定'); ?>　<?php echo e($delivery->delivery_location ?? '未設定'); ?>

                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-center">
                                        <?php echo e($delivery->payment_terms ?? '未設定'); ?>

                                    </td>
                                    <td class="px-2 py-1 sm:pr-3 whitespace-nowrap text-right">
                                        ¥<span class="text-sm"><?php echo e(number_format($delivery->total_amount)); ?></span>
                                    </td>
                                    <!-- ステータス（見積書と同じ実装） -->
                                    <td class="px-2 py-1 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center justify-center gap-1 text-xs">
                                            <button type="button" 
                                                    class="status-button px-2 py-1 rounded-full cursor-pointer whitespace-nowrap
                                                        <?php echo e($delivery->status === '作成済み' ? 'bg-gray-200 text-gray-800' : ''); ?>

                                                        <?php echo e($delivery->status === '発行済み' ? 'bg-blue-200 text-blue-800' : ''); ?>

                                                        <?php echo e($delivery->status === '送信済み' ? 'bg-green-200 text-green-800' : ''); ?>"
                                                    data-delivery-id="<?php echo e($delivery->id); ?>"
                                                    data-current-status="<?php echo e($delivery->status); ?>"
                                                    <?php if($delivery->status === '送信済み'): ?> disabled <?php endif; ?>>
                                                <?php echo e($delivery->status); ?>

                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <a href="<?php echo e(route('deliveries.show', $delivery)); ?>" class="text-blue-600 hover:text-blue-400" title="詳細">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                                                </svg>
                                            </a>
                                            <a href="<?php echo e(route('deliveries.edit', $delivery)); ?>" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
                                                </svg>
                                            </a>
                                            <form action="<?php echo e(route('deliveries.destroy', $delivery)); ?>" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('この納入書を削除します。よろしいですか？');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 確認モーダル -->
    <div id="statusConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4">ステータス変更確認</h3>
            <p id="confirmMessage" class="text-sm mb-6"></p>
            <div class="flex justify-end gap-3 text-xs">
                <button id="confirmCancel" class="px-3 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">キャンセル</button>
                <button id="confirmOK" class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">OK</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('statusConfirmModal');
        const message = document.getElementById('confirmMessage');
        const cancelBtn = document.getElementById('confirmCancel');
        const okBtn = document.getElementById('confirmOK');

        let currentButton = null;
        let currentDeliveryId = null;

        document.querySelectorAll('.status-button').forEach(button => {
            button.addEventListener('click', function() {
                if (this.disabled) return;

                currentButton = this;
                currentDeliveryId = this.dataset.deliveryId;
                const currentStatus = this.dataset.currentStatus;

                let nextStatus = '';
                if (currentStatus === '作成済み') nextStatus = '発行済み';
                else if (currentStatus === '発行済み') nextStatus = '送信済み';

                message.textContent = `ステータスを「${currentStatus}」→「${nextStatus}」に変更します。よろしいですか？`;
                modal.classList.remove('hidden');
            });
        });

        cancelBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        okBtn.addEventListener('click', () => {
            modal.classList.add('hidden');

            fetch(`/deliveries/${currentDeliveryId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                currentButton.textContent = data.status;
                currentButton.dataset.currentStatus = data.status;

                currentButton.className = 'status-button px-4 py-1 text-xs font-medium rounded-full cursor-pointer whitespace-nowrap ' +
                    (data.status === '作成済み' ? 'bg-gray-200 text-gray-800' :
                     data.status === '発行済み' ? 'bg-blue-200 text-blue-800' :
                     'bg-green-200 text-green-800');

                if (data.status === '送信済み') {
                    currentButton.disabled = true;
                    const arrow = currentButton.parentElement.querySelector('span');
                }
            })
            .catch(err => {
                console.error(err);
                alert('更新に失敗しました');
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/deliveries/index.blade.php ENDPATH**/ ?>