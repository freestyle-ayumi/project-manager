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
                            <select name="month" id="month" class="mt-1 block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-600">
                                @foreach($months ?? [] as $value => $label)
                                    <option value="{{ $value }}" {{ ($selectedMonth ?? '') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 active:bg-indigo-600 transition">
                                表示
                            </button>
                        </div>
                    </form>

                    <!-- テーブル -->
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
                            @foreach($dailyRecords as $dateKey => $records)
                                <tr>
                                    <td class="px-2 py-1 whitespace-nowrap font-medium">
                                        {{ \Carbon\Carbon::parse($dateKey)->format('m/d (D)') }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <!-- ★新規：地点表示★ -->
                                        @if($records['location'])
                                            @if($records['is_business_trip'])
                                                出張: {{ $records['location'] }}
                                            @else
                                                本社
                                            @endif
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['check_in'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['break_start'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['break_end'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap">{{ $records['check_out'] }}</td>
                                    <td class="px-2 py-1 whitespace-nowrap font-medium text-indigo-600">
                                        {{ $records['work_hours'] }}
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