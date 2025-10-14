<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">タスク作成</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            <!-- 登録者 -->
            <div class="mb-4">
                <label class="block text-gray-700">登録者</label>
                <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>

            <!-- プロジェクト選択 -->
            <div class="mb-4">
                <label class="block text-gray-700">プロジェクト</label>
                <select name="project_id" class="w-full border rounded px-3 py-2">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- タスク名 -->
            <div class="mb-4">
                <label class="block text-gray-700">タスク名</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- 担当者選択 -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">担当者</label>

                <!-- ユーザー単体 -->
                <div class="mb-2">
                    @foreach($users as $user)
                        <label class="inline-flex items-center mr-4 mb-2">
                            <input type="checkbox" name="assignees[]" value="{{ $user->id }}" class="form-checkbox">
                            <span class="ml-2">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- ロール単位 -->
                <div class="mt-2">
                    <span class="text-gray-500 text-sm">ロール単位で追加</span>
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
                <textarea name="description" class="w-full border rounded px-3 py-2" rows="4"></textarea>
            </div>

            <!-- 期日 -->
            <div class="mb-4 relative">
                <label class="block text-gray-700">期日</label>
                <input
                    id="due_date"
                    name="due_date"
                    type="text"
                    value="{{ old('due_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                    class="w-full border rounded px-3 py-2 pr-10"
                    placeholder="yyyy-mm-dd"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-5 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                    </svg>
                </div>
            </div>

            <!-- ステータス -->
            <div class="mb-4">
                <label class="block text-gray-700">ステータス</label>
                <select name="status" class="w-full border rounded px-3 py-2">
                    <option value="未完了" selected>未完了</option>
                    <option value="完了">完了</option>
                </select>
            </div>

            <!-- 優先度 -->
            <div class="mb-4">
                <label class="block text-gray-700">優先度</label>
                <select name="priority" class="w-full border rounded px-3 py-2">
                    <option value="高">高</option>
                    <option value="中" selected>中</option>
                    <option value="低">低</option>
                </select>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    作成
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

    flatpickr("#due_date", {
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: "{{ old('due_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
    });
</script>

</x-app-layout>
