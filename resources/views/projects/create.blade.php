<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規プロジェクト作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
                                <x-input-label for="name" :value="__('プロジェクト名')" />
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
                                    class="block mt-1 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    required autofocus>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="col-span-4">
                                <x-input-label for="client_id" :value="__('顧客')" />
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


                        {{-- 2カラム: 開始日・終了日 --}}
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1 min-w-[150px]">
                                <label for="start_date" class="block font-medium text-sm text-gray-700">開始日</label>
                                <input
                                    id="start_date"
                                    name="start_date"
                                    type="text"
                                    value="{{ old('start_date') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                />
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <label for="end_date" class="block font-medium text-sm text-gray-700">終了日</label>
                                <input
                                    id="end_date"
                                    name="end_date"
                                    type="text"
                                    value="{{ old('end_date') ?? old('start_date') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                />
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
