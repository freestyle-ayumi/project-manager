<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('経費一覧') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">

                    {{-- 成功メッセージ --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- 検索・フィルターフォーム --}}
                    <form action="{{ route('expenses.index') }}" method="GET" class="mb-2 p-2 rounded-md shadow-sm bg-white">
                        <div class="grid grid-cols-12 gap-2">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="申請者名・イベント名・ステータス"
                                class="col-span-12 md:col-span-8 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            <select name="status_filter" id="status_filter"
                                    class="h-8 mt-0.5 col-span-12 sm:col-span-2 md:col-span-2 border border-gray-300 rounded-md py-0 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all" {{ request('status_filter', 'all') == 'all' ? 'selected' : '' }}>全て</option>
                                <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>申請中</option>
                                <option value="approved" {{ request('status_filter') == 'approved' ? 'selected' : '' }}>承認済み</option>
                                <option value="rejected" {{ request('status_filter') == 'rejected' ? 'selected' : '' }}>否認</option>
                            </select>
                            <button type="submit"
                                    class="h-8 w-full flex rounded-md mt-0.5 pt-2 pb-1.5 items-center justify-center text-white text-xs bg-indigo-600 hover:bg-indigo-700">
                                検索
                            </button>
                            <a href="{{ route('expenses.index') }}"
                               class="h-8 w-full flex rounded-md mt-0.5 pt-2 pb-1.5 mr-5 sm:mr-0 items-center justify-center text-white text-xs bg-gray-400 hover:bg-gray-500">
                                クリア
                            </a>
                        </div>
                    </form>

                    {{-- 新規作成ボタン --}}
                    <div class="mb-4 text-right">
                        <a href="{{ route('expenses.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            ＋新規
                        </a>
                    </div>

                    {{-- 一覧テーブル --}}
                    @php
                        $allowedRoles = ['master', 'developer', 'accounting'];
                        $canEdit = in_array(auth()->user()->role->name, $allowedRoles);
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">申請日</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">申請者</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">関連イベント</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">経費合計金額</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">ステータス</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($expense->date)->format('Y/m/d') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                            {{ $expense->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            {{ $expense->project->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                            ¥{{ number_format($expense->amount) }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $expense->status->name === '承認済み' ? 'bg-green-100 text-green-800' :
                                                  ($expense->status->name === '申請中' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $expense->status->name ?? 'N/A' }}
                                                </span>
                                                @if($canEdit)
                                                    <form action="{{ route('expenses.updateStatus', $expense) }}" method="POST" class="flex items-center space-x-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="expense_status_id" class="border rounded px-1 text-xs">
                                                            @foreach($statuses as $status)
                                                                <option value="{{ $status->id }}" {{ $expense->expense_status_id == $status->id ? 'selected' : '' }}>
                                                                    {{ $status->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-2 py-1 rounded">更新</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right text-xs font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-400" title="詳細">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                    </svg>
                                                </a>
                                                @if($canEdit)
                                                    <a href="{{ route('expenses.edit', $expense) }}" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block" onsubmit="return confirm('この経費申請を削除します。よろしいですか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-400 mt-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- ページネーション --}}
                        <div class="mt-4">
                            {{ $expenses->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
