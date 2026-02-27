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
            0%, 100% { background-color: rgb(252 165 165, 0.5); }
            50% { background-color: rgb(254 202 202); }
        }
        .blink-red-bg {
            animation: blink-bg 2s ease-in-out infinite;
        }
    </style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div class="bg-white p-3 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-xl mb-1 text-gray-800">打刻</h3>
                        <div class="flex flex-col space-y-4 px-10">
                            <!-- 出勤 -->
                            <button id="check-in-btn" 
                                    class="text-center py-3 text-indigo-600 text-xs bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-type="in">
                                出勤
                            </button>

                            <!-- 中抜け / 戻り（横並び） -->
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <button id="break-start-btn" 
                                        class="text-center py-3 text-indigo-600 text-xs bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-type="break_start">
                                    中抜け
                                </button>
                                <button id="break-end-btn" 
                                        class="text-center py-3 text-indigo-600 text-xs bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-type="break_end">
                                    戻り
                                </button>
                            </div>

                            <!-- 退勤 -->
                            <button id="check-out-btn" 
                                    class="text-center py-3 text-indigo-600 text-xs bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-type="out">
                                退勤
                            </button>

                            <!-- 出張開始 -->
                            <button id="business-trip-start-btn" 
                                    class="text-center py-3 text-indigo-600 text-xs bg-purple-200 border border-gray-200 hover:bg-purple-700 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-type="business_trip_start">
                                出張開始
                            </button>

                            <!-- 出張終了 -->
                            <button id="business-trip-end-btn" 
                                    class="text-center py-3 text-indigo-600 text-xs bg-purple-200 border border-gray-200 hover:bg-purple-700 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-type="business_trip_end">
                                出張終了
                            </button>
                        <!-- メモ入力エリア（最初は非表示） -->
                        <div id="business-trip-form" class="mt-6 hidden bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <label for="business-note" class="block text-xs text-gray-700 mb-2">
                                出張メモ（必須）<br><span class="text-red-600">*出張先や目的を必ず入力してください</span>
                            </label>
                            <textarea id="business-note" rows="1" class="w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="例 : アイドルプロデュース。宮城出張"></textarea>

                            <div class="mt-2 flex justify-end space-x-2 text-xs">
                                <button id="cancel-business" class="text-center py-2 px-3 text-white bg-slate-400 border border-gray-200 hover:bg-slate-700  transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    キャンセル
                                </button>
                                <button id="confirm-business" class="text-center py-2 px-3 text-indigo-600 bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    出張申請
                                </button>
                            </div>
                        </div>
                        </div>
                    <div id="status-message" class="mt-4 text-center text-lg font-medium"></div>
                </div>

                <div class="md:col-span-1 lg:col-span-2 bg-white p-3 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-xl mb-1 text-gray-800">タイムカード<span class="text-sm">（今週 + 前週）</span></h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-gray-500 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 uppercase tracking-wider">日付</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">地点</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">出社</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">中抜け</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">戻り</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">退社</th>
                                    <th class="px-2 py-1 uppercase tracking-wider">勤務</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                <?php $__empty_1 = true; $__currentLoopData = $dashboardDailyRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-2 py-1 whitespace-nowrap font-medium">
                                            <?php echo e($records['date_formatted'] ?? $date); ?>  <!-- ← $records['date_formatted'] を優先 -->
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap">
                                            <?php echo e($records['location'] ?? '---'); ?>

                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['check_in'] ?? '---'); ?></td>
                                        <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['break_start'] ?? '---'); ?></td>
                                        <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['break_end'] ?? '---'); ?></td>
                                        <td class="px-2 py-1 whitespace-nowrap"><?php echo e($records['check_out'] ?? '---'); ?></td>
                                        <td class="px-2 py-1 whitespace-nowrap font-medium text-indigo-600">
                                            <?php echo e($records['work_hours'] ?? '---'); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="7" class="text-center py-8 text-gray-500">打刻データがありません</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('打刻スクリプト読み込み完了');

        // 出張状態（Bladeから渡す）
        let isBusinessTrip = <?php echo e($isBusinessTrip ? 'true' : 'false'); ?>;

        // 今日の出勤・退勤済みフラグ（Bladeから渡す）
        const todayClockedIn = <?php echo e($todayClockedIn ? 'true' : 'false'); ?>;
        const todayClockedOut = <?php echo e($todayClockedOut ? 'true' : 'false'); ?>;

        console.log('isBusinessTrip:', isBusinessTrip);
        console.log('todayClockedIn:', todayClockedIn);
        console.log('todayClockedOut:', todayClockedOut);

        // ボタン要素
        const checkInBtn = document.getElementById('check-in-btn');
        const checkOutBtn = document.getElementById('check-out-btn');
        const breakStartBtn = document.getElementById('break-start-btn');
        const breakEndBtn = document.getElementById('break-end-btn');
        const businessTripStartBtn = document.getElementById('business-trip-start-btn');
        const businessTripEndBtn = document.getElementById('business-trip-end-btn');
        const statusMessage = document.getElementById('status-message');
        const businessForm = document.getElementById('business-trip-form');
        const businessNote = document.getElementById('business-note');
        const confirmBusiness = document.getElementById('confirm-business');
        const cancelBusiness = document.getElementById('cancel-business');

        // 履歴読み込み関数（一番上に定義）
        async function loadRecentLogs() {
            console.log('履歴読み込み開始');
            try {
                const response = await fetch('<?php echo e(route("attendance.recent")); ?>');
                if (!response.ok) throw new Error('履歴取得失敗');

                const data = await response.json();
                console.log('取得データ:', data);

                const tbody = document.getElementById('attendance-tbody');
                if (!tbody) return;

                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-1 text-center text-sm text-gray-500">まだ打刻記録がありません</td></tr>';
                    return;
                }

                function getTypeClass(type) {
                    if (type === 'check_in') return 'bg-green-100 text-green-800';
                    if (type === 'check_out') return 'bg-red-100 text-red-800';
                    if (type === 'break_start') return 'bg-yellow-100 text-yellow-800';
                    if (type === 'break_end') return 'bg-blue-100 text-blue-800';
                    if (type === 'business_trip_start' || type === 'business_trip_end') return 'bg-purple-100 text-purple-800';
                    return 'bg-gray-100 text-gray-800';
                }

                data.forEach(log => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-2 py-1 whitespace-nowrap">${log.timestamp || '-'}</td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${getTypeClass(log.type)}">
                                ${log.type_label}
                            </span>
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                            ${log.location || '---'}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                            ${log.distance || '---'}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs ${log.is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${log.is_valid ? '有効' : '無効'}
                            </span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                console.error('履歴更新エラー:', error);
            }
        }

        // ボタン制御関数
        function updateButtons() {
            console.log('updateButtons called', { isBusinessTrip, todayClockedIn, todayClockedOut });

            const normalButtons = [checkInBtn, checkOutBtn, breakStartBtn, breakEndBtn].filter(btn => btn !== null);

            // 出張中 → 通常打刻を無効（出勤ボタンは許可）
            normalButtons.forEach(btn => btn.disabled = isBusinessTrip && btn !== checkInBtn);

            // 出勤ボタン → 出勤済みまたは出張中なら無効
            if (checkInBtn) checkInBtn.disabled = todayClockedIn || isBusinessTrip;

            // 中抜け・戻り → 出勤済みかつ退勤済みでないなら有効
            if (breakStartBtn) breakStartBtn.disabled = !todayClockedIn || todayClockedOut;
            if (breakEndBtn) breakEndBtn.disabled = !todayClockedIn || todayClockedOut;

            // 退勤ボタン → 退勤済みまたは出勤前なら無効
            if (checkOutBtn) checkOutBtn.disabled = todayClockedOut || !todayClockedIn;

            // 出張開始ボタン → 出張中または出勤済みなら無効
            if (businessTripStartBtn) businessTripStartBtn.disabled = isBusinessTrip || todayClockedIn;

            // 出張終了ボタン → 出張中でないなら無効
            if (businessTripEndBtn) businessTripEndBtn.disabled = !isBusinessTrip;
        }

        // 初期状態でボタン制御
        updateButtons();

        // 通常打刻処理（変更なし）
        async function handleCheck(type) {
            console.log(`打刻開始: ${type}`);

            const btn = 
                type === 'in' ? checkInBtn :
                type === 'out' ? checkOutBtn :
                type === 'break_start' ? breakStartBtn :
                breakEndBtn;

            if (!btn || btn.disabled) return;

            btn.disabled = true;
            statusMessage.innerHTML = '位置情報を取得中...';

            if (!navigator.geolocation) {
                statusMessage.innerHTML = '<span class="text-red-600">位置情報が利用できません</span>';
                btn.disabled = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude.toFixed(8);
                    const lng = position.coords.longitude.toFixed(8);

                    try {
                        const response = await fetch('<?php echo e(route("attendance.store")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                type: type,
                                latitude: lat,
                                longitude: lng
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            statusMessage.innerHTML = `<span class="text-green-600">${data.message || '打刻成功！'}</span>`;
                            location.reload();  // 状態更新
                        } else {
                            statusMessage.innerHTML = `<span class="text-red-600">エラー: ${data.message || '不明'}</span>`;
                        }
                    } catch (error) {
                        statusMessage.innerHTML = `<span class="text-red-600">通信エラー: ${error.message}</span>`;
                    } finally {
                        btn.disabled = false;
                    }
                },
                (error) => {
                    statusMessage.innerHTML = '<span class="text-red-600">位置情報の取得に失敗しました</span>';
                    btn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }

        // 通常打刻イベント
        if (checkInBtn) checkInBtn.addEventListener('click', () => handleCheck('in'));
        if (checkOutBtn) checkOutBtn.addEventListener('click', () => handleCheck('out'));
        if (breakStartBtn) breakStartBtn.addEventListener('click', () => handleCheck('break_start'));
        if (breakEndBtn) breakEndBtn.addEventListener('click', () => handleCheck('break_end'));

        // 出張開始ボタン
        if (businessTripStartBtn) {
            businessTripStartBtn.addEventListener('click', () => {
                businessForm.classList.remove('hidden');
                businessTripStartBtn.disabled = true;
                statusMessage.innerHTML = '出張メモを入力してください';
            });
        }

        // 出張終了ボタン
        if (businessTripEndBtn) {
            businessTripEndBtn.addEventListener('click', async () => {
                businessTripEndBtn.disabled = true;
                statusMessage.innerHTML = '位置情報を取得中...';

                if (!navigator.geolocation) {
                    statusMessage.innerHTML = '<span class="text-red-600">位置情報が利用できません</span>';
                    businessTripEndBtn.disabled = false;
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const lat = position.coords.latitude.toFixed(8);
                        const lng = position.coords.longitude.toFixed(8);

                        try {
                            const response = await fetch('<?php echo e(route("attendance.store")); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    type: 'business_trip_end',
                                    latitude: lat,
                                    longitude: lng,
                                    note: null
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                statusMessage.innerHTML = `<span class="text-green-600">${data.message || '出張終了しました'}</span>`;
                                isBusinessTrip = false;
                                updateButtons();

                                // 自動更新
                                console.log('出張終了成功 → タイムカード更新開始');
                                await loadRecentLogs();
                                console.log('タイムカード更新完了');

                                // リロード
                                setTimeout(() => {
                                    console.log('リロード実行');
                                    location.reload();
                                }, 800);
                            } else {
                                statusMessage.innerHTML = `<span class="text-red-600">エラー: ${data.message || '不明'}</span>`;
                            }
                        } catch (error) {
                            statusMessage.innerHTML = `<span class="text-red-600">通信エラー: ${error.message}</span>`;
                            console.error('出張終了エラー:', error);
                        } finally {
                            businessTripEndBtn.disabled = false;
                        }
                    },
                    (error) => {
                        statusMessage.innerHTML = '<span class="text-red-600">位置情報の取得に失敗しました</span>';
                        businessTripEndBtn.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            });
        }

        // メモ入力で確定ボタン有効化
        if (businessNote && confirmBusiness) {
            businessNote.addEventListener('input', () => {
                confirmBusiness.disabled = businessNote.value.trim() === '';
            });
        }

        // キャンセル
        if (cancelBusiness) {
            cancelBusiness.addEventListener('click', () => {
                businessForm.classList.add('hidden');
                if (businessTripStartBtn) businessTripStartBtn.disabled = false;
                if (businessNote) businessNote.value = '';
                if (statusMessage) statusMessage.innerHTML = '';
            });
        }

        // 出張開始確定
        if (confirmBusiness) {
            confirmBusiness.addEventListener('click', async () => {
                confirmBusiness.disabled = true;
                statusMessage.innerHTML = '位置情報を取得中...';

                if (!navigator.geolocation) {
                    statusMessage.innerHTML = '<span class="text-red-600">位置情報が利用できません</span>';
                    confirmBusiness.disabled = false;
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const lat = position.coords.latitude.toFixed(8);
                        const lng = position.coords.longitude.toFixed(8);

                        try {
                            const noteValue = businessNote.value.trim();
                            if (!noteValue) {
                                statusMessage.innerHTML = '<span class="text-red-600">メモを入力してください</span>';
                                confirmBusiness.disabled = false;
                                return;
                            }

                            const response = await fetch('<?php echo e(route("attendance.store")); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    type: 'business_trip_start',
                                    latitude: lat,
                                    longitude: lng,
                                    note: noteValue
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                statusMessage.innerHTML = `<span class="text-green-600">${data.message || '出張開始しました'}</span>`;
                                businessForm.classList.add('hidden');
                                if (businessTripStartBtn) businessTripStartBtn.disabled = true;
                                if (businessTripEndBtn) businessTripEndBtn.disabled = false;
                                if (businessNote) businessNote.value = '';
                                isBusinessTrip = true;
                                updateButtons();

                                // 自動更新（最優先）
                                console.log('出張開始成功 → タイムカード更新開始');
                                await loadRecentLogs();  // 履歴更新
                                console.log('タイムカード更新完了');

                                // 安全のためリロード（500ms遅延で状態反映）
                                setTimeout(() => {
                                    console.log('リロード実行');
                                    location.reload();
                                }, 800);
                            } else {
                                statusMessage.innerHTML = `<span class="text-red-600">エラー: ${data.message || '不明'}</span>`;
                            }
                        } catch (error) {
                            statusMessage.innerHTML = `<span class="text-red-600">通信エラー: ${error.message}</span>`;
                            console.error('出張開始エラー:', error);
                        } finally {
                            confirmBusiness.disabled = false;
                        }
                    },
                    (error) => {
                        statusMessage.innerHTML = '<span class="text-red-600">位置情報の取得に失敗しました</span>';
                        confirmBusiness.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                );
            });
        }

        // ページ読み込み時に履歴表示
        loadRecentLogs();
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\project-manager\resources\views/dashboard.blade.php ENDPATH**/ ?>