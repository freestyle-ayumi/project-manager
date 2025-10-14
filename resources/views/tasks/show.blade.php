<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">タスク詳細</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">

        <div class="mb-4">
            <strong>登録者：</strong> {{ $task->creator->name }}
        </div>

        <div class="mb-4">
            <strong>プロジェクト：</strong> {{ $task->project->name ?? 'N/A' }}
        </div>

        <div class="mb-4">
            <strong>タスク名：</strong> {{ $task->name }}
        </div>

        <div class="mb-4">
            <strong>担当者：</strong>
            @forelse($task->assignees as $assignee)
                <span class="inline-block px-2 py-1 bg-gray-200 rounded mr-1">{{ $assignee->name }}</span>
            @empty
                なし
            @endforelse
        </div>

        <div class="mb-4">
            <strong>詳細：</strong> {{ $task->description ?? '-' }}
        </div>

        <div class="mb-4">
            <strong>期日：</strong> {{ $task->due_date ?? '-' }}
        </div>

        <div class="mb-4">
            <strong>ステータス：</strong> {{ $task->status }}
        </div>

        <div class="mb-4">
            <strong>優先度：</strong> {{ $task->priority ?? '-' }}
        </div>

    </div>
</div>
</x-app-layout>
