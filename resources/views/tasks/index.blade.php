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

<style>
    .calendar-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }
    .calendar-table th,
    .calendar-table td {
        width: 6rem;
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
        {{-- ＋新規ボタン --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-right mb-4">
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ＋新規
            </a>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- あなたのタスク --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="font-semibold mb-2">あなたのタスク</h3>

            {{-- 週送りナビゲーション --}}
            <div class="flex flex-col items-center">
                {{-- 期間表示 --}}
                <div class="text-gray-700 mt-1 text-center text-sm">
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

            {{-- 週送りナビゲーション --}}
            <div class="flex flex-col items-center">
                {{-- 期間表示 --}}
                <div class="text-gray-700 mt-1 text-center text-sm">
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

                {{-- 全ユーザー表 --}}
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
                                    <td class="px-2 py-1 border bg-gray-50 font-semibold">{{ $user->name }}</td>
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
                                            @if ($tasksForDay->isNotEmpty())
                                                <ul class="m-0 p-0 text-xs">
                                                    @foreach ($tasksForDay as $task)
                                                        <li class="flex items-start gap-1">
                                                            <span class="text-black">•</span>
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
