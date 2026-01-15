<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            {{-- 左側: イベント詳細 --}}
            <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
                {{ __('イベント詳細') }}
            </h2>

            {{-- 右側: イベント管理に戻る --}}
            <a href="{{ route('projects.index') }}" class="flex items-center text-xs text-gray-700 hover:text-blue-700 hover:border-b hover:border-blue-700 gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                </svg>
                {{ __('イベント管理に戻る') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 overflow-hidden sm:rounded-lg p-4" style="@media (max-width: 400px) {padding: 0.5rem;}">
                {{-- 主要情報をカード形式で表示 --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    {{-- 左カラム（既存の主要情報） --}}
                    <div class="bg-white border border-slate-200 px-4 py-3 rounded-lg md:col-span-2">
                        {{-- 既存の左カラム内容 --}}
                        <div class="grid grid-cols-12 gap-4 items-center mb-2 border-b">
                            <h3 class="font-bold text-2xl col-span-6">{{ $project->name }}</h3>
                            <div class="col-span-4">
                                <dd class="ml-0 text-sm">{{ $project->client->name ?? 'N/A' }}</dd>
                                <dd class="ml-0 text-base">{{ $project->venue ?? 'N/A' }}</dd>
                            </div>
                            <div class="col-span-2 text-right text-xs">
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $start = \Carbon\Carbon::parse($project->start_date);
                                    $end = \Carbon\Carbon::parse($project->end_date);
                                @endphp

                                @if ($today->lt($start))
                                    <span class="px-3 py-0.5 inline-flex leading-5 font-semibold rounded-full bg-red-200 text-red-700">
                                        開催前
                                    </span>
                                @elseif ($today->between($start, $end))
                                    <span class="px-3 py-0.5 inline-flex leading-5 font-semibold rounded-full bg-amber-200 text-amber-700">
                                        開催中
                                    </span>
                                @else
                                    <span class="px-3 py-0.5 inline-flex leading-5 font-semibold rounded-full bg-slate-200 text-slate-700">
                                        終了
                                    </span>
                                @endif
                            </div>
                        </div>

                        <dl class="space-y-4">
                            <div class="col-span-12 md:col-span-7 text-sm">
                                開催日程：{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y年m月d日') : 'N/A' }}
                                〜
                                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y年m月d日') : 'N/A' }}
                            </div>
                            <div>
                                <dt class="font-medium text-sm">●説明</dt>
                                <dd class="ml-0 whitespace-pre-wrap text-xs">{{ $project->description ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 右カラム（既存の担当者・タスク） --}}
                    <div class="bg-white border border-slate-200 p-4 rounded-lg md:col-span-1">
                        <div class="mb-2 border-b border-dashed pb-2">
                            <div class="flex items-center gap-2">
                                <dt class="font-medium whitespace-nowrap text-sm">登録者:</dt>
                                <dd class="ml-0 flex flex-wrap gap-1 text-xs">
                                    @forelse ($project->users as $user)
                                        <span class="bg-gray-200 text-gray-800 px-2 py-0.5 rounded">
                                            {{ $user->name }}
                                        </span>
                                    @empty
                                        登録者なし
                                    @endforelse
                                </dd>
                            </div>
                        </div>

                        <div>
                            <dt class="font-medium text-sm">タスク</dt>
                            <dd class="ml-0 text-xs">
                                <ul>
                                    @forelse ($project->tasks as $task)
                                        <li>
                                            <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                ・{{ $task->name }}
                                            @if ($task->users->count() > 0)
                                                <span class="text-gray-500 ml-2">
                                                    (担当: {{ $task->users->pluck('name')->join(', ') }})
                                                </span>
                                            @endif
                                            </a>
                                        </li>
                                    @empty
                                        タスクなし
                                    @endforelse
                                </ul>
                            </dd>
                        </div>
                    </div>

                    {{-- 右カラム（チェックリスト） --}}
                    <div class="bg-white border border-slate-200 p-4 pl-3 rounded-lg md:col-span-1">
                        <h4 class="font-bold text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 pr-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            チェックリスト
                        </h4>

                        <div class="pl-5 w-full">
                            <table class="min-w-full mb-2 text-xs" style="border-collapse: collapse;">
                                <tbody>
                                    @foreach ($project->checklists as $checklist)
                                        <tr class="items-center pb-1">
                                            <td>{{ $checklist->name }}</td>
                                            <td>
                                                <button 
                                                    class="toggle-status text-white px-2 py-1 rounded"
                                                    style="background-color: {{ $checklist->status === '未' ? '#dc2626' : ($checklist->status === '作' ? '#f59e0b' : '#22c55e') }};"
                                                    data-checklist-id="{{ $checklist->id }}">
                                                    {{ $checklist->status }}
                                                </button>
                                            </td>
                                            <td class="flex justify-end items-center space-x-1">
                                                @if($checklist->link)
                                                    {{-- 登録済み: リンクアイコンだけ --}}
                                                    <a href="{{ $checklist->link }}" target="_blank" class="text-blue-600 hover:underline">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                        </svg>
                                                    </a>
                                                @else
                                                    {{-- 未登録: ＋リンクアイコン --}}
                                                    <button class="toggle-link-form text-gray-600 p-0.5 flex items-center" data-checklist-id="{{ $checklist->id }}">
                                                        <span class="text-sm font-bold">＋</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                        </svg>
                                                    </button>
                                                    {{-- 隠しフォーム --}}
                                                    <div class="link-form mt-1 hidden" data-checklist-id="{{ $checklist->id }}">
                                                        <input type="text" class="border px-1 py-0.5 rounded w-16 text-xs" placeholder="URL入力欄" data-checklist-id="{{ $checklist->id }}">
                                                        <button class="save-link px-2 py-1 bg-blue-600 text-white rounded text-xs" data-checklist-id="{{ $checklist->id }}">登録</button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 情報テーブル --}}
                <div class="overflow-x-auto mb-8">
                    <table class="min-w-full border border-gray-200" style="border-collapse: collapse;">
                        <thead class="bg-gray-100 text-xs font-medium text-gray-700">
                            <tr>
                                <th scope="col" class="p-2 text-right uppercase tracking-wider border border-gray-200">
                                    見積額
                                </th>
                                <th scope="col" class="p-2 text-right uppercase tracking-wider border border-gray-200">
                                    納品額
                                </th>
                                <th scope="col" class="p-2 text-right uppercase tracking-wider border border-gray-200">
                                    請求額
                                </th>
                                <th scope="col" class="p-2 text-right uppercase tracking-wider border border-gray-200">
                                    経費合計
                                </th>
                                <th scope="col" class="p-2 text-right uppercase tracking-wider border border-gray-200">
                                    実利
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white text-xs text-gray-600">
                            <tr>
                                <!-- 見積額 -->
                                <td class="px-2 py-1 text-right whitespace-nowrap border border-gray-200">
                                    @php
                                        $latestQuote = $project->quotes()->latest('created_at')->first();
                                        $hasQuotePdf = $latestQuote && $latestQuote->pdf_path;
                                    @endphp
                                    @if ($latestQuote)
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a href="{{ route('quotes.show', $latestQuote->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                ¥ <span class="text-sm">{{ number_format($latestQuote->total_amount) }}</span>
                                            </a>

                                            <a href="{{ route('quotes.downloadPdfMpdf', $latestQuote->id) }}" target="_blank"
                                            class="{{ $hasQuotePdf ? 'text-red-600 hover:text-red-800' : 'text-gray-400 hover:text-gray-600' }} generate-pdf"
                                            title="{{ $hasQuotePdf ? 'PDFダウンロード' : '見積書PDF未出力（クリックで生成）' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c .054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27c.146.23.308.535.49.875c.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                </svg>
                                            </a>

                                            <!-- フルステータス表示（色付き） -->
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                {{ $latestQuote->status === '作成済み' ? 'bg-green-200 text-green-800' : '' }}
                                                {{ $latestQuote->status === '発行済み' ? 'bg-amber-300 text-amber-800' : '' }}
                                                {{ $latestQuote->status === '送信済み' ? 'bg-gray-200 text-gray-800' : '' }}">
                                                {{ $latestQuote->status }}
                                            </span>
                                        </div>
                                    @else
                                        <a href="{{ route('quotes.create', ['project_id' => $project->id]) }}" 
                                        class="text-green-600 hover:text-green-800" title="見積書を作成">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </td>

                                <!-- 納品額 -->
                                <td class="px-2 py-1 text-right whitespace-nowrap border border-gray-200">
                                    @php
                                        $latestDelivery = $project->deliveries()->latest('created_at')->first();
                                        $hasDeliveryPdf = $latestDelivery && $latestDelivery->pdf_path;
                                    @endphp
                                    @if ($latestDelivery)
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a href="{{ route('deliveries.show', $latestDelivery->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                ¥ <span class="text-sm">{{ number_format($project->deliveries_sum_total_amount) }}</span>
                                            </a>

                                            <a href="{{ route('deliveries.downloadPdfMpdf', $latestDelivery->id) }}" target="_blank"
                                            class="{{ $hasDeliveryPdf ? 'text-red-600 hover:text-red-800' : 'text-gray-400 hover:text-gray-600' }} generate-pdf"
                                            title="{{ $hasDeliveryPdf ? 'PDFダウンロード' : '納品書PDF未出力（クリックで生成）' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline-block" viewBox="0 0 16 16">
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                    <path d="M4.603 12.087a.84.84 0 0 1-.438-.799c.075-.4.293-.678.623-.872l.06-.038c.28-.171.564-.4.96-.92.33-.414.68-1.133 1.02-1.968c .054-.13.128-.295.266-.445a.733.733 0 0 1 .793-.161c.07.053.175.148.243.27c.146.23.308.535.49.875c.217.412.438.828.596 1.257q.427.972 1.053 2.504c.28.705.326 1.007.065 1.187-.074.055-.118.065-.25.059q-.4-.027-.9-.166a2.72 2.72 0 0 1-.443-.24c-.109-.083-.25-.19-.363-.284l-.01-.007a.486.486 0 0 0-.083-.061c-.058-.042-.132-.118-.195-.178a.7.7 0 0 1-.153-.234c-.017-.065-.035-.12-.063-.193-.075-.197-.202-.456-.375-.76c-.147-.243-.3-.482-.464-.724a.5.5 0 0 0-.6-.096l-.007.004-.007.004-.005.002-.002.001a.5.5 0 0 0-.1.22z"/>
                                                </svg>
                                            </a>

                                            <!-- フルステータス表示（色付き） -->
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                {{ $latestDelivery->status === '作成済み' ? 'bg-green-200 text-green-800' : '' }}
                                                {{ $latestDelivery->status === '発行済み' ? 'bg-amber-300 text-amber-800' : '' }}
                                                {{ $latestDelivery->status === '送信済み' ? 'bg-gray-200 text-gray-800' : '' }}">
                                                {{ $latestDelivery->status }}
                                            </span>
                                        </div>
                                    @else
                                        <a href="{{ route('deliveries.create', ['project_id' => $project->id]) }}" 
                                        class="text-green-600 hover:text-green-800" title="納品書を作成">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5V7.5H11.5a.5.5 0 0 1 0 1H8.5V11.5a.5.5 0 0 1-1 0V8.5H4.5a.5.5 0 0 1 0-1H7.5V4.5A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </td>

                                <!-- 請求額 -->
                                <td class="px-2 py-1 text-right whitespace-nowrap border border-gray-200">
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
                                <!-- 経費合計 -->
                                <td class="px-2 py-1 text-right whitespace-nowrap border border-gray-200">
                                    <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-0.5 rounded mr-2">
                                        認 ¥{{ number_format($project->total_approved_expenses_sum ?? 0) }}
                                    </span>
                                    <span class="bg-red-200 text-red-800 text-xs font-semibold px-2 py-0.5 rounded">
                                        未 ¥{{ number_format($project->total_pending_expenses_sum ?? 0) }}
                                    </span>
                                </td>
                                <!-- 実利 -->
                                <td class="px-2 py-1 text-right whitespace-nowrap border border-gray-200">
                                    @php
                                        $invoicesTotal = $project->invoices_sum_total_amount ?? 0;
                                        $approved = $project->total_approved_expenses_sum ?? 0;
                                        $all = $project->total_all_expenses_sum ?? 0;

                                        $profitApproved = $invoicesTotal - $approved;
                                        $profitAll = $invoicesTotal - $all;
                                    @endphp

                                    <span class="bg-green-200 text-green-800 text-xs font-semibold px-2 py-0.5 rounded mr-2">
                                        認 ¥{{ number_format($profitApproved) }}
                                    </span>
                                    <span class="bg-red-200 text-red-800 text-xs font-semibold px-2 py-0.5 rounded">
                                        未 ¥{{ number_format($profitAll) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- アクションボタン --}}
                <div class="mt-6 flex space-x-2 justify-end text-xs text-white">
                    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('編集') }}
                    </a>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('一覧に戻る') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- モーダル（初期は非表示） -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-80 max-w-sm p-8">
            <h3 class="text-base font-semibold mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                ステータス変更確認
            </h3>
            <p id="modalMessage" class="text-sm mb-6">メッセージが入ります</p>
            <div class="flex justify-end gap-3 text-xs text-white">
                <button id="modalCancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">キャンセル</button>
                <button id="modalConfirm" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">変更する</button>
            </div>
        </div>
    </div>

<script>
    const modal = document.getElementById('statusModal');
    const modalMessage = document.getElementById('modalMessage');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalCancel = document.getElementById('modalCancel');

    let targetButton = null;
    let checklistId = null;

    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            targetButton = this;
            checklistId = this.dataset.checklistId;
            const currentStatus = this.textContent.trim();

            if (currentStatus === '済') {
                alert('すでに完了しています');
                return;
            }

            // 確認メッセージ設定
            if (currentStatus === '未') {
                modalMessage.textContent = 'ステータスを、未作成 から 作成済み に変更しますか？(未→作)';
            } else if (currentStatus === '作') {
                modalMessage.textContent = 'ステータスを、作成済み から 完了 に変更しますか？(作→済)';
            }

            modal.classList.remove('hidden');
        });
    });

    // キャンセルボタン
    modalCancel.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // 確定ボタン
    modalConfirm.addEventListener('click', () => {
        modal.classList.add('hidden');

        fetch(`/projects/{{ $project->id }}/checklists/${checklistId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            targetButton.textContent = data.status;
            targetButton.style.backgroundColor = data.status === '未' ? '#dc2626' : (data.status === '作' ? '#f59e0b' : '#22c55e');
        })
        .catch(err => alert('更新に失敗しました'));
    });
</script>
<!-- リンク保存 -->
<script>
    document.querySelectorAll('.save-link').forEach(button => {
        button.addEventListener('click', function() {
            const checklistId = this.dataset.checklistId;
            const input = document.querySelector(`input[data-checklist-id='${checklistId}']`);
            const url = input.value.trim();
            if (!url) return alert('URLが未入力です。URLを入力してください。');

            fetch(`/projects/{{ $project->id }}/checklists/${checklistId}/update-link`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ link: url })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    // 入力欄をアイコン表示に切替
                    const td = input.closest('td');
                    td.innerHTML = `<a href="${data.link}" target="_blank" class="text-blue-600 hover:underline">🔗</a>`;
                } else {
                    alert('保存に失敗しました');
                }
            })
            .catch(err => alert('保存に失敗しました'));
        });
    });
    // 「＋アイコン」をクリックするとフォームを表示し、アイコン自体は非表示にする
    document.querySelectorAll('.toggle-link-form').forEach(button => {
        button.addEventListener('click', function() {
            const checklistId = this.dataset.checklistId;
            const formDiv = document.querySelector(`.link-form[data-checklist-id='${checklistId}']`);
            if (formDiv) formDiv.classList.remove('hidden'); // フォームを表示
            this.classList.add('hidden'); // クリックしたアイコン自体を非表示
        });
    });
</script>
<script>
    document.querySelectorAll('.generate-pdf').forEach(btn => {
        btn.addEventListener('click', function() {
            setTimeout(() => {
                btn.classList.remove('text-gray-400', 'hover:text-gray-600');
                btn.classList.add('text-red-600', 'hover:text-red-800');
                btn.title = 'PDFダウンロード';
            }, 500);
        });
    });
</script>
</x-app-layout>
