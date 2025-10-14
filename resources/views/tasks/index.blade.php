<style>
    .calendar-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .calendar-table th,
    .calendar-table td {
        width: 6rem; /* w-24相当 */
        text-align: center;
        vertical-align: middle;
        height: 2rem;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('タスク管理') }}
        </h2>
    </x-slot>

    <div class="py-4">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 mb-4 text-right">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('＋新規') }}
            </a>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 週表示カレンダー --}}
            @php
                $today = \Carbon\Carbon::today(); // 今日
                $days = [];
                for ($i = 0; $i < 7; $i++) {
                    $days[] = $today->copy()->addDays($i); // 今日を0日目として7日間
                }

                // ヘッダー用に曜日・祝日情報をまとめておく
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
            @endphp

            {{-- プロジェクトスケジュール --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="font-semibold mb-2">スケジュール</h3>
                <div class="overflow-x-auto">
                    <table class="calendar-table border text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 border w-40">プロジェクト名</th>
                                @foreach($headerDays as $hd)
                                    <th class="px-2 py-1 border {{ $hd['bgClass'] }}">
                                        {{ $hd['day']->format('D m/d') }}
                                        @if($hd['isHoliday'])
                                            <div class="text-xs">{{ $hd['holidayName'] }}</div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr class="border-b relative">
                                <td class="px-2 py-1 border font-semibold bg-gray-50">{{ $project->name }}</td>
                                @foreach($headerDays as $hd)
                                    @php
                                        $start = \Carbon\Carbon::parse($project->start_date);
                                        $end = \Carbon\Carbon::parse($project->end_date);
                                        $current = $hd['day'];
                                        $inRange = $current->between($start, $end);
                                        $isStart = $current->isSameDay($start);
                                        $isEnd = $current->isSameDay($end);

                                        // 土日・祝日だけ薄い背景を反映
                                        $cellBgClass = $hd['day']->dayOfWeek === 0 || $hd['isHoliday'] ? 'bg-pink-50' 
                                                    : ($hd['day']->dayOfWeek === 6 ? 'bg-blue-50' : 'bg-white');
                                    @endphp
                                    <td class="border relative {{ $cellBgClass }}">
                                        @if($inRange)
                                            <div class="absolute top-1/2 left-0 right-0 -translate-y-1/2 h-3 bg-blue-400/40 border border-blue-500/40
                                                @if($isStart) rounded-l-full @endif
                                                @if($isEnd) rounded-r-full @endif">
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>

        {{-- あなたのタスク --}}
        <div class="bg-white shadow-sm rounded-lg p-4">
            <h3 class="font-semibold mb-2">あなたのタスク</h3>

            <div class="overflow-x-auto">
                <table class="calendar-table border text-sm text-gray-700">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- 空のセルを入れてズレを防ぐ --}}
                            <th class="px-2 py-1 border w-40">&nbsp;</th>

                            @foreach($headerDays as $hd)
                                <th class="px-2 py-1 border {{ $hd['bgClass'] }}">
                                    {{ $hd['day']->format('m/d (D)') }}
                                    @if($hd['isHoliday'])
                                        <div class="text-xs">{{ $hd['holidayName'] }}</div>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="border-b">
                            {{-- 空セル --}}
                            <td class="px-2 py-1 border bg-gray-50">&nbsp;</td>

                            @foreach($headerDays as $hd)
                                @php
                                    $taskBgClass = $hd['day']->dayOfWeek === 0 || $hd['isHoliday']
                                        ? 'bg-pink-50'
                                        : ($hd['day']->dayOfWeek === 6 ? 'bg-blue-50' : 'bg-white');

                                    // 当日のタスクを抽出
                                    $tasksForDay = $myTasks->filter(function($task) use ($hd) {
                                        $taskStart = \Carbon\Carbon::parse($task->start_date ?? now());
                                        $taskEnd = \Carbon\Carbon::parse($task->due_date);
                                        return $hd['day']->between($taskStart, $taskEnd);
                                    });
                                @endphp

                                <td class="px-2 py-1 border {{ $taskBgClass }}">
                                    @if ($tasksForDay->isNotEmpty())
                                        <ul class="list-disc list-inside text-left m-0 p-0 text-xs">
                                            @foreach ($tasksForDay as $task)
                                                <li>
                                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline">
                                                        {{ $task->name }}
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

            {{-- 全ユーザーのタスク --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="font-semibold mb-2">全ユーザーのタスク</h3>

                <div class="overflow-x-auto">
                    <table class="calendar-table border text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 border w-40">ユーザー名</th>
                                @foreach($headerDays as $hd)
                                    <th class="px-2 py-1 border {{ $hd['bgClass'] }}">
                                        {{ $hd['day']->format('m/d (D)') }}
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
                                    <td class="px-2 py-1 border bg-gray-50 font-semibold">
                                        {{ $user->name }}
                                    </td>

                                    @foreach ($headerDays as $hd)
                                        @php
                                            // 該当日のタスクを抽出
                                            $tasksForDay = $user->tasks->filter(function($task) use ($hd) {
                                                return $task->due_date && \Carbon\Carbon::parse($task->due_date)->isSameDay($hd['day']);
                                            });

                                            $taskBgClass = $hd['day']->dayOfWeek === 0 || $hd['isHoliday']
                                                ? 'bg-pink-50'
                                                : ($hd['day']->dayOfWeek === 6 ? 'bg-blue-50' : 'bg-white');
                                        @endphp

                                        <td class="px-2 py-1 border align-top {{ $taskBgClass }}">
                                            @if ($tasksForDay->isNotEmpty())
                                                <ul class="list-disc list-inside text-left m-0 p-0 text-xs">
                                                    @foreach ($tasksForDay as $task)
                                                        <li>
                                                            <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline">
                                                                {{ $task->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
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
