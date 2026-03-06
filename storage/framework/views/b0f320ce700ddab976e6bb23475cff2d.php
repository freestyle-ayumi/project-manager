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
            <?php echo e(__('イベント管理')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <style>
    /* 日曜日の列 */
    .fc .fc-col-header-cell.fc-day-sun,
    .fc .fc-daygrid-day.fc-day-sun {
        background-color: #fef2f2;
    }

    /* 土曜日の列 */
    .fc .fc-col-header-cell.fc-day-sat,
    .fc .fc-daygrid-day.fc-day-sat {
        background-color: #eff6ff;
    }
    </style>

    <div class="sm:py-2 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                <div class="responsive-padding">
                    <div class="flex flex-col lg:flex-row">
                        <!-- 左カラム: カレンダー -->
                        <div class="lg:w-1/2">
                            <div id="project-calendar"
                                class="bg-white border rounded-md shadow-sm p-4"
                                style="height: 100%; min-height: 700px; overflow: hidden;">
                            </div>
                        </div>

                        <!-- 右カラム: リスト -->
                        <div id="project-table" class="lg:w-1/2 overflow-x-auto">
                            <div class="max-w-7xl max-h-[70vh] mx-auto sm:pl-2">
                                <div class="bg-white border overflow-hidden shadow-sm sm:rounded-lg rounded-md shadow-sm mt-1 sm:mt-0">
                                    <div class="p-1 sm:p-4 sm:pt-3">
                                        <div class="text-right">
                                            <a href="<?php echo e(route('projects.create')); ?>" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <?php echo e(__('＋新規')); ?>

                                            </a>
                                        </div>

                                        
                                        <form action="<?php echo e(route('projects.index')); ?>" method="GET" class="mb-2 py-2 rounded-md shadow-sm bg-white">
                                            <div class="grid grid-cols-12 gap-1">
                                                <!-- キーワード検索 -->
                                                <div class="relative col-span-8">
                                                    <!-- アイコン -->
                                                    <div class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                                        </svg>
                                                    </div>

                                                    <!-- 検索入力 -->
                                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                                                        placeholder="イベント名・顧客名・タスク名 など"
                                                        class="w-full pl-8 border box-border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                                </div>

                                                <!-- ステータスフィルター -->
                                                <select name="status" class="col-span-2 border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                    <option value="" <?php echo e(request('status')=='' ? 'selected' : ''); ?>>すべて</option>
                                                    <option value="upcoming" <?php echo e(request('status')=='upcoming' ? 'selected' : ''); ?>>開催前</option>
                                                    <option value="ongoing" <?php echo e(request('status')=='ongoing' ? 'selected' : ''); ?>>開催中</option>
                                                    <option value="finished" <?php echo e(request('status')=='finished' ? 'selected' : ''); ?>>終了</option>
                                                </select>

                                                <!-- 検索ボタン -->
                                                <button type="submit"
                                                        class="col-span-1 rounded-md bg-indigo-500 hover:bg-indigo-300 text-white text-xs text-center transition">
                                                    検索
                                                </button>

                                                <!-- クリアボタン -->
                                                <a href="<?php echo e(route('projects.index')); ?>"
                                                        class="col-span-1 rounded-md bg-gray-400 hover:bg-gray-500 text-white text-xs text-center transition text-center flex items-center justify-center transition">
                                                    クリア
                                                </a>
                                                
                                            </div>
                                        </form>

                                        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                                            <?php if($projects->isEmpty()): ?>
                                                <p class="mx-6">該当イベントが存在しません。</p>
                                            <?php else: ?>
                                                <table class="min-w-full divide-y divide-gray-200 text-xs border-r border-gray-200">
                                                    <thead class="bg-gray-50 text-center">
                                                        <tr>
                                                            <th scope="col" class="whitespace-nowrap w-14">開始</th>
                                                            <th scope="col" class="whitespace-nowrap w-14 border-x border-gray-200">終了</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">イベント名</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">顧客</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">担当</th>
                                                            <th scope="col" class="whitespace-nowrap w-14">ステータス</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">タスク</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap w-20">見積</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap w-20">納品</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap w-20">請求</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">経費合計</th>
                                                            <th scope="col" class="px-1 py-2 whitespace-nowrap">実利</th>
                                                            <th scope="col" class="relative px-1 py-2 whitespace-nowrap border-x border-gray-200">操作</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="hover:bg-gray-50 text-gray-500 text-xs">
                                                                <td class="px-1 py-0.5 whitespace-nowrap bg-red-100 text-center">
                                                                    <?php echo e($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('y/m/d') : 'N/A'); ?>

                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap bg-lime-200 text-center border-x border-gray-200">
                                                                    
                                                                    <?php echo e($project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('y/m/d') : '-'); ?>

                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap">
                                                                    
                                                                    <a href="<?php echo e(route('projects.show', $project)); ?>" 
                                                                        class="text-blue-600 hover:text-fuchsia-600 hover:underline block"
                                                                        title="<?php echo e($project->name); ?> (<?php echo e($project->client->abbreviation ?? 'N/A'); ?>)">
                                                                        <?php echo e(Str::limit($project->name, 42, '...')); ?>

                                                                    </a>
                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap">
                                                                    
                                                                    <?php echo e($project->client->abbreviation ?? 'N/A'); ?>

                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap" style="font-size:0.7rem;">
                                                                    
                                                                    <?php if($project->users && $project->users->count() > 0): ?>
                                                                        <?php echo e($project->users->pluck('name')->join('、')); ?>

                                                                    <?php else: ?>
                                                                        －
                                                                    <?php endif; ?>
                                                                </td>
                                                                
                                                                <td class="px-0.5 whitespace-nowrap text-center w-12">
                                                                    <?php if($project->status === 'before'): ?>
                                                                        <span class="px-2 py-0.5 inline-flex leading-4 rounded-full bg-red-200 text-red-700">開催前</span>
                                                                    <?php elseif($project->status === 'progress'): ?>
                                                                        <span class="px-2 py-0.5 inline-flex leading-4 rounded-full bg-amber-200 text-amber-700">開催中</span>
                                                                    <?php else: ?>
                                                                        <span class="px-2 py-0.5 inline-flex leading-4 rounded-full bg-slate-200 text-slate-700">終了</span>
                                                                    <?php endif; ?>
                                                                </td>

                                                                <td class="px-1 py-0.5 whitespace-nowrap text-center">
                                                                     
                                                                    <?php if($project->tasks->count() > 0): ?>
                                                                        <div class="inline-flex items-center space-x-1">
                                                                            <a href="<?php echo e(route('projects.show', $project)); ?>" class="text-blue-500 hover:text-blue-700">
                                                                                <?php echo e($project->tasks->count()); ?> 件
                                                                            </a>

                                                                            <!-- ▼/▲ボタン -->
                                                                            <button type="button" class="text-gray-400 hover:text-gray-600" 
                                                                                    onclick="toggleTasks(<?php echo e($project->id); ?>, this)">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                                                </svg>
                                                                            </button>
                                                                        </div>

                                                                        <!-- アコーディオン部分 -->
                                                                        <div id="tasks-<?php echo e($project->id); ?>" class="hidden text-left text-xs bg-gray-50 hover:bg-blue-100">
                                                                            <ul class="list-disc list-inside">
                                                                                <?php $__currentLoopData = $project->tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <li>
                                                                                        <a href="<?php echo e(route('tasks.show', $task)); ?>" class="text-blue-500 hover:text-blue-700 hover:underline">
                                                                                            <?php echo e($task->name); ?>

                                                                                        </a>
                                                                                    </li>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            </ul>
                                                                        </div>

                                                                        <script>
                                                                        function toggleTasks(id, btn) {
                                                                            const taskDiv = document.getElementById('tasks-' + id);
                                                                            const isHidden = taskDiv.classList.toggle('hidden');
                                                                            // SVGの切り替え
                                                                            btn.innerHTML = isHidden
                                                                                ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                                                </svg>` // 閉じている時
                                                                                : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                                                                </svg>` // 開いている時
                                                                        }
                                                                        </script>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>

                                                                <?php
                                                                    // 最新見積書を取得（存在しなければ null）
                                                                    $latestQuote = $latestQuotes[$project->id] ?? null;

                                                                    // PDF出力済みかどうか（quote_logsテーブルに「PDF出力」アクションがあれば true）
                                                                    $hasPdf = $latestQuote 
                                                                        && $latestQuote->logs()->where('action', 'like', '%PDF出力%')->exists();
                                                                ?>

                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    <?php
                                                                        $latestQuote = $latestQuotes[$project->id] ?? null;
                                                                        $hasPdf = $latestQuote && $latestQuote->pdf_path;
                                                                        $statusChar = '';
                                                                        if ($latestQuote) {
                                                                            $statusChar = mb_substr($latestQuote->status, 0, 1);  // 1文字
                                                                        }
                                                                    ?>
                                                                    <?php if($latestQuote): ?>
                                                                        <div class="inline-flex items-center justify-end gap-1">
                                                                            <!-- 金額 -->
                                                                            <a href="<?php echo e(route('quotes.show', $latestQuote->id)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                                                ¥ <?php echo e(number_format($latestQuote->total_amount)); ?>

                                                                            </a>

                                                                            <!-- PDFアイコン -->
                                                                            <a href="<?php echo e(route('quotes.downloadPdfMpdf', $latestQuote->id)); ?>" target="_blank"
                                                                            class="<?php echo e($hasPdf ? 'text-red-600 hover:text-red-800' : 'text-gray-400 hover:text-gray-600'); ?> generate-pdf"
                                                                            title="<?php echo e($hasPdf ? 'PDFダウンロード' : 'PDF生成'); ?>">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27c.146.23.308.535.49.875c.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                                                </svg>
                                                                            </a>

                                                                            <!-- ステータス1文字 -->
                                                                            <?php if($statusChar): ?>
                                                                                <span class="px-0.5 py-0 text-xs font-bold rounded
                                                                                    <?php echo e($latestQuote->status === '作成済み' ? 'bg-green-200 text-green-800' : ''); ?>

                                                                                    <?php echo e($latestQuote->status === '発行済み' ? 'bg-amber-300 text-amber-800' : ''); ?>

                                                                                    <?php echo e($latestQuote->status === '送信済み' ? 'bg-gray-200 text-gray-800' : ''); ?>">
                                                                                    <?php echo e($statusChar); ?>

                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <!-- 未作成 -->
                                                                        <a href="<?php echo e(route('quotes.create', ['project_id' => $project->id])); ?>" 
                                                                            class="text-green-600 hover:text-green-800" title="見積書を作成">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                                                            </svg>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </td>

                                                                
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    <?php
                                                                        $latestDelivery = $latestDeliveries[$project->id] ?? null;
                                                                        $hasDeliveryPdf = $latestDelivery && $latestDelivery->pdf_path;
                                                                        $deliveryStatusChar = '';
                                                                        if ($latestDelivery) {
                                                                            $deliveryStatusChar = mb_substr($latestDelivery->status, 0, 1);
                                                                        }
                                                                    ?>
                                                                    <?php if($latestDelivery): ?>
                                                                        <div class="inline-flex items-center justify-end gap-1">
                                                                            <!-- 金額 -->
                                                                            <a href="<?php echo e(route('deliveries.show', $latestDelivery->id)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                                                ¥ <?php echo e(number_format($project->deliveries_sum_total_amount)); ?>

                                                                            </a>

                                                                            <!-- PDFアイコン -->
                                                                            <a href="<?php echo e(route('deliveries.downloadPdfMpdf', $latestDelivery->id)); ?>" target="_blank"
                                                                            class="<?php echo e($hasDeliveryPdf ? 'text-red-600 hover:text-red-800' : 'text-gray-400 hover:text-gray-600'); ?> generate-pdf"
                                                                            title="<?php echo e($hasDeliveryPdf ? 'PDFダウンロード' : '納品書PDF未出力（クリックで生成）'); ?>">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27c.146.23.308.535.49.875c.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                                                </svg>
                                                                            </a>

                                                                            <!-- ステータス1文字 -->
                                                                            <?php if($deliveryStatusChar): ?>
                                                                                <span class="px-0.5 py-0 text-xs font-bold rounded
                                                                                    <?php echo e($latestDelivery->status === '作成済み' ? 'bg-green-200 text-green-800' : ''); ?>

                                                                                    <?php echo e($latestDelivery->status === '発行済み' ? 'bg-amber-300 text-amber-800' : ''); ?>

                                                                                    <?php echo e($latestDelivery->status === '送信済み' ? 'bg-gray-200 text-gray-800' : ''); ?>">
                                                                                    <?php echo e($deliveryStatusChar); ?>

                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <!-- 納品書未作成 -->
                                                                        <a href="<?php echo e(route('deliveries.create', ['project_id' => $project->id])); ?>" 
                                                                        class="text-green-600 hover:text-green-800" title="納品書を作成">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                                                            </svg>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </td>
                                                                
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    <?php
                                                                        $latestInvoice = $latestInvoices[$project->id] ?? null;
                                                                        $hasInvoicePdf = $latestInvoice && $latestInvoice->pdf_path;
                                                                        $invoiceStatusChar = $latestInvoice ? mb_substr($latestInvoice->status, 0, 1) : '';
                                                                    ?>
                                                                    <?php if($latestInvoice): ?>
                                                                        <div class="inline-flex items-center justify-end gap-1">
                                                                            <!-- 金額 -->
                                                                            <a href="<?php echo e(route('invoices.show', $latestInvoice->id)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                                                ¥ <?php echo e(number_format($project->invoices_sum_total_amount)); ?>

                                                                            </a>

                                                                            <!-- PDFアイコン -->
                                                                            <a href="<?php echo e(route('invoices.downloadPdfMpdf', $latestInvoice->id)); ?>" target="_blank"
                                                                            class="<?php echo e($hasInvoicePdf ? 'text-red-600 hover:text-red-800' : 'text-gray-400 hover:text-gray-600'); ?> generate-pdf"
                                                                            title="<?php echo e($hasInvoicePdf ? 'PDFダウンロード' : '請求書PDF未出力（クリックで生成）'); ?>">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27c.146.23.308.535.49.875c.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                                                </svg>
                                                                            </a>

                                                                            <!-- ステータス1文字 -->
                                                                            <?php if($invoiceStatusChar): ?>
                                                                                <span class="px-0.5 py-0 text-xs font-bold rounded
                                                                                    <?php echo e($latestInvoice->status === '作成済み' ? 'bg-green-200 text-green-800' : ''); ?>

                                                                                    <?php echo e($latestInvoice->status === '発行済み' ? 'bg-amber-300 text-amber-800' : ''); ?>

                                                                                    <?php echo e($latestInvoice->status === '送信済み' ? 'bg-gray-200 text-gray-800' : ''); ?>">
                                                                                    <?php echo e($invoiceStatusChar); ?>

                                                                                </span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <!-- 未作成 -->
                                                                        <a href="<?php echo e(route('invoices.create', ['project_id' => $project->id])); ?>" 
                                                                        class="text-green-600 hover:text-green-800" title="請求書を作成">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                                                <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                                                            </svg>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </td>
                                                                
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    ¥<?php echo e(number_format($project->total_approved_expenses_sum ?? 0)); ?>

                                                                </td>
                                                                
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    <?php
                                                                        $netProfit = ($project->invoices_sum_total_amount ?? 0) - ($project->total_approved_expenses_sum ?? 0);
                                                                    ?>
                                                                    ¥<?php echo e(number_format($netProfit)); ?>

                                                                </td>
                                                                
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right font-medium border-x border-gray-200 w-10">
                                                                    <div class="flex items-center gap-x-0.5">
                                                                        
                                                                        <a href="<?php echo e(route('projects.show', $project)); ?>" class="text-blue-600 hover:text-blue-400" title="詳細">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right mr-0.5" viewBox="0 0 17 15">
                                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                                            </svg>
                                                                        </a>

                                                                        
                                                                        <a href="<?php echo e(route('projects.edit', $project)); ?>" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                            </svg>
                                                                        </a>

                                                                        
                                                                        <form action="<?php echo e(route('projects.destroy', $project)); ?>" method="POST" class="inline-block" onsubmit="return confirm('本当にこのイベントを削除しますか？');">
                                                                            <?php echo csrf_field(); ?>
                                                                            <?php echo method_field('DELETE'); ?>
                                                                            <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
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
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>

    <?php
        $calendarEvents = $allProjects->map(fn($p) => [
            'title' => $p->name . ($p->client->abbreviation ? " ({$p->client->abbreviation})" : ''),
            'start' => $p->start_date,
            'end' => $p->end_date ? \Carbon\Carbon::parse($p->end_date)->addDay()->format('Y-m-d') : null,
            'url' => route('projects.show', $p->id),
            'color' => optional($colors?->firstWhere('id', $p->color))->hex_code ?? '#3B82F6',
        ]);
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("✅ FullCalendar読み込みテスト開始");

        const el = document.getElementById('project-calendar');
        if (!el) {
            console.error("❌ カレンダー要素が見つかりません");
            return;
        }

        if (typeof FullCalendar === 'undefined') {
            console.error("❌ FullCalendarがロードされていません（cdnjs版）");
            return;
        }

        const projects = <?php echo json_encode($calendarEvents, 15, 512) ?>;

        const calendar = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,today,next',
                center: 'title',
                right: '' // ← FullCalendar標準ボタンを使わない
            },
            events: projects,
            height: 700, 
            contentHeight: 'auto',
            expandRows: false,
            aspectRatio: 1.35,
            datesSet: function (info) {
                const monthNames = ["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"];
                const currentDate = info.view.currentStart; 
                const month = monthNames[currentDate.getMonth()];
                const year = currentDate.getFullYear();
                const titleEl = document.querySelector('#project-calendar .fc-toolbar-title');
                if (titleEl) titleEl.textContent = `${year}年 ${month}`;
            }
        });

        calendar.render();
        console.log("✅ カレンダー描画完了（Blade版）");

        // ✅ カレンダー描画後に「＋新規」ボタンを右上に追加
        const toolbar = el.querySelector('.fc-header-toolbar .fc-toolbar-chunk:last-child');
        if (toolbar) {
            const btn = document.createElement('a');
            btn.href = "<?php echo e(route('projects.create')); ?>";
            btn.textContent = "＋新規";
            btn.className = "inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150";
            toolbar.appendChild(btn);
        } else {
            console.warn("⚠️ FullCalendarツールバーが見つかりません。＋新規ボタンを配置できませんでした。");
        }
    });
    </script>

    <!-- モバイル固定ボタン -->
    <div id="mobile-fixed-buttons" class="fixed bottom-0 left-0 w-full bg-slate-600 border-t shadow-inner flex justify-around items-center p-1 z-50 lg:hidden">
        <!-- カレンダー -->
        <a href="#project-calendar" class="flex flex-col items-center justify-center flex-1 py-1 mx-1 text-white ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
            </svg>
            <span style="font-size:8px;">カレンダー</span>
        </a>

        <!-- 表 -->
        <a href="#project-table" class="flex flex-col items-center justify-center flex-1 py-1 mx-1 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"/>
            </svg>
            <span style="font-size:8px;">表</span>
        </a>

        <!-- +新規 -->
        <a href="<?php echo e(route('projects.create')); ?>" class="flex flex-col items-center justify-center flex-1 py-1 mx-1 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span style="font-size:8px;">新規</span>
        </a>
    </div>
<script>
    document.querySelectorAll('.generate-pdf').forEach(btn => {
        btn.addEventListener('click', function() {
            // PDF生成後に色を赤に変更
            setTimeout(() => {
                btn.classList.remove('text-gray-400', 'hover:text-gray-600');
                btn.classList.add('text-red-600', 'hover:text-red-800');
                btn.title = 'PDFダウンロード';
            }, 500); // Ajaxなどで生成待ちがあれば調整
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/projects/index.blade.php ENDPATH**/ ?>