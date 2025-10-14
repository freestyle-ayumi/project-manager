<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">タスク編集</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">

        <form action="{{ route('tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- 登録者（変更不可） -->
            <div class="mb-4">
                <label class="block text-gray-700">登録者</label>
                <input type="text" value="{{ $task->creator->name }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>

            <!-- プロジェクト選択 -->
            <div class="mb-4">
                <label class="block text-gray-700">プロジェクト</label>
                <select name="project_id" class="w-full border rounded px-3 py-2">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $project->id == $task->project_id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- タスク名 -->
            <div class="mb-4">
                <label class="block text-gray-700">タスク名</label>
                <input type="text" name="name" value="{{ old('name', $task->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- 担当者選択 -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">担当者</label>

                <!-- ユーザー単体 -->
                <div class="mb-2">
                    @foreach($users as $user)
                        <label class="inline-flex items-center mr-4 mb-2">
                            <input type="checkbox" name="assignees[]" value="{{ $user->id }}" class="form-checkbox"
                                {{ in_array($user->id, $selectedAssignees) ? 'checked' : '' }}>
                            <span class="ml-2">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- ロール単位 -->
                <div class="mt-2">
                    <span class="text-gray-500 text-sm">ロール単位で担当者を追加</span>
                    <div class="mt-1">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center mr-4 mb-2">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-checkbox">
                                <span class="ml-2">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 詳細 -->
            <div class="mb-4">
                <label class="block text-gray-700">詳細</label>
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="4">{{ old('description', $task->description) }}</textarea>
            </div>

            <!-- 期日 -->
            <div class="mb-4">
                <label class="block text-gray-700">期日</label>
                <input type="date" name="due_date" value="{{ old('due_date', $task->due_date) }}" class="w-full border rounded px-3 py-2">
            </div>

            <!-- ステータス -->
            <div class="mb-4">
                <label class="block text-gray-700">ステータス</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="未完了" {{ $task->status === '未完了' ? 'selected' : '' }}>未完了</option>
                    <option value="完了" {{ $task->status === '完了' ? 'selected' : '' }}>完了</option>
                </select>
            </div>

            <!-- 優先度 -->
            <div class="mb-4">
                <label class="block text-gray-700">優先度</label>
                <select name="priority" class="w-full border rounded px-3 py-2">
                    <option value="高" {{ $task->priority === '高' ? 'selected' : '' }}>高</option>
                    <option value="中" {{ $task->priority === '中' ? 'selected' : '' }}>中</option>
                    <option value="低" {{ $task->priority === '低' ? 'selected' : '' }}>低</option>
                </select>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    更新
                </button>
            </div>

        </form>
    </div>
</div>
</x-app-layout>
