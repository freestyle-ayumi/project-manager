<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                勤務詳細：{{ $user->name }}
            </h2>
            <a href="{{ route('admin.summary.index', ['month' => $selectedMonth]) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; 一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 text-xs">
            
            <div class="bg-white p-4 shadow-sm sm:rounded-lg mb-4 text-gray-600">
                <div class="grid grid-cols-3 gap-2 border-b border-gray-100 mb-4 pb-2 px-2">
                    <div class="text-gray-900 text-lg font-bold">
                        {{ Carbon\Carbon::parse($selectedMonth)->format('Y年m月度') }}
                    </div>

                    <div>
                        <span class="text-gray-400 uppercase">所属:</span>
                        <span class="ml-2 text-gray-900 text-sm">イベント事業部</span>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase">氏名:</span>
                        <span class="ml-2 text-gray-900 text-sm font-semibold">{{ $user->name }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-5 gap-2 mb-3 text-center">
                    <div>
                        <p class="text-gray-400 mb-1">出勤日数</p>
                        <p class="text-lg font-bold text-gray-800">{{ $monthlySummary['days_worked'] }} <span class="text-xs font-normal">日</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-1">欠勤日数</p>
                        <p class="text-lg font-bold text-red-500">{{ $monthlySummary['absent_days'] }} <span class="text-xs font-normal">日</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-1">有休取得日数</p>
                        <p class="text-lg font-bold text-green-600">{{ $monthlySummary['paid_holidays'] }} <span class="text-xs font-normal">日</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-1">代休取得日数</p>
                        <p class="text-lg font-bold text-orange-500">{{ $monthlySummary['sub_holidays'] }} <span class="text-xs font-normal">日</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-1">特別休暇</p>
                        <p class="text-lg font-bold text-gray-800">0 <span class="text-xs font-normal">日</span></p>
                    </div>
                </div>

                <div class="flex py-2 bg-gray-50 rounded-lg">
                    <div class="flex-1 text-center">
                        <p class="text-gray-400 mb-1">総就業時間</p>
                        <p class="text-xl font-bold text-indigo-600">{{ $monthlySummary['total_work_time'] }}</p>
                    </div>

                    <div class="flex-1 text-center border-l border-gray-200">
                        <p class="text-gray-400 mb-1">早出残業時間</p>
                        <p class="text-xl font-bold text-gray-800">{{ $monthlySummary['total_early_over'] }}</p>
                    </div>

                    <div class="flex-1 text-center border-l border-gray-200">
                        <p class="text-gray-400 mb-1">早朝深夜勤務時間</p>
                        <p class="text-xl font-bold text-gray-800">{{ $monthlySummary['total_night'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-50 text-xs text-left text-gray-500">
                        <tr>
                            <th class="px-2 py-2 font-medium border-b w-8">日</th>
                            <th class="px-2 py-2 font-medium border-b w-8">曜</th>
                            <th class="px-2 py-2 font-medium border-b">出社</th>
                            <th class="px-2 py-2 font-medium border-b">退社</th>
                            <th class="px-2 py-2 font-medium border-b bg-blue-50/50">基本</th>
                            <th class="px-2 py-2 font-medium border-b">早出</th>
                            <th class="px-2 py-2 font-medium border-b">残業</th>
                            <th class="px-2 py-2 font-medium border-b">深夜</th>
                            <th class="px-2 py-2 font-medium border-b font-bold text-indigo-600">合計</th>
                            <th class="px-2 py-2 font-medium border-b">備考</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white font-mono text-gray-700">
                        @foreach($dailyData as $data)
                        <tr class="{{ $data['day'] == '日' ? 'bg-red-50/50' : ($data['day'] == '土' ? 'bg-blue-50/50' : '') }} hover:bg-gray-50">
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['date'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['day'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['in'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['out'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap bg-blue-50/30 font-medium">{{ $data['basic'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['early'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['over'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap">{{ $data['night'] }}</td>
                            <td class="px-2 py-1.5 whitespace-nowrap font-bold text-indigo-500">{{ $data['total'] }}</td>
                            <td class="px-2 py-1.5 text-[10px] font-sans text-gray-400">{{ $data['note'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>