<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">タスク作成</h2>
</x-slot>

<div class="py-4 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <!-- イベント -->
            <div class="mb-2">
                <label class="block font-medium text-sm text-gray-700">イベント</label>
                <select name="project_id" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-2">
                <!-- タスク名 -->
                <div>
                    <label class="block font-medium text-sm text-gray-700">タスク名</label>
                    <input type="text" name="name" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" required>
                </div>
                <!-- 優先度 -->
                <div>
                    <label class="block font-medium text-sm text-gray-700">優先度</label>
                    <select name="priority" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        <option value="高">高</option>
                        <option value="中" selected>中</option>
                        <option value="低">低</option>
                    </select>
                </div>
            </div>

            <!--  依頼人 依頼日 開始時刻 ステータス -->
            <div class="grid grid-cols-4 gap-4 mb-2">
                <div>
                    <label class="block font-medium text-sm text-gray-700">依頼人</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                </div>
                <!-- 依頼日 -->
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">依頼日</label>
                    <input
                        type="text"
                        name="start_date"
                        id="start_date"
                        value="{{ old('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md 
                            focus:border-indigo-500 focus:ring focus:ring-indigo-200 
                            focus:ring-opacity-50 text-sm"
                        placeholder="yyyy-mm-dd"
                    />
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
                        value="{{ old('start_time') }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md 
                            focus:border-indigo-500 focus:ring focus:ring-indigo-200 
                            focus:ring-opacity-50 text-sm pl-8"
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
                        <option value="未完了" selected>未完了</option>
                        <option value="完了">完了</option>
                    </select>
                </div>
            </div>
            
            <!-- 詳細 -->
            <div class="mb-2">
                <label class="block font-medium text-sm text-gray-700">詳細</label>
                <textarea name="description" class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" rows="4">{{ old('description') }}</textarea>
            </div>

            <!-- 関連URL（複数登録可能） -->
            <div>
                <label class="block font-medium text-sm text-gray-700">関連URL</label>
                
                <div id="url-fields" class="space-y-2">
                    <!-- 初期1行 -->
                    <div class="url-field bg-gray-50 p-2 rounded-md border border-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                            <div>
                                <input type="text" name="urls[0][title]" placeholder="タイトル" 
                                    class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div>
                                <input type="text" name="urls[0][memo]" placeholder="メモ" 
                                    class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="url" name="urls[0][url]" placeholder="URL"
                                class="flex-1 block py-1.5 border-gray-300 rounded-md focus:border-indigo-400 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 sm:text-sm">
                            <button type="submit" class="text-red-600 hover:text-red-400" title="削除">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 追加ボタン -->
                <div class="mt-1 flex justify-end">
                    <button type="button" id="add-url" class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-600 hover:text-white transition text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        追加
                    </button>
                </div>
            </div>

            <!-- 担当者 -->
            <div class="mb-3">
                <label class="block font-medium text-sm text-gray-700">担当者</label>
                <div class="ml-5">
                    @foreach($users as $user)
                        <label class="inline-flex items-center mr-4">
                            <input type="checkbox" name="assignees[]" value="{{ $user->id }}" class="form-checkbox border-gray-400"
                                {{ (isset($selectedUserId) && $selectedUserId == $user->id) ? 'checked' : '' }}>
                            <span class="ml-2 text-xs">{{ $user->name }}</span>
                        </label>
                    @endforeach
                </div>

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

            <!--完了希望日 期日  -->
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div class="relative">
                    <label class="block font-medium text-sm text-gray-700">完了希望日</label>
                    <input
                        type="text"
                        name="plans_date"
                        id="plans_date"
                        value="{{ old('plans_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                        placeholder="yyyy-mm-dd"
                    />
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
                        value="{{ old('due_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                        placeholder="yyyy-mm-dd"
                    />
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
                    登録
                </button>
            </div>

        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
<script>
    flatpickr.localize(flatpickr.l10ns.ja);
    flatpickr("#start_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('start_date', $defaultDate ?? \Carbon\Carbon::today()->format('Y-m-d')) }}" });
    flatpickr("#due_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('due_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" });
    flatpickr("#plans_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('plans_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" });
</script>
<!-- JavaScript（動的追加/削除） -->
<script>
    let urlIndex = 1; // 初期1行あるので次は1から

    document.getElementById('add-url').addEventListener('click', function() {
        const container = document.getElementById('url-fields');
        const field = document.createElement('div');
        field.className = 'url-field bg-gray-50 p-4 rounded-md border border-gray-200';
        field.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                <div>
                    <input type="text" name="urls[${urlIndex}][title]" placeholder="タイトル（任意）" 
                           class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                </div>
                <div>
                    <input type="text" name="urls[${urlIndex}][memo]" placeholder="メモ（任意）" 
                           class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="url" name="urls[${urlIndex}][url]" placeholder="URL（必須）" required
                       class="flex-1 block py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                <button type="button" class="remove-url p-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(field);
        urlIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-url')) {
            e.target.closest('.url-field').remove();
        }
    });
</script>

</x-app-layout>
