<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('顧客編集') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">

                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-2">
                            <label class="block font-medium text-sm text-gray-700">顧客名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}"
                                   class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @error('name') border-red-500 @enderror">
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-2">
                            <div class="mb-2">
                                <label class="block font-medium text-sm text-gray-700">略称</label>
                                <input type="text" name="abbreviation" value="{{ old('abbreviation', $client->abbreviation) }}"
                                    class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @error('abbreviation') border-red-500 @enderror">
                                @error('abbreviation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label class="block font-medium text-sm text-gray-700">電話番号</label>
                                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                    class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>

                            <div class="mb-2">
                                <label class="block font-medium text-sm text-gray-700">担当者名</label>
                                <input type="text" name="contact_person_name" value="{{ old('contact_person_name', $client->contact_person_name) }}"
                                    class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="block font-medium text-sm text-gray-700">メールアドレス</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                   class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>

                        <div class="mb-2">
                            <label class="block font-medium text-sm text-gray-700">住所</label>
                            <input type="text" name="address" value="{{ old('address', $client->address) }}"
                                   class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                        </div>

                        <div class="mb-2">
                            <label class="block font-medium text-sm text-gray-700">備考</label>
                            <textarea name="notes" rows="4"
                                   class="block py-1.5 w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            {{ old('notes', $client->notes) }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('clients.index') }}"
                               class="items-center px-4 py-2 text-xs bg-gray-300 border-transparent rounded-md hover:bg-gray-400 font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 transition ease-in-out duration-150 ms-4">
                               キャンセル</a>
                            <button type="submit"
                                    class="items-center px-4 py-2 text-xs bg-blue-600 border-transparent rounded-md hover:bg-blue-700 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 transition ease-in-out duration-150 ms-4">
                                    更新</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
