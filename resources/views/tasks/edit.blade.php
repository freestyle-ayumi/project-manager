<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">タスク編集</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">

        <form action="{{ route('tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- イベント選択 -->
            <div class="mb-2">
                <label class="block font-medium text-sm text-gray-700">イベント</label>
                <select name="project_id" class="w-full block py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @if($task->project_id == $project->id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-2">
                <!-- タスク名 -->
                <div>
                    <label class="block font-medium text-sm text-gray-700">タスク名</label>
                    <input type="text" name="name" value="{{ old('name', $task->name) }}" class="w-full block py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" required>
                </div>
                <!-- 優先度 -->
                <div>
                    <label class="block font-medium text-sm text-gray-700">優先度</label>
                    <select name="priority" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        <option value="高" {{ $task->priority === '高' ? 'selected' : '' }}>高</option>
                        <option value="中" {{ $task->priority === '中' ? 'selected' : '' }}>中</option>
                        <option value="低" {{ $task->priority === '低' ? 'selected' : '' }}>低</option>
                    </select>
                </div>
            </div>
            
            <!-- 依頼人 依頼日 開始時刻 ステータス -->
            <div class="grid grid-cols-4 gap-4 mb-2">
                <div>
                    <label class="block font-medium text-sm text-gray-700">依頼人</label>
                    <input type="text" value="{{ $task->creator->name }}" disabled class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                </div>
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">依頼日</label>
                    <input
                        type="text"
                        name="start_date"
                        id="start_date"
                        value="{{ old('start_date', $task->start_date) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 
                            focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                        placeholder="yyyy-mm-dd"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 mt-5 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- 開始時刻 -->
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">開始時刻</label>
                    <input
                        type="time"
                        name="start_time"
                        id="start_time"
                        value="{{ old('start_time', $task->start_time) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                    <!-- 時計アイコン -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 mt-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6v6l4 2m6 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                </div>
                
                <div>
                    <label class="block font-medium text-sm text-gray-700">ステータス</label>
                    <select name="status" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        <option value="未完了" {{ $task->status === '未完了' ? 'selected' : '' }}>未完了</option>
                        <option value="完了" {{ $task->status === '完了' ? 'selected' : '' }}>完了</option>
                    </select>
                </div>
            </div>
            
            <!-- 詳細 -->
            <div class="mb-2">
                <label class="block font-medium text-sm text-gray-700">詳細</label>
                <textarea name="description" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" rows="4">{{ old('description', $task->description) }}</textarea>
            </div>

            <!-- 担当者選択 -->
            <div class="mb-3">
                <label class="block font-medium text-sm text-gray-700">担当者</label>

                <!-- ユーザー単体 -->
                <div class="ml-5">
                    @foreach($users as $user)
                        <label class="inline-flex items-center mr-4">
                            <input type="checkbox" name="assignees[]" value="{{ $user->id }}" class="form-checkbox border-gray-400"
                                {{ in_array($user->id, $selectedAssignees) ? 'checked' : '' }}>
                            <span class="ml-2 text-xs">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- ロール単位 -->
                <div class="ml-5">
                    <span class="text-gray-500 text-xs">まとめて追加</span>
                    <div>
                        @foreach($roles as $role)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-checkbox border-gray-400">
                                <span class="ml-2 text-xs">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 完了希望日 期日） -->
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">完了希望日</label>
                    <input
                        type="text"
                        name="plans_date"
                        id="plans_date"
                        value="{{ old('plans_date', $task->plans_date) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 mt-5 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">期日</label>
                    <input
                        type="text"
                        name="due_date"
                        id="due_date"
                        value="{{ old('due_date', $task->due_date) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 mt-5 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="items-center px-4 py-2 text-xs bg-blue-600 border-transparent rounded-md hover:bg-blue-700 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 transition ease-in-out duration-150 ms-4">
                    更新
                </button>
            </div>

        </form>
    </div>
</div>
{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
<script>
    flatpickr.localize(flatpickr.l10ns.ja);

    flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: "{{ old('start_date', $task->start_date) }}"
    });

    flatpickr("#plans_date", {
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: "{{ old('plans_date', $task->plans_date) }}"
    });

    flatpickr("#due_date", {
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: "{{ old('due_date', $task->due_date) }}"
    });
</script>

</x-app-layout>
