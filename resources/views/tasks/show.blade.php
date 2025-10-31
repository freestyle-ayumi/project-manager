<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">タスク詳細</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- 上部ボタン -->
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('tasks.edit', $task->id) }}" 
            class="inline-flex items-center px-2 pr-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                編集
            </a>
            <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm">
                一覧に戻る
            </a>
        </div>

        <!-- メインカード -->
        <div class="bg-white shadow-sm rounded-xl p-6 pt-5">
            <!-- プロジェクト -->
            <div class="text-sm text-gray-500 mb-1">
                {{ $task->project->name ?? 'N/A' }}
            </div>

            <!-- タスク名 + 依頼人 + 依頼日 + ステータス・優先度 -->
            <div class="flex justify-between items-start">
                <h1 class="text-2xl font-bold text-gray-900">{{ $task->name }}</h1>
                <div class="flex items-center space-x-2 text-sm">
                    {{-- 依頼人 --}}
                    <span class="text-gray-700">依頼：{{ $task->creator->name }}</span>

                    {{-- 依頼日 --}}
                    <span class="text-gray-700">
                        依頼日：{{ \Carbon\Carbon::parse($task->start_date ?? now())->format('y.m/d') }}
                    </span>

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

            <hr class="my-5 border-gray-200">
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
                    <span>{{ \Carbon\Carbon::parse($task->plans_date ?? now())->format('y-m-d') }}</span>
                </div>
                <div>
                    <strong class="block text-gray-500 text-xs">期日</strong>
                    <span>{{ $task->due_date ?? '-' }}</span>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
