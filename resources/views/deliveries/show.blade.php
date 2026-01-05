<x-app-layout>
    <div class="quote-page">
    {{-- このファイルは主にウェブブラウザでの表示に使用されます。 --}}
    <style>
        /* 日本語フォントの埋め込み (DomPDFとの互換性を考慮した標準CSS) */
        @font-face {
            font-family: 'ipaexgothic';
            /* storage_path()はLaravelのヘルパー関数であり、DomPDFが実行されるPHP環境から正しくパスを解決します */
            src: url('{{ storage_path("fonts/ipaexg.ttf") }}') format('truetype');
        }

        /* 基本的なスタイルリセットとフォント設定 */
        .quote-page h1,
        .quote-page h2,
        .quote-page h3,
        .quote-page h4,
        .quote-page h5,
        .quote-page h6,
        .quote-page table,
        .quote-page th,
        .quote-page td,
        .quote-page p,
        .quote-page span,
        .quote-page div,
        .quote-page strong,
        .quote-page a, {
            font-family: 'ipaexgothic', sans-serif !important;
            font-size: 10pt;
            line-height: 1.5;
            color: #4b5563;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .p-xy{
            padding: 0.5rem 0.75rem;
        }
        .font-s{
            font-size: 12px;
        }
        .text-sm{
            font-size: 0.875rem;  line-height:
                      1.25rem; /* 20px */
        }
        .max-w-cont {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 2rem;
            padding-right: 2rem;
            padding-top: 0.5rem;
        }
        @media (min-width: 1024px) {
            .max-w-cont {
                padding-left: 3rem;
                padding-right: 3rem;
            }
        }

        .bg-card {
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem 1rem 1rem;
        }

        /* セクション間の区切り線 */
        .divider {
            background: #f0fdf4;
            height: 1px;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            margin-top: 0.25rem;
            margin-bottom: 0.75rem;
        }

        /* 御納品書タイトル */
        .flex-col-cont { 
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .main-title { 
            font-size: 1.875rem;
            font-weight: 700;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        /* 会社ロゴ */
        .comp-logo { 
            margin-bottom: 0.5rem;
            margin-left: auto;
            border-radius: 0.25rem;
            display: block;
            width: 150px;
            height: 50px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .info-table th {
            text-align: center;
            background: #f0fdf4;
            width: 20%;
        }
        .info-table td {
            text-align: left;
            width: 80%;
        }
        .info-table .bdr-4-w {
            border: 4px solid #fff;
        }
    /* 御納品金額合計セクション */
        .quote-total-table {
            width: 100%;
            margin: 0.5rem 0 .5rem 0;
        }

        .quote-total-label {
            width: 16%;
            background: #f0fdf4;
            font-weight: 700;
            padding: 0.25rem;
            border: 2px solid #a3e635;
            text-align: center;
            box-sizing: border-box;
        }

        .quote-total-value {
            width: 43%;
            font-size: 30px;
            border: 2px solid #a3e635;
            border-left: none;
            padding: 0.2rem;
            text-align: center;
            box-sizing: border-box;
        }

        .quote-total-spacer {
            width: 41%;
        }
        /* 明細テーブル */
        .items-table-wrap {
            overflow-x: auto;
            margin-bottom: 1rem;
        }
        .items-table {
            min-width: 100%;
            border-collapse: collapse;
        }

        .items-table th{
            background: #f0fdf4;
            text-align: right;
        }
        .items-table th, 
        .items-table td {
            padding: 0.75rem;
            text-align: right;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
            border: 1px solid #e5e7eb;
        }

        .items-table .text-right { text-align: right; }

        /* 合計フッター */
        .ftr-cell {
            padding: 5px;
            font-weight: bold;
        }
        .ftr-lbl-txt {
            font-size: 12px;
            text-align: right !important;
        }
        .ftr-val-txt {
            font-size: 20px;
            text-align: right !important;
        }
        .ftr-grnd-total-txt {
            font-size: 25px;
            text-align: right !important;
        }
        .lbl-bg {
            background: #f0fdf4;
        }
        .val-bg-w {
            background: #ffffff;
        }

        /* 備考 */
        .notes-sec {
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            font-weight: normal;
        }
        .notes-hdr {
            background: #f0fdf4;
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
        }
        .notes-cont { 
            white-space: pre-wrap;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 0.5rem;
        }

        /* アクションボタン */
        .action-btns {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            gap: 1rem;
        }
        .action-btns a { 
            padding-top: 0.25rem;
            padding-bottom: 0.375rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            display: inline-block;
        }

    </style>

    <div>
        <div class="max-w-cont text-gray-700 pb-20">
            {{-- アクションボタン --}}
            <div class="flex justify-end mb-3 space-x-4">
                <a href="#log-section"
                class="bg-slate-400 hover:bg-slate-300 text-white font-bold text-xs px-4 py-2 rounded-md">
                    log</a>
                <a href="{{ route('deliveries.edit', $delivery) }}"
                class="bg-indigo-600 hover:bg-indigo-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                    編集
                </a>
                <a href="{{ route('deliveries.index') }}"
                class="bg-green-600 hover:bg-green-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                    一覧に戻る
                </a>
                <a href="{{ route('deliveries.downloadPdfMpdf', $delivery) }}" class="bg-red-600 hover:bg-red-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                    PDF出力
                </a>
            </div>
            <div class="bg-card">
                <div class="divider"></div>
                {{-- トップセクション：タイトル --}}
                <div class="flex-col-cont">
                    <h1 class="main-title">御納品書</h1>
                </div>
                
                {{-- 納品番号、発行日、顧客名 (2カラム) --}}
                {{-- 顧客名・納品番号・発行日をテーブル化 --}}
                <table style="width: 100%; border-collapse: collapse; padding-left: 0.75rem;">
                    <tr>
                        <td style="width: 60%; font-size: 1.2rem; font-weight: 500;">
                            {{ $delivery->client->name ?? 'N/A' }} 御中
                        </td>
                        <td style="width: 40%; font-size: 0.875rem; text-align: right;" class="p-xy">
                            <p style="margin-bottom: 0.25rem;">納品番号：{{ $delivery->delivery_number }}</p>
                            <p>発行日：{{ \Carbon\Carbon::parse($delivery->issue_date)->format('Y年m月d日') }}</p>
                        </td>
                    </tr>
                </table>

                {{-- プロジェクト名と会社情報 (2カラム) --}}
                <table style="width: 100%; border-collapse: collapse; font-size:0.85em;">
                    <tr>
                        {{-- 左側 --}}
                        <td class="p-xy" style="width: 60%; vertical-align: top;">
                        {{-- 御納品金額合計 --}}
                            <p>下記のとおり、御納品申し上げます。</p>
                            <table class="quote-total-table">
                                <tr>
                                    <td class="quote-total-label">
                                        御納品金額<br>(消費税込)
                                    </td>
                                    <td class="quote-total-value">
                                        ¥{{ number_format($delivery->total_amount) }}
                                    </td>                            </tr>
                            </table>
                            <p>{{ $delivery->project->name ?? 'N/A' }}</p>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tbody>
                                    <tr style="border: 4px solid #fff;">
                                        <th class="p-xy" style="text-align: center; background: #f0fdf4; width: 25%; padding: 0.375rem">件名</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $delivery->subject }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th class="p-xy" style="text-align: center; background: #f0fdf4; padding: 0.375rem">納品予定日</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $delivery->delivery_date ? \Carbon\Carbon::parse($delivery->delivery_date)->format('Y年m月d日') : '未設定' }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th class="p-xy" style="text-align: center; background: #f0fdf4; padding: 0.375rem">有効期限</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $delivery->expiry_date }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th class="p-xy" style="text-align: center; background: #f0fdf4; padding: 0.375rem">納入場所</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $delivery->delivery_location ?: '未設定' }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th class="" style="text-align: center; background: #f0fdf4; padding: 0.375rem">お支払条件</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $delivery->payment_terms ?: '未設定' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        {{-- 右側 --}}
                        <td style="width: 40%; vertical-align: top; font-size: 0.85em;"  class="company-txt p-xy">
                        <img src="{{ asset('img/fse-logo.png') }}" alt="株式会社フリースタイルエンターテイメント" class="comp-logo">
                            <p>株式会社フリースタイルエンターテイメント</p>
                            <p>〒710-0038</p>
                            <p>岡山県倉敷市新田2554</p>
                            <p>TEL：086-435-5557</p>
                            <p>　</p>
                            <p>E-mail：{{ Auth::user()->email ?? 'N/A' }}</p>
                            <p>担当：{{ Auth::user()->name ?? 'N/A' }}</p>
                            <p>適格登録番号：T62600010027085</p>
                        </td>
                    </tr>
                </table>

                {{-- 明細テーブル --}}
                <div class="items-table-wrap">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 45%; text-align: left;" class="font-s">品目</th>
                                <th style="width: 15%;" class="font-s">単価</th>
                                <th style="width: 5%;" class="font-s">数量</th>
                                <th style="width: 5%;" class="font-s">単位</th>
                                <th style="width: 15%;" class="font-s">小計 (税抜)</th>
                                <th style="width: 15%;" class="font-s">合計 (税込)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalSubtotal = 0;
                                $totalTax = 0;
                            @endphp
                            @foreach ($delivery->items as $item)
                                @php
                                    $itemSubtotal = $item->price * $item->quantity;
                                    $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                                    $itemTotal = $itemSubtotal + $itemTax;
                                    
                                    $totalSubtotal += $itemSubtotal;
                                    $totalTax += $itemTax;
                                @endphp
                                <tr>
                                    <td class="font-s" style="text-align: left;">{{ $item->item_name }}</td>
                                    <td class="text-right font-s">¥{{ number_format($item->price) }}</td>
                                    <td class="text-right font-s">{{ number_format($item->quantity) }}</td>
                                    <td class="font-s">{{ $item->unit }}</td>
                                    <td class="text-right font-s">¥{{ number_format(round($itemSubtotal, 0)) }}</td>
                                    <td class="text-right font-s">¥{{ number_format(round($itemTotal, 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="ftr-cell"></td> {{-- 空のセルで右寄せを調整 --}}
                                <td class="ftr-cell ftr-lbl-txt lbl-bg">小計 (税抜)</td>
                                <td class="ftr-cell ftr-val-txt val-bg-w">¥{{ number_format(round($totalSubtotal, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="ftr-cell"></td> {{-- 空のセルで右寄せを調整 --}}
                                <td class="ftr-cell ftr-lbl-txt lbl-bg">消費税</td>
                                <td class="ftr-cell ftr-val-txt val-bg-w">¥{{ number_format(round($totalTax, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="ftr-cell"></td> {{-- 空のセルで右寄せを調整 --}}
                                <td class="ftr-cell ftr-lbl-txt lbl-bg">合計金額 (税込)</td>
                                <td class="ftr-cell ftr-grnd-total-txt val-bg-w">¥{{ number_format($delivery->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 備考 --}}
                <div class="notes-sec">
                    <p class="notes-hdr">備考</p>
                    <p class="notes-cont">{{ $delivery->notes ?: '-' }}</p>
                </div>
                <div class="divider"></div> {{-- 備考の下の区切り線 --}}

                {{-- アクションボタン --}}
                <div class="flex justify-end mt-4 space-x-4">
                    <a href="{{ route('deliveries.edit', $delivery) }}"
                    class="bg-indigo-600 hover:bg-indigo-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                        編集
                    </a>
                    <a href="{{ route('deliveries.index') }}"
                    class="bg-green-600 hover:bg-green-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                        一覧に戻る
                    </a>
                    <a href="{{ url('/quotes/' . $delivery->id . '/pdf-mpdf') }}"
                    class="bg-red-600 hover:bg-red-400 text-white font-bold text-xs px-4 py-2 rounded-md">
                        PDF出力
                    </a>
                </div>
            </div>
            {{-- ログデータ --}}
            <hr class="border-t border-gray-300 my-6">
            <div class="overflow-x-auto bg-white rounded-md shadow-sm p-6 pt-4 max-w-4xl mx-auto">
                <h3 id="quote-log-section" class="text-lg font-semibold text-gray-800 pl-1 pb-1 text-sm">
                    log
                </h3>
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-600 uppercase">
                        <tr>
                            <th class="px-3 py-1" style="font-size:0.75em;">操作内容</th>
                            <th class="px-3 py-1" style="font-size:0.75em;">ユーザー</th>
                            <th class="px-3 py-1" style="font-size:0.75em;">日時</th>
                        </tr>
                    </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($delivery->logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-2" style="font-size:0.75em;">{!! $log->action !!}</td>
                                    <td class="px-6 py-2" style="font-size:0.75em;">{{ $log->user->name ?? '不明' }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap" style="font-size:0.75em;">{{ $log->created_at->format('Y年m月d日 H:i') }}</td>
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
