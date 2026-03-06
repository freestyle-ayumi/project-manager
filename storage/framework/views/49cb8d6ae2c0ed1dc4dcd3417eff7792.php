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
                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2 w-5 h-5 text-gray-400 pointer-events-none" width="16px" height="16px" viewBox="0 0 24 24">
                                    <g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M10.5 2a8.5 8.5 0 1 0 5.262 15.176l3.652 3.652a1 1 0 0 0 1.414-1.414l-3.652-3.652A8.5 8.5 0 0 0 10.5 2M4 10.5a6.5 6.5 0 1 1 13 0a6.5 6.5 0 0 1-13 0"/></g>
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                    <g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M5 3a2 2 0 0 0-2 2v4.129a1.5 1.5 0 0 0-.861 1.665l1.72 8.598A2 2 0 0 0 5.819 21H18.18a2 2 0 0 0 1.961-1.608l1.72-8.598A1.5 1.5 0 0 0 21 9.13V7.5a2 2 0 0 0-2-2h-6.52l-1.399-1.75A2 2 0 0 0 9.52 3zm14.78 8H4.22l1.6 8h12.36zM5 9h14V7.5h-6.52a2 2 0 0 1-1.561-.75L9.519 5H5z"/></g>
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                    <g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M5 3a2 2 0 0 0-2 2v4.129a1.5 1.5 0 0 0-.861 1.665l1.72 8.598A2 2 0 0 0 5.819 21H18.18a2 2 0 0 0 1.961-1.608l1.72-8.598A1.5 1.5 0 0 0 21 9.13V7.5a2 2 0 0 0-2-2h-6.52l-1.399-1.75A2 2 0 0 0 9.52 3zm14.78 8H4.22l1.6 8h12.36zM5 9h14V7.5h-6.52a2 2 0 0 1-1.561-.75L9.519 5H5z"/></g>
                                                </svg>
                                            </a>
                                            <a href="<?php echo e(route('deliveries.edit', $delivery)); ?>" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                    <g fill="none"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M13.896 3.03a2 2 0 0 1 2.829 0l4.242 4.242a2 2 0 0 1 0 2.83L10.653 20.415a2 2 0 0 1-1.414.586H3.996a1 1 0 0 1-1-1v-5.243a2 2 0 0 1 .586-1.414zM17 17a1 1 0 0 1 .946.677c.06.177.2.316.377.377a1 1 0 0 1 0 1.892a.6.6 0 0 0-.377.377a1 1 0 0 1-1.892 0a.6.6 0 0 0-.377-.377a1 1 0 0 1 0-1.892c.177-.06.316-.2.377-.377l.062-.146A1 1 0 0 1 17 17M13.584 6.17l4.243 4.243l1.726-1.726l-4.243-4.243zM5 0a1 1 0 0 1 .946.677l.13.378c.3.879.99 1.57 1.87 1.87l.377.129a1 1 0 0 1 0 1.892l-.378.13c-.879.3-1.57.99-1.87 1.87l-.129.377a1 1 0 0 1-1.892 0l-.13-.378a3 3 0 0 0-1.87-1.87l-.377-.129a1 1 0 0 1 0-1.892l.378-.13c.879-.3 1.57-.99 1.87-1.87l.129-.377C4.222.285 4.552 0 5 0m0 3.196A5 5 0 0 1 4.196 4q.449.355.804.803q.356-.447.803-.803A5 5 0 0 1 5 3.196m-.004 15.805H9.24l7.174-7.174l-4.243-4.243l-7.174 7.174z"/></g>
                                                </svg>
                                            </a>
                                            <form action="<?php echo e(route('deliveries.destroy', $delivery)); ?>" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('この納入書を削除します。よろしいですか？');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                        <g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07L4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z"/></g>
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