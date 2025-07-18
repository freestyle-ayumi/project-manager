<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('プロジェクト一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 text-right">
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('＋新規') }}
                        </a>
                    </div>
                    @if ($projects->isEmpty())
                        <p>まだプロジェクトが登録されていません。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-gray-600 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            プロジェクト名
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            顧客名
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            担当者
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            ステータス
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            開始日
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            終了日
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            タスク
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            見積額
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            請求額
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
                                            経費合計
                                        </th>
                                        <th scope="col" class="px-2 py-2 text-left font-medium uppercase tracking-wider">
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
                                            <td class="px-2 py-1 whitespace-nowrap text-xs">
                                                {{ $project->id }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-sm">
                                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-fuchsia-600 hover:underline">
                                                    {{ $project->name }}
                                                </a>
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                                                {{ $project->client->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->user->name ?? 'N/A' }}
                                            </td>
                                            {{-- index.blade.php および show.blade.php の該当箇所 --}}
                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                @if ($project->status)
                                                    <span class="badge status-{{ $project->status->id }}">
                                                        {{ $project->status->name }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">ステータスなし</span>
                                                @endif
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y/m/d') : 'N/A' }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                                                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y/m/d') : 'N/A' }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-gray-500">
                                                @forelse ($project->tasks as $task)
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:text-blue-700 block">{{ $task->name }}</a>
                                                @empty
                                                    タスクなし
                                                @endforelse
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                @if (isset($latestQuotes[$project->id]))
                                                    <span class="flex items-center">
                                                        <a href="{{ route('quotes.show', $latestQuotes[$project->id]->id) }}" class="text-blue-500 hover:text-blue-700">
                                                            ¥{{ number_format($project->quotes_sum_total_amount) }}
                                                        </a>

                                                        @if ($latestQuotes[$project->id]->pdf_path)
                                                            <a href="{{ route('quotes.downloadPdf', $latestQuotes[$project->id]->id) }}"
                                                            target="_blank"
                                                            class="ml-2 text-red-600 hover:text-red-800"
                                                            title="PDFをダウンロード">
                                                                <!-- PDFアイコン -->
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                    class="inline-block" viewBox="0 0 16 16">
                                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27.146.23.308.535.49.875.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </span>
                                                @else
                                                    <a href="{{ route('quotes.create', ['project_id' => $project->id]) }}"
                                                    class="text-green-600 hover:text-green-800"
                                                    title="見積書を新規作成">
                                                        <!-- プラスアイコン -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                            class="inline-block" viewBox="0 0 16 16">
                                                            <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </td>

                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                @if ($project->invoices_sum_total_amount)
                                                    <a href="{{ route('invoices.index', ['project_id' => $project->id]) }}" class="text-blue-500 hover:text-blue-700">
                                                        ¥{{ number_format($project->invoices_sum_total_amount) }}
                                                    </a>
                                                @else
                                                    ¥0
                                                @endif
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                {{-- total_approved_expenses_sum は withSum(['expenses as total_approved_expenses_sum' => ...]) で定義されたもの --}}
                                                ¥{{ number_format($project->total_approved_expenses_sum ?? 0) }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $netProfit = ($project->invoices_sum_total_amount ?? 0) - ($project->total_approved_expenses_sum ?? 0);
                                                @endphp
                                                ¥{{ number_format($netProfit) }}
                                            </td>

                                            <td class="px-2 py-1 whitespace-nowrap text-right text-sm font-medium">
                                                {{-- Flexboxでアイコンを横並びに配置 --}}
                                                <div class="flex items-center justify-end space-x-2"> {{-- ここを修正 --}}
                                                    <a href="{{ route('projects.edit', $project) }}" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこのプロジェクトを削除しますか？');">
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