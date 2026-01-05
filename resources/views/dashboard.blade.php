<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

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
                                        <li class="flex justify-between items-center bg-gray-50 p-1 rounded-md">
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
                                        <li class="flex justify-between items-center bg-gray-50 p-2 rounded-md">
                                            <div class="text-xs">
                                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:underline">
                                                    {{ $expense->title }}
                                                </a>
                                                <p class="text-gray-500">
                                                    ¥{{ number_format($expense->amount) }} ・
                                                    {{ \Carbon\Carbon::parse($expense->date)->format('m/d') }}
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 rounded-full bg-amber-200 text-amber-800">
                                                未承認
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-4 text-right">
                                <a href="{{ route('expenses.index') }}" class="text-sm text-indigo-600 hover:underline">イベント一覧 →</a>
                            </div>
                        </div>

                        <!-- 3. 割り当てられたタスク -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                            <h3 class="font-bold text-xl mb-1 text-gray-800">割り当てられたタスク</h3>
                            @if($assignedTasks->isEmpty())
                                <p class="text-gray-600">担当タスクはありません。</p>
                            @else
                                <ul class="space-y-3">
                                    @foreach($assignedTasks as $task)
                                        <li class="bg-gray-50 p-3 rounded-md">
                                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-blue-600 hover:underline block mb-1">
                                                {{ $task->name }}
                                            </a>
                                            <p class="text-xs text-gray-500">
                                                {{ $task->project->name }} ・
                                                期限: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('m/d') : '未設定' }}
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