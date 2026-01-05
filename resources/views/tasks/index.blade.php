@php
    use Carbon\Carbon;

    // 現在のオフセット（クエリパラメータ week_offset で週移動）
    $weekOffset = (int) request()->query('week_offset', 0); // ← キャスト追加！

    // 今日を基準に週オフセットを加算
    $baseDate = Carbon::today()->copy()->addWeeks($weekOffset);

    // 表示する7日間を生成
    $days = [];
    for ($i = 0; $i < 7; $i++) {
        $days[] = $baseDate->copy()->addDays($i);
    }

    $headerDays = collect($days)->map(function($day) use ($holidays) {
        $dayKey = $day->format('Y/m/d');
        $isSunday = $day->dayOfWeek === 0;
        $isSaturday = $day->dayOfWeek === 6;
        $isHoliday = isset($holidays[$dayKey]);
        $bgClass = $isSunday || $isHoliday ? 'bg-pink-200 text-red-600' : ($isSaturday ? 'bg-blue-100 text-blue-600' : '');
        return [
            'day' => $day,
            'key' => $dayKey,
            'bgClass' => $bgClass,
            'isHoliday' => $isHoliday,
            'holidayName' => $isHoliday ? $holidays[$dayKey] : null,
        ];
    });

    $weekRangeText = $baseDate->format('Y/m/d') . ' 〜 ' . $baseDate->copy()->addDays(6)->format('Y/m/d');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('タスク管理') }}
        </h2>
    </x-slot>

    <div class="py-1 sm:py-2">
        {{-- ＋新規ボタン --}}
        <div class="max-w-3xl sm:max-w-7xl mx-auto pl-2 sm:pl-0 pb-1 sm:px-6 lg:px-8 flex justify-end">
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ＋新規
            </a>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-2 sm:space-y-6">

            {{-- あなたのタスク --}}
            <div class="bg-white shadow-sm rounded-lg p-4 mx-1 sm:m-0">
                <h3 class="absolute text-sm font-semibold pl-1">あなたのタスク</h3>

                {{-- 週送りナビゲーション --}}
                <div class="flex flex-col items-center">
                    {{-- 期間表示 --}}
                    <div class="text-gray-700 text-center text-sm">
                        <span class="text-xs">{{ $baseDate->format('Y年') }}</span> {{ $baseDate->format('m/d') }}〜{{ $baseDate->copy()->addDays(6)->format('m/d') }}</span>
                    </div>
                    <div class="flex justify-between w-full items-center bg-gray-500 p-1 rounded">
                        <a href="{{ route('tasks.index', ['week_offset' => $weekOffset - 1]) }}"
                        class="mx-1 my-1 bg-gray-200 hover:bg-white text-gray-700 rounded shadow flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <a href="{{ route('tasks.index') }}" class="px-2 py-0.5 bg-gray-200 hover:bg-white text-xs text-gray-700 font-bold rounded shadow">
                            今日
                        </a>
                        <a href="{{ route('tasks.index', ['week_offset' => $weekOffset + 1]) }}"
                        class="mx-1 my-1 bg-gray-200 hover:bg-white text-gray-700 rounded shadow flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- あなたのタスク表 --}}
                <div class="overflow-x-auto">
                    <table class="calendar-table border text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 border w-40">&nbsp;</th>
                                @foreach($headerDays as $hd)
                                    <th class="px-2 py-1 border {{ $hd['bgClass'] }}">
                                        {{ $hd['day']->format('m/d') }} <span class="text-xs">({{ $hd['day']->format('D') }})</span>
                                        @if($hd['isHoliday'])
                                            <div class="text-xs">{{ $hd['holidayName'] }}</div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-1 border bg-gray-50">&nbsp;</td>
                                @foreach($headerDays as $hd)
                                    @php
                                        $taskBgClass = $hd['day']->dayOfWeek === 0 || $hd['isHoliday']
                                            ? 'bg-pink-50'
                                            : ($hd['day']->dayOfWeek === 6 ? 'bg-blue-50' : 'bg-white');

                                        $tasksForDay = $myTasks->filter(function($task) use ($hd) {
                                            $start = \Carbon\Carbon::parse($task->start_date ?? $task->due_date ?? now());
                                            $end = \Carbon\Carbon::parse($task->due_date ?? $task->start_date ?? now());
                                            return $hd['day']->between($start, $end);
                                        });
                                    @endphp

                                    <td class="px-2 py-1 border {{ $taskBgClass }}">
                                        @if ($tasksForDay->isNotEmpty())
                                            <ul class="m-0 p-0 text-xs">
                                                @foreach ($tasksForDay as $task)
                                                    <li class="flex items-start gap-1">
                                                        <span class="text-black">•</span>
                                                        <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline">
                                                            {{ $task->name }}
                                                            @if($task->start_time)
                                                                <span class="text-gray-500">({{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }})</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 他の人のタスク --}}
            <div class="bg-white shadow-sm rounded-lg p-4 text-xs mx-1 sm:m-0">

                {{-- フィルター --}}
                <form method="GET" action="{{ route('tasks.index') }}" class="mb-0 rounded border p-1">
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- フィルターマーク --}}
                        <div class="flex items-center text-gray-600 ml-2">
                            {{-- Heroicons: Funnel（フィルター）アイコン --}}
                            <svg xmlns="http://www.w3.org/2000/svg" 
                            fill="none" viewBox="0 0 24 24" 
                            stroke-width="1.5" 
                            stroke="currentColor"
                             class="w-4 h-4 size-4 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                            </svg>
                            <span class="text-xs font-semibold whitespace-nowrap ml-1">フィルター：</span>
                        </div>

                        {{-- 共通「すべて」ボタン --}}
                        <button type="button"
                            id="btn-all"
                            class="px-2 py-1 rounded border text-xs hover:bg-blue-400 focus:outline-none">
                            すべて
                        </button>

                        {{-- 区切り --}}
                        <div class="mx-1 h-6 border-l"></div>

                        {{-- ユーザーボタン群 --}}
                        @foreach($allUsersForFilter as $user)
                            @if($user->id != Auth::id())
                                <button type="button"
                                    class="px-2 py-1 rounded border text-xs user-filter-btn hover:bg-sky-200 focus:outline-none"
                                    data-user-id="{{ $user->id }}">
                                    {{ $user->name }}
                                </button>
                            @endif
                        @endforeach

                        {{-- 区切り --}}
                        <div class="mx-1 h-6 border-l"></div>

                        {{-- ロールボタン群 --}}
                        @foreach(\App\Models\Role::all() as $role)
                            <button type="button"
                                class="px-2 py-1 rounded border text-xs role-filter-btn hover:bg-teal-100 focus:outline-none"
                                data-role-id="{{ $role->id }}">
                                {{ $role->name }}
                            </button>
                        @endforeach

                        {{-- 検索ボタン --}}
                        <div class="ml-auto">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-xs">
                                検索
                            </button>
                        </div>
                    </div>

                    {{-- hidden inputs --}}
                    <div id="hidden-inputs"></div>
                </form>

            {{-- 週送りナビゲーション --}}
            <div class="flex flex-col items-center">
                {{-- 期間表示 --}}
                <div class="text-gray-700 text-center text-sm">
                    <span class="text-xs">{{ $baseDate->format('Y年') }}</span> {{ $baseDate->format('m/d') }}〜{{ $baseDate->copy()->addDays(6)->format('m/d') }}</span>
                </div>
                <div class="flex justify-between w-full items-center bg-gray-500 p-1 rounded">
                    <a href="{{ route('tasks.index', ['week_offset' => $weekOffset - 1]) }}"
                    class="mx-1 my-1 bg-gray-200 hover:bg-white text-gray-700 rounded shadow flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="px-2 py-0.5 bg-gray-200 hover:bg-white text-xs text-gray-700 font-bold rounded shadow">
                        今日
                    </a>
                    <a href="{{ route('tasks.index', ['week_offset' => $weekOffset + 1]) }}"
                    class="mx-1 my-1 bg-gray-200 hover:bg-white text-gray-700 rounded shadow flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- スクリプト：1つの「すべて」でユーザー/ロール両方をリセットする --}}
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ボタンノード
                const btnAll = document.getElementById('btn-all');
                const userBtns = Array.from(document.querySelectorAll('.user-filter-btn'));
                const roleBtns = Array.from(document.querySelectorAll('.role-filter-btn'));
                const hiddenContainer = document.getElementById('hidden-inputs');

                // 初期選択セット（Bladeで渡されている userIds, roleIds を利用して初期化）
                // コントローラから渡した変数名に合わせてください（存在しない場合は空配列）
                let selectedUsers = new Set(@json($userIds ?? []));
                let selectedRoles = new Set(@json($roleIds ?? []));

                // helper: render hidden inputs
                function renderHiddenInputs() {
                    hiddenContainer.innerHTML = '';
                    // ユーザー選択
                    selectedUsers.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'user_ids[]';
                        input.value = id;
                        hiddenContainer.appendChild(input);
                    });
                    // ロール選択
                    selectedRoles.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'role_ids[]';
                        input.value = id;
                        hiddenContainer.appendChild(input);
                    });
                }

                // helper: 更新ボタン外観
                function updateButtonStyles() {
                    userBtns.forEach(btn => {
                        const id = btn.dataset.userId;
                        if (selectedUsers.has(String(id))) {
                            btn.classList.add('bg-blue-600','text-white');
                        } else {
                            btn.classList.remove('bg-blue-600','text-white');
                        }
                    });
                    roleBtns.forEach(btn => {
                        const id = btn.dataset.roleId;
                        if (selectedRoles.has(String(id))) {
                            btn.classList.add('bg-teal-600','text-white');
                        } else {
                            btn.classList.remove('bg-teal-600','text-white');
                        }
                    });

                    // "すべて" ボタンは両方が未選択（＝空）なら強調、そうでなければ通常
                    if ((selectedUsers.size === 0) && (selectedRoles.size === 0)) {
                        btnAll.classList.add('bg-gray-700','text-white');
                    } else {
                        btnAll.classList.remove('bg-gray-700','text-white');
                    }
                }

                // 初期描画
                updateButtonStyles();
                renderHiddenInputs();

                // 共通「すべて」クリック：両方の選択をクリア（＝全員表示に相当）
                btnAll.addEventListener('click', () => {
                    selectedUsers.clear();
                    selectedRoles.clear();

                    // 重要：hiddenには特別な値 'all' を入れたい場合は以下を有効化
                    // const allInput = document.createElement('input');
                    // allInput.type = 'hidden';
                    // allInput.name = 'user_ids[]';
                    // allInput.value = 'all';
                    // hiddenContainer.appendChild(allInput);

                    updateButtonStyles();
                    renderHiddenInputs();
                });

                // ユーザーボタンのトグル
                userBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = String(btn.dataset.userId);

                        // toggle selected
                        if (selectedUsers.has(id)) {
                            selectedUsers.delete(id);
                        } else {
                            selectedUsers.add(id);
                        }

                        // すべてボタンが意味を持つように、選択が入ったら 'all' は消す（今回は 'all' を使わない実装）
                        if (selectedUsers.size > 0) {
                            // nothing else required
                        }

                        updateButtonStyles();
                        renderHiddenInputs();
                    });
                });

                // ロールボタンのトグル
                roleBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = String(btn.dataset.roleId);

                        if (selectedRoles.has(id)) {
                            selectedRoles.delete(id);
                        } else {
                            selectedRoles.add(id);
                        }

                        updateButtonStyles();
                        renderHiddenInputs();
                    });
                });

            });
            </script>


            {{-- タスク表示 --}}
                <div class="overflow-x-auto">
                    <table class="calendar-table border text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 border w-40">ユーザー名</th>
                                @foreach($headerDays as $hd)
                                    <th class="px-2 py-1 border {{ $hd['bgClass'] }}">
                                        {{ $hd['day']->format('m/d') }} <span class="text-xs">({{ $hd['day']->format('D') }})</span>
                                        @if($hd['isHoliday'])
                                            <div class="text-xs">{{ $hd['holidayName'] }}</div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b">
                                    <td class="px-2 py-1 border bg-gray-50 text-xs">{{ $user->name }}</td>
                                    @foreach($headerDays as $hd)
                                        @php
                                            $tasksForDay = $user->tasks->filter(function($task) use ($hd) {
                                                $start = \Carbon\Carbon::parse($task->start_date ?? $task->due_date ?? now());
                                                $end = \Carbon\Carbon::parse($task->due_date ?? $task->start_date ?? now());
                                                return $hd['day']->between($start, $end);
                                            });

                                            $taskBgClass = $hd['day']->dayOfWeek === 0 || $hd['isHoliday']
                                                ? 'bg-pink-50'
                                                : ($hd['day']->dayOfWeek === 6 ? 'bg-blue-50' : 'bg-white');
                                        @endphp
                                        <td class="px-2 py-1 border align-top {{ $taskBgClass }}">
                                            <div class="flex justify-between items-center">
                                                {{-- タスクリスト --}}
                                                @if ($tasksForDay->isNotEmpty())
                                                    <ul class="m-0 p-0 text-xs">
                                                        @foreach ($tasksForDay as $task)
                                                            <li class="flex items-start gap-1">
                                                                <span class="text-black">•</span>
                                                                <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline">
                                                                    {{ $task->name }}
                                                                    @if($task->start_time)
                                                                        <span class="text-gray-500">({{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }})</span>
                                                                    @endif
                                                                </a>

                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span></span> {{-- タスクがない場合のスペース --}}
                                                @endif

                                                {{-- 「＋」ボタン --}}
                                                <a href="{{ route('tasks.create', ['user_id' => $user->id, 'date' => $hd['day']->format('Y-m-d')]) }}"
                                                    class="text-green-600 hover:text-green-800"
                                                    title="タスク追加">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="inline-block" viewBox="0 0 16 16">
                                                        <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
