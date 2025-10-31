<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg pl-3 text-gray-800 leading-tight">
            {{ __('プロジェクト一覧') }}
        </h2>
    </x-slot>

    <style>
        #project-calendar .fc-toolbar-title {
            font-size: 1rem; line-height: 1.5rem;
        }
        #project-calendar .fc-toolbar button {
            padding: 0.25rem 0.5rem; font-size: 0.75rem; border-color: #ccc !important; background-color: #fff; color: #666;
        }
        #project-calendar .fc-prev-button { border-radius: 0.375rem 0 0 0.375rem; }
        #project-calendar .fc-next-button { border-radius: 0 0.375rem 0.375rem 0; }
        #project-calendar .fc-toolbar .fc-today-button{ color: #333; }
        
        #project-calendar .fc-toolbar button:hover {
            background-color: rgb(100 116 139); color: #fff; font-weight: normal;
        }
        #project-calendar .fc-col-header-cell-cushion { font-size: 0.75rem; color: #666; }
        #project-calendar .fc-toolbar {
            margin-bottom: 0.5rem ;
        }
        #project-calendar .fc-toolbar-chunk {
            margin-bottom: 0 ;
        }
        #project-calendar .fc-daygrid-day-number{
            font-size: 0.75rem; color: #666;
        }

        #project-calendar .fc-event-title {
            font-size: 0.7rem; /* text-sm相当 */
            line-height: 1rem;
            padding:0.3rem 0.1rem 0.2rem 0.4rem;
        }
        #project-calendar .fc-event {
            margin-bottom:0.1rem;
        }

        #project-calendar .fc-event-time {
            font-size: 0.65rem;
        }

    </style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div>
                <div class="responsive-padding">
                    <div class="flex flex-col lg:flex-row">
                        <!-- 左カラム: カレンダー -->

                        <div class="lg:w-1/2">
                            <div id="project-calendar" class="bg-white border rounded-md shadow-sm p-4" style="min-height: 600px;"></div>
                        </div>

                        <!-- 右カラム: リスト -->
                        <div class="lg:w-1/2 overflow-x-auto text-gray-600">
                            <div class="max-w-7xl mx-auto px-1">
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                        <style>
                                            @media (max-width: 400px) {
                                                .responsive-padding {
                                                    padding: 0.5rem;
                                                }
                                            }
                                        </style>
                                    <div class="p-4 responsive-padding">
                                        <div class="text-right">
                                            <a href="{{ route('projects.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('＋新規') }}
                                            </a>
                                        </div>

                                        {{-- 検索・フィルターフォーム --}}
                                        <form action="{{ route('projects.index') }}" method="GET" class="mb-2 py-2 rounded-md shadow-sm bg-white">
                                            <div class="grid grid-cols-12 gap-1">
                                                <!-- キーワード検索 -->
                                                <div class="relative col-span-8">
                                                    <!-- アイコン -->
                                                    <div class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                                        </svg>
                                                    </div>

                                                    <!-- 検索入力 -->
                                                    <input type="text" name="search" value="{{ request('search') }}" 
                                                        placeholder="プロジェクト名・顧客名・タスク名"
                                                        class="w-full pl-8 border box-border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                                </div>


                                                <!-- ステータスフィルター -->
                                                <select name="status" class="col-span-2 border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                    <option value="" {{ request('status')=='' ? 'selected' : '' }}>すべて</option>
                                                    <option value="upcoming" {{ request('status')=='upcoming' ? 'selected' : '' }}>開催前</option>
                                                    <option value="ongoing" {{ request('status')=='ongoing' ? 'selected' : '' }}>開催中</option>
                                                    <option value="finished" {{ request('status')=='finished' ? 'selected' : '' }}>終了</option>
                                                </select>

                                                <!-- 検索ボタン -->
                                                <button type="submit"
                                                        class="col-span-1 rounded-md bg-indigo-500 hover:bg-indigo-300 text-white text-xs text-center transition">
                                                    検索
                                                </button>

                                                <!-- クリアボタン -->
                                                <a href="{{ route('projects.index') }}"
                                                        class="col-span-1 rounded-md bg-gray-400 hover:bg-gray-500 text-white text-xs text-center transition text-center flex items-center justify-center transition">
                                                    クリア
                                                </a>
                                            </div>
                                        </form>



                                            <div class="overflow-x-auto text-gray-600">
                                            @if ($projects->isEmpty())
                                                <p class="mx-6">該当プロジェクトが存在しません。</p>
                                            @else
                                                <table class="min-w-[1000px] divide-y divide-gray-200 text-gray-600 text-xs">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="text-left font-medium uppercase tracking-wider text-center w-16">
                                                                開始
                                                            </th>
                                                            <th scope="col" class="text-left font-medium uppercase tracking-wider text-center w-16">
                                                                終了
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                プロジェクト名
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                顧客
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                担当者
                                                            </th>
                                                            <th scope="col" class="text-left font-medium uppercase tracking-wider text-center">
                                                                ステータス
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider text-center">
                                                                タスク
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                見積
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                納品
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                請求
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                経費合計
                                                            </th>
                                                            <th scope="col" class="p-2 text-left font-medium uppercase tracking-wider">
                                                                実利
                                                            </th>
                                                            <th scope="col" class="relative px-2 py-2">
                                                                操作
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach ($projects as $project)
                                                            <tr class="hover:bg-gray-50 text-gray-500">
                                                                <td class="px-1 py-0.5 whitespace-nowrap bg-red-100 text-center">
                                                                    <!-- 開始 -->
                                                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('y/m/d') : 'N/A' }}
                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap bg-lime-200 text-center">
                                                                    <!-- 終了 -->
                                                                    {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('y/m/d') : 'N/A' }}
                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap">
                                                                    <!-- プロジェクト名 -->
                                                                    <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-fuchsia-600 hover:underline">
                                                                        {{ $project->name }}
                                                                    </a>
                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap">
                                                                    <!-- 顧客 -->
                                                                    {{ $project->client->name ?? 'N/A' }}
                                                                </td>
                                                                <td class="px-1 py-0.5 whitespace-nowrap">
                                                                    <!-- 担当（複数対応） -->
                                                                    @if ($project->users && $project->users->count() > 0)
                                                                        {{ $project->users->pluck('name')->join('、') }}
                                                                    @else
                                                                        －
                                                                    @endif
                                                                </td>
                                                                <!-- ステータス -->
                                                                @php
                                                                $today = \Carbon\Carbon::today();
                                                                $start = \Carbon\Carbon::parse($project->start_date);
                                                                $end = \Carbon\Carbon::parse($project->end_date);
                                                                @endphp

                                                                <td class="px-0.5 whitespace-nowrap text-center">
                                                                    @if ($today->lt($start))
                                                                        <span class="px-2 py-0.5 inline-flex text-xs leading-4 rounded-full bg-red-200 text-red-700 justify-items-center">開催前</span>
                                                                    @elseif ($today->between($start, $end))
                                                                        <span class="px-2 py-0.5 inline-flex text-xs leading-4 rounded-full bg-amber-200 text-amber-700 justify-items-center">開催中</span>
                                                                    @else
                                                                        <span class="px-2 py-0.5 inline-flex text-xs leading-4 rounded-full bg-slate-200 text-slate-700">終了</span>
                                                                    @endif
                                                                </td>
                                                                    
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-center">
                                                                    {{-- タスク件数 --}}
                                                                    {!! $project->tasks->count() > 0 
                                                                        ? '<a href="'.route('projects.show', $project).'" class="text-blue-500 hover:text-blue-700">'.$project->tasks->count().' 件</a>'
                                                                        : '-' !!}
                                                                </td>

                                                                <td class="px-1 py-0.5 whitespace-nowrap text-sm text-right">
                                                                    {{-- 見積額 --}}
                                                                    @if (isset($latestQuotes[$project->id]))
                                                                        <span class="flex items-center">
                                                                            <a href="{{ route('quotes.show', $latestQuotes[$project->id]->id) }}" class="text-blue-500 hover:text-blue-700">
                                                                                ¥{{ number_format($project->quotes_sum_total_amount) }}
                                                                            </a>

                                                                            @if ($latestQuotes[$project->id]->pdf_path)
                                                                                <a href="{{ route('quotes.downloadPdfMpdf', $latestQuotes[$project->id]->id) }}"
                                                                                    target="_blank"
                                                                                    class="ml-2 text-red-600 hover:text-red-800"
                                                                                    title="PDFダウンロード">
                                                                                    <!-- PDFアイコン -->
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                                        class="inline-block mb-1" viewBox="0 0 16 16">
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
                                                                {{-- 納品 --}}
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">-</td>
                                                                {{-- 請求 --}}
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    @if ($project->invoices_sum_total_amount)
                                                                        <a href="{{ route('invoices.index', ['project_id' => $project->id]) }}" class="text-blue-500 hover:text-blue-700">
                                                                            ¥{{ number_format($project->invoices_sum_total_amount) }}
                                                                        </a>
                                                                    @else
                                                                        ¥0
                                                                    @endif
                                                                </td>
                                                                {{-- 経費合計 --}}
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    ¥{{ number_format($project->total_approved_expenses_sum ?? 0) }}
                                                                </td>
                                                                {{-- 実利 --}}
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right">
                                                                    @php
                                                                        $netProfit = ($project->invoices_sum_total_amount ?? 0) - ($project->total_approved_expenses_sum ?? 0);
                                                                    @endphp
                                                                    ¥{{ number_format($netProfit) }}
                                                                </td>
                                                                {{-- 操作ボタン --}}
                                                                <td class="px-1 py-0.5 whitespace-nowrap text-right font-medium">
                                                                    <div class="flex items-center justify-end gap-x-0.5">
                                                                        {{-- 詳細 --}}
                                                                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-400" title="詳細">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right mr-0.5" viewBox="0 0 17 15">
                                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                                            </svg>
                                                                        </a>

                                                                        {{-- 編集 --}}
                                                                        <a href="{{ route('projects.edit', $project) }}" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                                            <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                                                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                                                            </svg> -->
                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                            </svg>

                                                                        </a>

                                                                        {{-- 削除 --}}
                                                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこのプロジェクトを削除しますか？');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除">
                                                                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                                                </svg> -->
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FullCalendar CSS & JS（cdnjs版） --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>

