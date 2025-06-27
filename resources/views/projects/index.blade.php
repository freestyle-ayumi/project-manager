<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('プロジェクト一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 text-right">
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('＋新規') }}
                        </a>
                    </div>
                    @if ($projects->isEmpty())
                        <p>まだプロジェクトが登録されていません。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            プロジェクト名
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            顧客名
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            担当者
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ステータス
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            開始日
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            終了日
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            タスク
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            見積額
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            請求額
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            経費合計
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            実利
                                        </th>
                                        <th scope="col" class="relative px-2 py-2">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($projects as $project)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-2 py-3 whitespace-nowrap text-sm">
                                                {{ $project->id }}
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm">
                                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $project->name }}
                                                </a>
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->client->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->user->name ?? 'N/A' }}
                                            </td>
                                            {{-- index.blade.php および show.blade.php の該当箇所 --}}
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @if ($project->status)
                                                    <span class="badge status-{{ $project->status->id }}">
                                                        {{ $project->status->name }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">ステータスなし</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y/m/d') : 'N/A' }}
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y/m/d') : 'N/A' }}
                                            </td>
                                            {{-- ここから追加するTDタグ --}}
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @forelse ($project->tasks as $task)
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:text-blue-700 block">{{ $task->name }}</a>
                                                @empty
                                                    タスクなし
                                                @endforelse
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @if ($project->quotes_sum_total_amount)
                                                    <a href="{{ route('quotes.index', ['project_id' => $project->id]) }}" class="text-blue-500 hover:text-blue-700">
                                                        ¥{{ number_format($project->quotes_sum_total_amount) }}
                                                    </a>
                                                @else
                                                    ¥0
                                                @endif
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @if ($project->invoices_sum_total_amount)
                                                    <a href="{{ route('invoices.index', ['project_id' => $project->id]) }}" class="text-blue-500 hover:text-blue-700">
                                                        ¥{{ number_format($project->invoices_sum_total_amount) }}
                                                    </a>
                                                @else
                                                    ¥0
                                                @endif
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{-- total_approved_expenses_sum は withSum(['expenses as total_approved_expenses_sum' => ...]) で定義されたもの --}}
                                                ¥{{ number_format($project->total_approved_expenses_sum ?? 0) }}
                                            </td>
                                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $netProfit = ($project->invoices_sum_total_amount ?? 0) - ($project->total_approved_expenses_sum ?? 0);
                                                @endphp
                                                ¥{{ number_format($netProfit) }}
                                            </td>

                                            <td class="px-2 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこのプロジェクトを削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                        </svg>
                                                    </button>
                                                </form>
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