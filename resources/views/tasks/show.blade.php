<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">タスク詳細</h2>
    </x-slot>

    <div class="py-2 sm:py-4 max-w-4xl mx-auto px-1 sm:px-6 lg:px-8">
        <!-- 上部ボタン -->
        <div class="mb-2 sm:mb-4 flex justify-end space-x-2">
            <a href="{{ route('tasks.edit', $task->id) }}" 
            class="inline-flex items-center px-2 pr-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                編集
            </a>
            <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm">
                一覧に戻る
            </a>
        </div>

        <!-- メインカード -->
        <div class="bg-white shadow-sm rounded-xl p-6 pt-5 ">
            <!-- イベント -->
            <div class="text-sm text-gray-500 mb-1">
                {{ $task->project->name ?? 'N/A' }}
            </div>

            {{-- タスク名 + 期日 + 開始時刻 + 依頼人 + 依頼日 + ステータス・優先度 --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    {{-- タスク名 --}}
                    <h1 class="text-2xl font-bold text-gray-900">{{ $task->name }}</h1>

                    {{-- 期日（存在する場合のみ、カレンダーアイコン付き） --}}
                    @if(!empty($task->due_date))
                        <div class="flex items-center text-sm text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($task->due_date)->format('y.m/d') }}</span>
                        </div>
                    @endif

                    {{-- 開始時刻（存在する場合のみ） --}}
                    @if(!empty($task->start_time))
                        <div class="flex items-center text-sm text-gray-600 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6v6l4 2m6 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center space-x-3 text-sm text-gray-700">
                    {{-- 依頼人 --}}
                    <span>依頼：{{ $task->creator->name }}</span>

                    {{-- 依頼日 --}}
                    <span>依頼日：{{ \Carbon\Carbon::parse($task->start_date ?? now())->format('y.m/d') }}</span>

                    {{-- ステータス --}}
                    @php
                        $statusClass = match($task->status) {
                            '未完了' => 'bg-red-500 text-white',
                            '完了'   => 'bg-green-500 text-white',
                            default  => 'bg-gray-200 text-gray-800',
                        };
                    @endphp
                    <span class="px-2 py-1 rounded {{ $statusClass }}">{{ $task->status }}</span>

                    {{-- 優先度 --}}
                    <span class="px-2 py-1 bg-gray-200 rounded">{{ $task->priority ?? '-' }}</span>
                </div>
            </div>


            <hr class="mb-5 border-gray-200">

            <!-- 詳細 -->
            <p class="text-gray-800 text-base leading-relaxed whitespace-pre-line">
                {{ $task->description ?? '詳細情報は登録されていません。' }}
            </p>

            <hr class="mt-5 mb-3 border-gray-200">
            <!-- 担当者 -->
            <div class="flex items-center space-x-2 mb-3">
                <strong class="text-gray-500 text-xs">担当者：</strong>
                @forelse($task->assignees as $assignee)
                    <span class="inline-block bg-gray-100 px-2 py-0.5 rounded text-gray-700 text-sm">{{ $assignee->name }}</span>
                @empty
                    <span class="text-gray-700 text-sm">なし</span>
                @endforelse
            </div>


            <!-- 完了予定日と期日 -->
            <div class="grid grid-cols-2 gap-2 text-sm text-gray-800 mb-2">
                <div>
                    <strong class="block text-gray-500 text-xs">完了予定日</strong>
                    <span>{{ \Carbon\Carbon::parse($task->plans_date ?? now())->format('y/m/d') }}</span>
                </div>
                <div>
                    <strong class="block text-gray-500 text-xs">期日</strong>
                    <span>
                        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('y/m/d') : '-' }}
                    </span>
                </div>

            </div>


        </div>
    </div>
</x-app-layout>
