<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg pl-3 text-gray-800 leading-tight">
            {{ __('プロジェクト編集') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">

                    {{-- バリデーションエラー --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('projects.update', $project) }}">
                        @csrf
                        @method('PUT')

                        {{-- 1段目: プロジェクト名・カラー・顧客・登録者 --}}
                        <div class="grid grid-cols-12 gap-4 mb-4">
                            {{-- プロジェクト名 --}}
                            <div class="col-span-5">
                                <x-input-label for="name">プロジェクト名<span class="text-red-500">*</span></x-input-label>
                                <input id="name" name="name" type="text" value="{{ old('name', $project->name) }}"
                                    class="block py-1.5 mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                    required autofocus>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- カラー --}}
                            <div class="col-span-1" x-data="{ open: false, selectedColor: {{ old('color', $project->color ?? $colors->first()->id) }}, selectedHex: '{{ optional($colors->firstWhere('id', (int) old('color', $project->color ?? $colors->first()->id)))->hex_code ?? '#3B82F6' }}' }" x-cloak>
                                <x-input-label>カラー</x-input-label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" class="w-full border border-gray-300 rounded-md px-3 py-1.5 mt-1 flex items-center justify-between focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        <span :style="'color:' + selectedHex">■</span>
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <ul x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-auto">
                                        @foreach ($colors as $color)
                                            <li @click="selectedColor={{ $color->id }}; selectedHex='{{ $color->hex_code }}'; open=false"
                                                class="cursor-pointer px-3 py-1.5 hover:bg-gray-100 text-center"
                                                :style="'color:' + '{{ $color->hex_code }}'">■</li>
                                        @endforeach
                                    </ul>
                                    <input type="hidden" name="color" :value="selectedColor">
                                </div>
                                <x-input-error :messages="$errors->get('color')" class="mt-2" />
                            </div>

                            {{-- 顧客 --}}
                            <div class="col-span-4">
                                <x-input-label for="client_id">顧客<span class="text-red-500">*</span></x-input-label>
                                <select id="client_id" name="client_id" class="block py-1.5 mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">-- 顧客を選択してください --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            {{-- 登録者 --}}
                            <div class="col-span-2">
                                <x-input-label for="creator" value="登録者" />
                                <input type="text" value="{{ auth()->user()->name }}" class="block w-full py-1.5 mt-1 border-gray-300 rounded-md text-sm bg-gray-100" disabled>
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            </div>
                        </div>

                        {{-- 2段目: 担当者チップ --}}
                        <div class="grid grid-cols-12 gap-4 mb-4" x-data="userChips({{ json_encode($users) }}, {{ json_encode(old('users', $projectUsers ?? [])) }})" x-init="init()" x-cloak>
                            {{-- 左: 担当者チップ --}}
                            <div class="col-span-4">
                                <x-input-label value="担当者" />
                                <div class="flex flex-wrap gap-1 border rounded-md p-2 min-h-[40px] cursor-pointer" x-ref="chipContainer" @click="$refs.input.focus()">
                                    <template x-for="user in selectedUsers" :key="user.id">
                                        <span class="flex items-center bg-indigo-100 text-indigo-800 rounded-md text-xs pl-2 pr-1 h-6">
                                            <span x-text="user.name"></span>
                                            <button type="button" class="ml-1 text-red-300 hover:text-red-500" @click.stop.prevent="removeUser(user.id)">&times;</button>
                                        </span>
                                    </template>
                                    <input type="text" x-ref="input" x-model="search" @input="filterUsers()" class="absolute opacity-0 w-0 h-0">
                                </div>
                                <template x-for="user in selectedUsers" :key="user.id">
                                    <input type="hidden" name="users[]" :value="user.id">
                                </template>
                            </div>

                            {{-- 右: 担当者一覧 --}}
                            <div class="col-span-8 border rounded-md p-2 mt-5 min-h-[40px] flex flex-wrap gap-1 items-start text-xs">
                                <template x-for="user in allUsers" :key="user.id">
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md cursor-pointer hover:bg-gray-200"
                                        :class="selectedUsers.some(u => u.id === user.id) ? 'bg-indigo-100 text-indigo-800' : ''"
                                        @click="!selectedUsers.some(u => u.id === user.id) && addUser(user)"
                                        x-text="user.name">
                                    </span>
                                </template>
                            </div>
                        </div>


                        {{-- 3カラム: 催事場所・開始日・終了日 --}}
                        <div class="flex gap-4 mb-4">
                            {{-- 催事場所 --}}
                            <div class="flex-1 min-w-[150px]">
                                <label for="venue" class="block font-medium text-sm text-gray-700">
                                    催事場所<span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="venue"
                                    name="venue"
                                    type="text"
                                    value="{{ old('venue', $project->venue) }}"
                                    class="block w-full py-1 mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                    required
                                />
                                <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                            </div>

                            {{-- 開始日 --}}
                            <div class="flex-1 min-w-[150px] relative">
                                <label for="start_date" class="block font-medium text-sm text-gray-700">
                                    開始日<span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="start_date"
                                    name="start_date"
                                    type="text"
                                    value="{{ old('start_date', $project->start_date) }}"
                                    class="block w-full pr-10 py-1.5 mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-5 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            {{-- 終了日 --}}
                            <div class="flex-1 min-w-[150px] relative">
                                <label for="end_date" class="block font-medium text-sm text-gray-700">終了日 (任意)</label>
                                <input
                                    id="end_date"
                                    name="end_date"
                                    type="text"
                                    value="{{ old('end_date', $project->end_date) }}"
                                    class="block w-full pr-10 py-1.5 mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-5 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        {{-- 説明欄 --}}
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('説明 (任意)')" />
                            <textarea id="description" name="description" rows="4"
                                class="block py-1 mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ old('description', $project->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- 保存ボタン --}}
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('プロジェクトを更新') }}
                            </x-primary-button>
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
        flatpickr("#start_date", { dateFormat: "Y-m-d", allowInput: true });
        flatpickr("#end_date", { 
            dateFormat: "Y-m-d", 
            allowInput: true, 
            defaultDate: "{{ old('end_date', $project->end_date) ?? '' }}" 
        });
    </script>
    <script>
    function userChips(allUsers, oldSelectedIds) {
        return {
            allUsers,
            selectedUsers: allUsers.filter(u => oldSelectedIds.includes(u.id)),
            search: '',
            filteredUsers: allUsers.filter(u => !oldSelectedIds.includes(u.id)),
            dropdownEl: null,

            init() {
                window.addEventListener('scroll', () => this.updateDropdownPosition());
                window.addEventListener('resize', () => this.updateDropdownPosition());
            },

            openDropdown() {
                if (!this.dropdownEl) {
                    this.dropdownEl = document.createElement('div');
                    this.dropdownEl.className = 'absolute z-[1000] bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-auto ';
                    document.body.appendChild(this.dropdownEl);
                }
                this.updateDropdownPosition();
                this.renderDropdown();
            },

            updateDropdownPosition() {
                if (!this.dropdownEl || !this.$refs.input || !this.$refs.chipContainer) return;
                const rect = this.$refs.chipContainer.getBoundingClientRect();
                this.dropdownEl.style.top = (rect.bottom + window.scrollY) + 'px';
                this.dropdownEl.style.left = (rect.left + window.scrollX) + 'px';
                this.dropdownEl.style.width = rect.width + 'px';
                this.dropdownEl.style.display = this.filteredUsers.length ? 'block' : 'none';
            },

            renderDropdown() {
                if (!this.dropdownEl) return;
                this.dropdownEl.innerHTML = '';
                this.filteredUsers.forEach(user => {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-1 cursor-pointer hover:bg-gray-100';
                    item.textContent = user.name;
                    item.onclick = () => {
                        this.addUser(user);
                        this.dropdownEl.style.display = 'none';
                    };
                    this.dropdownEl.appendChild(item);
                });
            },

            filterUsers() {
                const s = this.search.toLowerCase();
                this.filteredUsers = this.allUsers
                    .filter(u => !this.selectedUsers.some(sel => sel.id === u.id))
                    .filter(u => u.name.toLowerCase().includes(s));
                this.updateDropdownPosition();
                this.renderDropdown();
            },

            addUser(user) {
                this.selectedUsers.push(user);
                this.search = '';
                this.filterUsers();
                this.$refs.input.focus();
            },

            removeUser(id) {
                this.selectedUsers = this.selectedUsers.filter(u => u.id !== id);
                this.filterUsers();
            }
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-app-layout>
