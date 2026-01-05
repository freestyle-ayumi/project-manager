<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('新規イベント作成') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div class="py-4 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    {{-- バリデーションエラー --}}
                    @if ($errors->any())
                        <div class="mb-2 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('projects.store') }}"
                          x-data="projectForm()"
                          x-init="init()" x-cloak>
                        @csrf

                        {{-- イベント名・カラー・顧客・登録者 --}}
                        <div class="grid grid-cols-12 gap-2 mb-2">
                            {{-- イベント名 --}}
                            <div class="col-span-9 sm:col-span-5">
                                <x-input-label for="name">イベント名<span class="text-red-500">*</span></x-input-label>
                                <input id="name" name="name" type="text"
                                       x-model="eventName"
                                       @input.debounce.300ms="checkKeyword(eventName)"
                                       value="{{ old('name', '') }}"
                                       class="block py-1.5 w-full border-gray-300 rounded-md text-sm" required>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- カラー --}}
                            <div class="col-span-3 sm:col-span-1" x-data="{ open: false, selectedColor: {{ old('color', $colors->first()->id) }}, selectedHex: '{{ optional($colors->firstWhere('id', (int) old('color', $colors->first()->id)))->hex_code ?? '#3B82F6' }}' }" x-cloak>
                                <x-input-label>カラー</x-input-label>
                                <div class="relative">
                                    <button type="button"
                                            @click="open = !open"
                                            class="w-full border border-gray-300 rounded-md pl-3 pr-3 sm:pr-2 py-1.5 flex items-center justify-between focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        <span :style="'color:' + selectedHex">■</span>
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <ul x-show="open"
                                        @click.outside="open = false"
                                        class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-auto">
                                        @foreach ($colors as $color)
                                            <li @click="selectedColor={{ $color->id }}; selectedHex='{{ $color->hex_code }}'; open=false"
                                                class="cursor-pointer px-3 py-1.5 hover:bg-gray-100 text-center"
                                                :style="'color:' + '{{ $color->hex_code }}'">
                                                ■
                                            </li>
                                        @endforeach
                                    </ul>

                                    <input type="hidden" name="color" :value="selectedColor">
                                </div>
                                <x-input-error :messages="$errors->get('color')" class="mt-2" />
                            </div>

                            {{-- 顧客 --}}
                            <div class="col-span-9 sm:col-span-5">
                                <x-input-label for="client_id">顧客<span class="text-red-500">*</span></x-input-label>
                                <select id="client_id" name="client_id"
                                        class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">-- 顧客を選択してください --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            {{-- 登録者 --}}
                            <div class="col-span-3 sm:col-span-1">
                                <x-input-label for="creator" value="登録者" />
                                <input type="text" value="{{ auth()->user()->name }}"
                                       class="block w-full py-1.5 border-gray-300 rounded-md text-sm bg-gray-100" disabled>
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            </div>
                        </div>

                        {{-- 担当者 --}}
                        <div class="grid grid-cols-12 gap-4 mb-2">
                            <div class="col-span-4">
                                <x-input-label value="担当者" />
                                <div class="flex flex-wrap items-center gap-1 border border-gray-300 rounded-md pl-2 pr-1 min-h-[35px] text-sm">
                                    <template x-for="user in selectedUsers" :key="user.id">
                                        <span class="flex items-center bg-indigo-100 text-indigo-800 rounded-md text-xs pl-2 pr-1 h-6">
                                            <span x-text="user.name"></span>
                                            <button type="button" class="ml-1 text-red-300 hover:text-red-500"
                                                    @click.stop.prevent="removeUser(user.id)">&times;</button>
                                        </span>
                                    </template>
                                </div>
                                <template x-for="user in selectedUsers" :key="user.id">
                                    <input type="hidden" name="users[]" :value="user.id">
                                </template>
                            </div>

                            <div class="col-span-8 border border-gray-300 rounded-md px-2 py-1 flex flex-wrap gap-1 items-center text-xs mt-5">
                                <template x-for="user in allUsers" :key="user.id">
                                    <span
                                        class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded-md cursor-pointer hover:bg-gray-200"
                                        :class="selectedUsers.some(u => u.id === user.id) ? 'bg-indigo-100 text-indigo-800' : ''"
                                        @click="!selectedUsers.some(u => u.id === user.id) && addUser(user)"
                                        x-text="user.name">
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- 催事場所・日付 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label for="venue" class="block font-medium text-sm text-gray-700">
                                    催事場所<span class="text-red-500">*</span>
                                </label>
                                <input id="venue" name="venue" type="text" value="{{ old('venue') }}"
                                       class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                       required>
                                <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                            </div>

                            {{-- PC用開始日 --}}
                            <div class="hidden md:block relative">
                                <label for="start_date" class="block font-medium text-sm text-gray-700">開始日<span class="text-red-500">*</span></label>
                                <input id="start_date" name="start_date" type="text"
                                       value="{{ old('start_date') }}"
                                       class="block w-full pr-10 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            {{-- PC用終了日 --}}
                            <div class="hidden md:block relative">
                                <label for="end_date" class="block font-medium text-sm text-gray-700">終了日 (任意)</label>
                                <input id="end_date" name="end_date" type="text"
                                       value="{{ old('end_date') }}"
                                       class="block w-full pr-10 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        {{-- モバイル用 --}}
                        <div class="grid grid-cols-2 gap-4 mt-2 md:hidden">
                            <div class="relative">
                                <label for="start_date_mobile" class="block font-medium text-sm text-gray-700">開始日<span class="text-red-500">*</span></label>
                                <input id="start_date_mobile" name="start_date_mobile" type="text"
                                       value="{{ old('start_date') }}"
                                       class="block w-full pr-10 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div class="relative">
                                <label for="end_date_mobile" class="block font-medium text-sm text-gray-700">終了日 (任意)</label>
                                <input id="end_date_mobile" name="end_date_mobile" type="text"
                                       value="{{ old('end_date') }}"
                                       class="block w-full pr-10 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                        </div>

                        {{-- 説明 --}}
                        <div class="mb-2">
                            <x-input-label for="description" :value="__('説明 (任意)')" />
                            <textarea id="description" name="description" rows="4"
                                      class="block py-1.5 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- チェックリスト --}}
                        <script>
                            window.ALL_USERS = @json($users);
                            window.OLD_SELECTED_USER_IDS = @json(old('users', []));
                            window.OLD_EVENT_NAME = @json(old('name', ''));
                            window.KEYWORDS = @json($keywordFlags);

                            function projectForm() {
                                const allUsers = window.ALL_USERS || [];
                                const oldSelected = (window.OLD_SELECTED_USER_IDS || []).map(id => String(id));
                                const initialName = window.OLD_EVENT_NAME || '';
                                const keywords = window.KEYWORDS || [];

                                return {
                                    eventName: initialName,
                                    manualItem: '',
                                    checklists: [],
                                    allUsers: allUsers,
                                    selectedUsers: allUsers.filter(u => oldSelected.includes(String(u.id))),
                                    keywords: keywords,
                                    init() { if (this.eventName) this.checkKeyword(this.eventName); },
                                    addItem(name) { if (!this.checklists.some(i => i.name === name)) this.checklists.push({ name, status: '未', link: '' }); },
                                    advanceStatus(index) { const item = this.checklists[index]; if (!item) return; if (item.status==='未') item.status='作'; else if(item.status==='作') item.status='済'; },
                                    checkKeyword(name) { this.checklists=[]; if(!name) return; this.keywords.forEach(k=>{ if(typeof k.keyword==='string' && name.includes(k.keyword)) { (k.templates||[]).forEach(t=>{ if(t&&t.name) this.addItem(t.name); }); } }); },
                                    addManualItem(name) { if(name && name.trim()!=='') this.addItem(name.trim()); },
                                    addUser(user) { if(!this.selectedUsers.some(u=>u.id===user.id)) this.selectedUsers.push(user); },
                                    removeUser(id) { this.selectedUsers=this.selectedUsers.filter(u=>u.id!==id); },
                                    removeItem(index) { this.checklists.splice(index,1); }
                                };
                            }
                        </script>

                        {{-- チェックリスト表示・手動追加 --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3">
                                <div class="block font-medium text-sm text-gray-700 mb-2">テンプレートチェックリスト(イベント名で自動追加)</div>
                                <template x-for="(item,index) in checklists" :key="index">
                                    <div class="flex items-center justify-between pb-1 pl-2 rounded text-xs">
                                        <div><span x-text="item.name"></span></div>
                                        <div class="flex items-center">
                                            <button type="button" @click="advanceStatus(index)"
                                                    class="px-2 py-1 rounded text-white"
                                                    :class="{'bg-red-600': item.status==='未','bg-amber-500': item.status==='作','bg-green-500': item.status==='済'}"
                                                    x-text="item.status"></button>
                                            <button type="button" @click="removeItem(index)" class="ml-1 px-2 py-1 rounded text-red-600 hover:text-red-400 hover:bg-red-5">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="hidden" :name="'checklists['+index+'][name]'" :value="item.name">
                                        <input type="hidden" :name="'checklists['+index+'][status]'" :value="item.status">
                                        <input type="hidden" :name="'checklists['+index+'][link]'" :value="item.link">
                                    </div>
                                </template>

                                <div class="block pt-3 text-xs text-blue-600 text-right">
                                    <a href="{{ route('admin.keyword_flags.index') }}" class="inline-flex items-center gap-1">
                                        チェック項目テンプレ変更
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="border border-y-0 border-r-0 p-3">
                                <h3 class="block font-medium text-sm text-gray-700">項目の追加</h3>
                                <div class="mb-2 flex gap-2 items-center">
                                    <input type="text"
                                           x-model="manualItem"
                                           placeholder="項目を追加"
                                           class="block w-full pr-10 py-1.5 border-gray-300 rounded-md text-sm flex-1">
                                    <button type="button"
                                            @click="addManualItem(manualItem); manualItem=''"
                                            class="inline-flex items-center justify-center px-3 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-blue-600 transition">
                                        追加
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
    <script>
        flatpickr.localize(flatpickr.l10ns.ja);

        ["#start_date","#start_date_mobile"].forEach(id=>{
            flatpickr(id, { dateFormat:"Y-m-d", allowInput:true, defaultDate: document.querySelector(id).value || new Date() });
        });
        ["#end_date","#end_date_mobile"].forEach(id=>{
            flatpickr(id, { dateFormat:"Y-m-d", allowInput:true, defaultDate: document.querySelector(id).value || null });
        });
    </script>
</x-app-layout>
