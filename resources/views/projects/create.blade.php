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

                    {{-- バリデーションエラーメッセージの表示 --}}
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

                        {{-- 3カラム: プロジェクト名 / 顧客 / ステータス --}}
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="name" :value="__('プロジェクト名')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="client_id" :value="__('顧客')" />
                                <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- 顧客を選択してください --</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="project_status_id" :value="__('ステータス')" />
                                <select id="project_status_id" name="project_status_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- ステータスを選択してください --</option>
                                    @foreach ($projectStatuses as $status)
                                        <option value="{{ $status->id }}" {{ old('project_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('project_status_id')" class="mt-2" />
                            </div>
                        </div>

                        {{-- 2カラム: 開始日 / 終了日 --}}
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div class="flex-1 min-w-[150px]">
                                <x-input-label for="start_date" :value="__('開始日')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <div class="flex-1 min-w-[150px]">
                                <x-input-label for="end_date" :value="__('終了日 (任意)')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        {{-- 説明 --}}
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('説明 (任意)')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- 送信ボタン --}}
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