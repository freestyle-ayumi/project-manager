<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('見積書一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- 検索・フィルターフォーム --}}
                    <form action="{{ route('quotes.index') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-md shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700">キーワード検索</label>
                                <input type="text" name="search" id="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="見積番号、件名、顧客名、イベント名" value="{{ request('search') }}">
                            </div>
                            <div>
                                <label for="project_filter" class="block text-sm font-medium text-gray-700">プロジェクトタイプで絞り込み</label>
                                <select name="project_filter" id="project_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="current" {{ request('project_filter', 'current') == 'current' ? 'selected' : '' }}>現在開催中のプロジェクト</option>
                                    <option value="all" {{ request('project_filter') == 'all' ? 'selected' : '' }}>全てのプロジェクト</option>
                                    <option value="past" {{ request('project_filter') == 'past' ? 'selected' : '' }}>過去のプロジェクト</option>
                                </select>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md text-sm">
                                    検索
                                </button>
                                <a href="{{ route('quotes.index') }}" class="w-full bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-md text-sm text-center">
                                    クリア
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="mb-4 text-right">
                        <a href="{{ route('quotes.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ＋新規
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        見積番号
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        イベント名
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        顧客名
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        件名
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        作成者
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        発行日
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        納品予定日
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        納品場所
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        お支払条件
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        合計金額
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        ステータス
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($quotes as $quote)
                                    <tr>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs font-medium">
                                            <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-fuchsia-600 hover:underline">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $quote->project->name ?? 'N/A' }}
                                            @if ($quote->project)
                                                <a href="{{ route('projects.show', $quote->project) }}" class="text-blue-600 hover:text-fuchsia-600 ml-1 inline-block align-middle" title="プロジェクト詳細へ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $quote->client->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $quote->subject }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $quote->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($quote->issue_date)->format('Y/m/d') }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y/m/d') : '未設定' }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $quote->delivery_location ?? '未設定' }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $quote->payment_terms ?? '未設定' }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                            ¥{{ number_format($quote->total_amount) }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $quote->status }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-right text-xs font-medium">
                                            {{-- Flexboxでアイコンを横並びに配置 --}}
                                            <div class="flex items-center justify-end space-x-1">
                                                <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-400" title="詳細">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('quotes.edit', $quote) }}" class="text-emerald-600 hover:text-emerald-400" title="編集"> 
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="inline-block" onsubmit="return confirm('この見積書を削除します。よろしいですか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
