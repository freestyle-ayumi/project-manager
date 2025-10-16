<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">タスク詳細</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">

    <!-- 右上ボタン -->
    <div class="mb-4 flex justify-end space-x-2">
        <a href="{{ route('tasks.edit', $task->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
            編集
        </a>
        <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
            一覧に戻る
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">

        <!-- 登録者 & 依頼日 -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <strong>登録者：</strong> {{ $task->creator->name }}
            </div>
            <div>
                <strong>依頼日：</strong> {{ $task->start_date ?? '-' }}
            </div>
        </div>

        <!-- プロジェクト -->
        <div class="mb-4">
            <strong>プロジェクト：</strong> {{ $task->project->name ?? 'N/A' }}
        </div>

        <!-- タスク名 -->
        <div class="mb-4">
            <strong>タスク名：</strong> {{ $task->name }}
        </div>

        <!-- 担当者 -->
        <div class="mb-4">
            <strong>担当者：</strong>
            @forelse($task->assignees as $assignee)
                <span class="inline-block px-2 py-1 bg-gray-200 rounded mr-1">{{ $assignee->name }}</span>
            @empty
                なし
            @endforelse
        </div>

        <!-- 詳細 -->
        <div class="mb-4">
            <strong>詳細：</strong> {{ $task->description ?? '-' }}
        </div>

        <!-- 完了予定日 & 期日 -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <strong>完了予定日：</strong> {{ $task->plans_date ?? '-' }}
            </div>
            <div>
                <strong>期日：</strong> {{ $task->due_date ?? '-' }}
            </div>
        </div>

        <!-- ステータス & 優先度 -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <strong>ステータス：</strong> {{ $task->status }}
            </div>
            <div>
                <strong>優先度：</strong> {{ $task->priority ?? '-' }}
            </div>
        </div>

    </div>
</div>
</x-app-layout>
