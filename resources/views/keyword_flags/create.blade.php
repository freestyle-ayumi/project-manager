<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('キーワード新規作成') }}
        </h2>
    </x-slot>

    <div class="sm:py-2 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form action="{{ route('admin.keyword_flags.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="keyword" class="block text-sm font-medium text-gray-700">キーワード</label>
                        <input type="text" name="keyword" id="keyword" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('keyword') }}" required>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn-primary">保存</button>
                        <a href="{{ route('admin.keyword_flags.index') }}" class="btn-secondary ml-2">キャンセル</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>