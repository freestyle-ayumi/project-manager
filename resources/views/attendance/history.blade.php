<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            打刻履歴
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    <!-- 月選択フォーム -->
                    <form method="GET" action="{{ route('attendance.history') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
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
                                <button type="submit" class="inline-flex items-center px-4 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 active:bg-indigo-600 transition">
                                    表示
                                </button>

                                <a href="{{ route('attendance.history') }}" class="inline-flex items-center px-4 py-1 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition">
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
                                <tr class="{{ $rowBg }}">
                                    <td class="px-2 py-1 whitespace-nowrap font-medium">
                                        {{ $carbonDate->format('m/d') }} 
                                        <span class="text-[10px]">{{ ['日','月','火','水','木','金','土'][$carbonDate->dayOfWeek] }}</span>
                                    </td>
                                    <td class="px-2 py-1">
                                        @if($records['is_business_trip'])
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-purple-200 text-[10px] bg-purple-100 text-purple-800 mr-1">出張</span>
                                            <span class="text-gray-600">{{ $records['note'] ?? '出張先未入力' }}</span>
                                        @elseif($records['location'])
                                            <span class="text-gray-600">{{ $records['location'] }}</span>
                                        @else
                                            <span class="text-gray-300">---</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['check_in'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-gray-400">{{ $records['break_start'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap text-gray-400">{{ $records['break_end'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['check_out'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap font-bold text-indigo-600">
                                        {{ $records['work_hours'] != '0:00' ? $records['work_hours'] : '---' }}
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