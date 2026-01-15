<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>
    <style>
        @keyframes blink-bg {
            0%, 100% { background-color: rgb(252 165 165, 0.5); } /* 薄赤 */
            50% { background-color: rgb(254 202 202); } /* 濃い赤 */
        }
        .blink-red-bg {
            animation: blink-bg 2s ease-in-out infinite;
        }
    </style>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 1. イベント -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">イベント</h3>
                            @if($upcomingProjects->isEmpty())
                                <p class="text-gray-600">開催前・開催中のイベントはありません。</p>
                            @else
                                <ul class="space-y-1">
                                    @foreach($upcomingProjects as $project)
                                        <li class="flex justify-between items-center bg-gray-50 p-2 rounded-md">
                                            <div class="text-xs">
                                                <a href="{{ route('projects.show', $project) }}" class="text-sm text-blue-600 hover:underline">
                                                    {{ $project->name }}
                                                </a>
                                                <p class="text-gray-500">
                                                    {{ \Carbon\Carbon::parse($project->start_date)->format('m/d') }}
                                                    @if($project->end_date)
                                                        〜 {{ \Carbon\Carbon::parse($project->end_date)->format('m/d') }}
                                                    @endif
                                                    （{{ $project->client->abbreviation ?? '未設定' }}）
                                                </p>
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $project->start_date > $today ? 'bg-red-200 text-red-800' : 'bg-amber-200 text-amber-800' }}">
                                                {{ $project->start_date > $today ? '開催前' : '開催中' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-4 text-right">
                                <a href="{{ route('projects.index') }}" class="text-sm text-indigo-600 hover:underline">すべて見る →</a>
                            </div>
                        </div>

                        <!-- 2. 未承認の経費 -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">未承認の経費</h3>
                            @if($pendingExpenses->isEmpty())
                                <p class="text-gray-600">未承認の経費申請はありません。</p>
                            @else
                                <ul class="space-y-3">
                                    @foreach($pendingExpenses as $expense)
                                        <li class="rounded-md overflow-hidden
                                            {{ $expense->status->name === '差し戻し' ? 'blink-red-bg' : 'bg-gray-50' }}">
                                            <a href="{{ route('expenses.show', $expense) }}" class="block p-2 hover:bg-gray-100 transition-colors">
                                                <div class="flex justify-between items-center">
                                                    <div class="text-xs">
                                                        <div class="text-sm text-blue-600">
                                                            {{ $expense->project->name ?? '未設定' }} <span class="text-[11px] text-gray-500 font-normal">({{ \Carbon\Carbon::parse($expense->date)->format('m/d') }})</span>
                                                        </div>
                                                        <p class="text-gray-500">
                                                            ¥{{ number_format($expense->amount) }}
                                                        </p>
                                                    </div>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                        {{ $expense->status->name === '差し戻し' ? 'bg-red-600 text-white' : 'bg-amber-200 text-amber-800' }}">
                                                        {{ $expense->status->name }}
                                                        @if($expense->status->name === '差し戻し')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-4 text-right">
                                <a href="{{ route('expenses.index') }}" class="text-sm text-indigo-600 hover:underline">すべて見る →</a>
                            </div>
                        </div>

                        <!-- 3. 割り当てられたタスク -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-xs">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">あなたのタスク</h3>
                            @if($assignedTasks->isEmpty())
                                <p class="text-gray-600">現在タスクはありません。</p>
                            @else
                                <ul class="space-y-1">
                                    @foreach($assignedTasks as $task)
                                        <li class="bg-gray-50 p-2 rounded-md text-xs text-gray-500">

                                            {{-- 上段：プロジェクト名（左）＋期限（右） --}}
                                            <p class="flex justify-between items-center">
                                                {{ $task->project->name }}
                                                <span class="text-[11px] text-gray-500 font-normal">
                                                    期限 : {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('m/d') : '未設定' }}
                                                </span>
                                            </p>

                                            {{-- 下段：タスク名 --}}
                                            <p>
                                                <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:underline block text-sm">
                                                    {{ $task->name }}
                                                </a>
                                            </p>

                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-4 text-right">
                                <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:underline">すべてのタスク →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>