@php
$calendarEvents = $allProjects->map(function($project) use ($colors) {
    $colorId = $project->color ?? $colors?->first()?->id;
    $colorHex = optional($colors?->firstWhere('id', $colorId))->hex_code ?? '#3B82F6';

    $endDate = $project->end_date
        ? \Carbon\Carbon::parse($project->end_date)->addDay()->format('Y-m-d')
        : null;

    return [
        'title' => $project->name,
        'start' => $project->start_date,
        'end' => $endDate,
        'url' => route('projects.show', $project->id),
        'color' => $colorHex,
    ];
});
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("✅ FullCalendar読み込みテスト開始");

    const el = document.getElementById('project-calendar');
    if (!el) {
        console.error("❌ カレンダー要素が見つかりません");
        return;
    }

    if (typeof FullCalendar === 'undefined') {
        console.error("❌ FullCalendarがロードされていません（cdnjs版）");
        return;
    }

    // Laravel Blade から JS 配列に変換済み
    const projects = @json($calendarEvents);

    const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,today,next',
            center: 'title',
            right: ''
        },
        events: projects,
        height: 700,
        datesSet: function(info) {
            const monthNames = ["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"];

            // 現在表示中の月を取得
            const currentDate = info.view.currentStart; 
            const month = monthNames[currentDate.getMonth()];
            const year = currentDate.getFullYear();

            document.querySelector('#project-calendar .fc-toolbar-title').textContent = `${year}年 ${month}`;
        }
    });

    calendar.render();
    console.log("✅ カレンダー描画完了（Blade版）");
});
</script>

</x-app-layout>