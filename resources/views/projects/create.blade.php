<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規プロジェクト作成') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">
                    <h3 class="font-bold text-xl mb-4">プロジェクト情報入力</h3>

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

                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf

                        {{-- 3カラム: プロジェクト名・顧客・ステータス（12グリッド換算） --}}
                        <div class="grid grid-cols-12 gap-4 mb-4">
                            <div class="col-span-6">
                                <x-input-label for="name">
                                    プロジェクト名<span class="text-red-500">*</span>
                                </x-input-label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
                                    class="block mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    required autofocus>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="col-span-4">
                                    <x-input-label for="client_id">
                                        顧客<span class="text-red-500">*</span>
                                    </x-input-label>
                                <select id="client_id" name="client_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- 顧客を選択してください --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            <div class="col-span-2">
                                <x-input-label for="project_status_id" :value="__('ステータス')" />
                                <select id="project_status_id" name="project_status_id" required
                                    class="block mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($projectStatuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ old('project_status_id', 1) == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('project_status_id')" class="mt-2" />
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
                        value="{{ old('venue') }}"
                        class="block w-full mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
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
                        value="{{ old('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        class="block w-full pr-10 mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <!-- カレンダーアイコン -->
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
                    <label for="end_date" class="block font-medium text-sm text-gray-700">終了日</label>
                    <input
                        id="end_date"
                        name="end_date"
                        type="text"
                        value="{{ old('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                        class="block w-full pr-10 mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <!-- カレンダーアイコン -->
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

                        {{-- Flatpickr の CSS/JS --}}
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>

                        <script>
                        flatpickr.localize(flatpickr.l10ns.ja);

                        flatpickr("#start_date", {
                            dateFormat: "Y-m-d",
                            allowInput: true
                        });

                        flatpickr("#end_date", {
                            dateFormat: "Y-m-d",
                            allowInput: true,
                            defaultDate: document.getElementById('start_date').value
                        });
                        </script>




                        {{-- 説明欄 --}}
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('説明 (任意)')" />
                            <textarea id="description" name="description" rows="4"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('プロジェクトを保存') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
