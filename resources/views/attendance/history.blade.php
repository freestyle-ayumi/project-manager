<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            打刻履歴
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    <!-- 月選択フォーム -->
                    <form method="GET" action="{{ route('attendance.history') }}" class="mb-4">
                        <div class="flex flex-col items-center sm:flex-row sm:items-end gap-3">
                            <div>
                                <select name="month" id="month" class="block w-full sm:w-48 rounded-md border-gray-300 px-2 py-1 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600">
                                    @foreach($months ?? [] as $value => $label)
                                        <option value="{{ $value }}" {{ ($selectedMonth ?? '') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 active:bg-indigo-600 transition">
                                    表示
                                </button>

                                <a href="{{ route('attendance.history') }}" class="inline-flex items-center px-2 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition">
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
                            @foreach($dailyRecords as $dateKey => $records)
                                @php
                                    $carbonDate = \Carbon\Carbon::parse($dateKey);
                                    // 土日の背景色
                                    $rowBg = '';
                                    if($carbonDate->isSaturday()) $rowBg = 'bg-blue-50';
                                    if($carbonDate->isSunday()) $rowBg = 'bg-red-50';
                                @endphp
                                <tr class="{{ $rowBg }} text-gray-500">
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        {{ $carbonDate->format('m/d') }} 
                                        <span class="text-[10px]">{{ ['日','月','火','水','木','金','土'][$carbonDate->dayOfWeek] }}</span>
                                    </td>
                                    <td class="px-2 py-1">
                                        @if($records['is_business_trip'])
                                            <div class="flex items-center gap-1">
                                                <span class="text-purple-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                                        <g fill="none" fill-rule="evenodd"><path d="M0 0h24v24H0z"/><path fill="currentColor" d="M12.868 5a3 3 0 0 1 2.572 1.457l2.167 3.611l2.641.33A2 2 0 0 1 22 12.383V15a3 3 0 0 1-2.128 2.872A3.001 3.001 0 0 1 14.17 18H9.829a3.001 3.001 0 0 1-5.7-.128A3 3 0 0 1 2 15v-3.807a2 2 0 0 1 .143-.743l1.426-3.564A3 3 0 0 1 6.354 5zM7 16a1 1 0 1 0 0 2a1 1 0 0 0 0-2m10 0a1 1 0 1 0 0 2a1 1 0 0 0 0-2m-4.132-9H11v3h4.234l-1.509-2.514A1 1 0 0 0 12.868 7M9 7H6.354a1 1 0 0 0-.928.629L4.477 10H9z"/></g>
                                                    </svg>
                                                </span>
                                                {{ $records['note'] ?? '出張先未入力' }}
                                            </div>
                                        @elseif($records['location'])
                                            {{ $records['location'] }}
                                        @else
                                            <span class="text-gray-200">---</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap {{ ($records['check_in'] ?? '---') === '---' ? 'text-gray-200' : '' }}">
                                            {{ $records['check_in'] ?? '---' }}
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap {{ ($records['break_start'] ?? '---') === '---' ? 'text-gray-200' : '' }}">
                                            {{ $records['break_start'] ?? '---' }}
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap {{ ($records['break_end'] ?? '---') === '---' ? 'text-gray-200' : '' }}">
                                            {{ $records['break_end'] ?? '---' }}
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap {{ ($records['check_out'] ?? '---') === '---' ? 'text-gray-200' : '' }}">
                                            {{ $records['check_out'] ?? '---' }}
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap {{ ($records['work_hours'] ?? '---') === '---' ? 'text-gray-200' : 'text-indigo-500 font-bold' }}">
                                            {{ $records['work_hours'] ?? '---' }}
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>