<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('顧客編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- 顧客名 -->
                        <div class="mb-4">
                            <label class="block text-gray-700">顧客名</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <!-- メールアドレス -->
                        <div class="mb-4">
                            <label class="block text-gray-700">メールアドレス</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <!-- 電話番号 -->
                        <div class="mb-4">
                            <label class="block text-gray-700">電話番号</label>
                            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <!-- 住所 -->
                        <div class="mb-4">
                            <label class="block text-gray-700">住所</label>
                            <input type="text" name="address" value="{{ old('address', $client->address) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>

                        <!-- 備考 -->
                        <div class="mb-4">
                            <label class="block text-gray-700">備考</label>
                            <textarea name="notes" rows="4"
                                      class="w-full border border-gray-300 rounded px-3 py-2">{{ old('notes', $client->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('clients.index') }}"
                               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">キャンセル</a>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">更新</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
