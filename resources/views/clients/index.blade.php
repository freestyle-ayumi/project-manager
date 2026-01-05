<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('顧客一覧') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <!-- 新規登録ボタン -->
                    <div class="mb-4 text-right">
                        <a href="{{ route('clients.create') }}"
                           class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ＋新規
                        </a>
                    </div>

                    @if ($clients->isEmpty())
                        <p>まだ顧客が登録されていません。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-gray-600 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">顧客名</th>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">略称</th>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">メールアドレス</th>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">電話番号</th>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">担当者名</th>
                                        <th class="px-2 py-2 text-left font-medium uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($clients as $client)
                                        <tr class="hover:bg-gray-50 text-gray-500 text-xs">
                                            <td class="px-2 py-1 whitespace-nowrap">{{ $client->name }}</td>
                                            <td class="px-2 py-1 whitespace-nowrap">{{ $client->abbreviation }}</td>
                                            <td class="px-2 py-1 whitespace-nowrap">{{ $client->email }}</td>
                                            <td class="px-2 py-1 whitespace-nowrap">{{ $client->phone }}</td>
                                            <td class="px-2 py-1 whitespace-nowrap">{{ $client->contact_person_name }}</td>
                                            <td class="px-2 py-1 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-1">
                                                    <!-- 編集 -->
                                                    <a href="{{ route('clients.edit', $client) }}" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                                        </svg>
                                                    </a>
                                                    <!-- 削除 -->
                                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこの顧客を削除しますか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
