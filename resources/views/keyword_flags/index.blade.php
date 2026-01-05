<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('キーワード管理') }}
        </h2>
    </x-slot>

    <div class="sm:py-2 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="text-right mb-2">
                    <a href="{{ route('admin.keyword_flags.create') }}" class="btn-primary">
                        ＋新規
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-1 text-left">ID</th>
                            <th class="px-2 py-1 text-left">キーワード</th>
                            <th class="px-2 py-1 text-left">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($keywordFlags as $flag)
                        <tr>
                            <td class="px-2 py-1">{{ $flag->id }}</td>
                            <td class="px-2 py-1">{{ $flag->keyword }}</td>
                            <td class="px-2 py-1">
                                <a href="{{ route('admin.keyword_flags.edit', $flag) }}" class="text-emerald-600 hover:text-emerald-400">編集</a>
                                <form action="{{ route('admin.keyword_flags.destroy', $flag) }}" method="POST" class="inline-block" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-400 ml-1">削除</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $keywordFlags->links() }}
            </div>
        </div>
    </div>
</x-app-layout>