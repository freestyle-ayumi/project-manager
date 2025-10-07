<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('プロジェクト詳細') }}
        </h2>
    </x-slot>

    <div class="py-4 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 overflow-hidden shadow-sm sm:rounded-lg p-6" style="@media (max-width: 400px) {padding: 0.5rem;}">
                {{-- 主要情報をカード形式で表示 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- 左カラム --}}
                    <div class="bg-white border border-slate-200 p-6 rounded-lg shadow-sm">
                        {{-- プロジェクト名・催事場所・ステータスを3カラムで表示 --}}
                        <div class="grid grid-cols-12 gap-4 items-center mb-6 border-b pb-3">
                            {{-- 左: プロジェクト名 --}}
                            <h3 class="font-bold text-2xl col-span-6">{{ $project->name }}</h3>

                            {{-- 中央: 顧客 --}}
                            <div class="col-span-4">
                                <dt class="font-medium">顧客</dt>
                                <dd class="ml-0">{{ $project->client->name ?? 'N/A' }}</dd>
                            </div>

                            {{-- 右: ステータス --}}
                            <div class="col-span-2 text-right">
                                @if ($project->status)
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 truncate">
                                        {{ $project->status->name }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 truncate">
                                        ステータスなし
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- 下に他の情報 --}}
                        <dl class="space-y-4">
                            <!-- 上段: 2カラム -->
                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-7">
                                    <dt class="font-medium">開催日程</dt>
                                    <dd class="ml-0 text-sm">
                                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y年m月d日') : 'N/A' }}
                                        〜
                                        {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y年m月d日') : 'N/A' }}
                                    </dd>
                                </div>

                                <div class="col-span-5">
                                    <dt class="font-medium">催事場所</dt>
                                    <dd class="ml-0">{{ $project->venue ?? 'N/A' }}</dd>
                                </div>
                            </div>

                            <!-- 下段: 説明は1カラム -->
                            <div>
                                <dt class="font-medium">説明</dt>
                                <dd class="ml-0 whitespace-pre-wrap">{{ $project->description ?? 'N/A' }}</dd>
                            </div>
                        </dl>

                    </div>

                    {{-- 右カラム --}}
                    <div class="bg-white border border-slate-200 p-6 rounded-lg shadow-sm">
                        {{-- 担当者セクションを横並びに変更 --}}
                        <div class="flex items-start mb-4 border-b border-dashed pb-2">
                            <dt class="font-medium mr-2 whitespace-nowrap">担当者:</dt>
                            <dd class="ml-0">{{ $project->user->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium">タスク</dt>
                            <dd class="ml-0">                                    @forelse ($project->tasks as $task)
                                        <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 hover:underline block">{{ $task->name }}</a>
                                    @empty
                                        タスクなし
                                    @endforelse</dd>
                        </div>
                    </div>
                </div>

                {{-- 情報テーブル --}}
                <h4 class="font-bold text-xl pl-1 pb-2">情報</h4>
                <div class="overflow-x-auto mb-8 rounded-lg shadow-sm">
                    <table class="min-w-full border border-gray-200" style="border-collapse: collapse;">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">
                                    見積額
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">
                                    請求額
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">
                                    経費合計
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">
                                    実利
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border border-gray-200">
                                    @if ($project->quotes_sum_total_amount > 0 && isset($latestQuote))
                                        <span class="flex items-center">
                                            <a href="{{ route('quotes.show', $latestQuote->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                ¥{{ number_format($project->quotes_sum_total_amount) }}
                                            </a>
                                            {{-- PDFダウンロードリンクアイコン --}}
                                            @if ($latestPdfQuoteId)
                                                <a href="{{ route('quotes.downloadPdfMpdf', $latestPdfQuoteId) }}" target="_blank" class="ml-2 text-red-600 hover:text-red-800" title="最新ログPDFをダウンロード">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                        <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27.146.23.308.535.49.875.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </span>
                                    @else
                                        ¥0
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border border-gray-200">
                                    @if ($project->invoices_sum_total_amount > 0 && isset($latestInvoice))
                                        <span class="flex items-center">
                                            <a href="{{ route('invoices.show', $latestInvoice->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                ¥{{ number_format($project->invoices_sum_total_amount) }}
                                            </a>
                                            {{-- 請求書PDFダウンロードリンクアイコン --}}
                                            @if (isset($latestInvoice->pdf_path) && $latestInvoice->pdf_path)
                                                {{-- TODO: 請求書PDFのパスを保存するロジックと、invoices.downloadPdf ルートの実装が必要です --}}
                                                <a href="{{-- route('invoices.downloadPdf', $latestInvoice->id) --}}" target="_blank" class="ml-2 text-red-600 hover:text-red-800" title="PDFをダウンロード">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                        <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c.054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27.146.23.308.535.49.875.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </span>
                                    @else
                                        ¥0
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border border-gray-200">
                                    <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded mr-2">
                                        認 ¥{{ number_format($project->total_approved_expenses_sum ?? 0, 0) }}
                                    </span>
                                    <span class="bg-red-200 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                                        未 ¥{{ number_format($project->total_pending_expenses_sum ?? 0, 0) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border border-gray-200">
                                    @php
                                        $approvedExpenses = $project->expenses()
                                                                    ->whereHas('status', fn($q) => $q->where('name', '承認済み'))
                                                                    ->sum('amount');

                                        $allExpenses = $project->expenses()->sum('amount');

                                        $invoicesTotal = $project->invoices_sum_total_amount ?? 0;

                                        $netProfitApprovedOnly = $invoicesTotal - $approvedExpenses;
                                        $netProfitAll = $invoicesTotal - $allExpenses;
                                    @endphp

                                    <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-1 rounded mr-2">
                                        認 ¥{{ number_format($netProfitApprovedOnly, 0) }}
                                    </span>
                                    <span class="bg-red-200 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                                        未 ¥{{ number_format($netProfitAll, 0) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- アクションボタン --}}
                <div class="mt-6 flex space-x-4 justify-end">
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('編集') }}
                    </a>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('一覧に戻る') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
