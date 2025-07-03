<x-app-layout>
    {{-- このファイルは主にウェブブラウザでの表示に使用されます。 --}}
    <style>
        /* 日本語フォントの埋め込み (DomPDFとの互換性を考慮した標準CSS) */
        @font-face {
            font-family: 'ipaexgothic';
            /* storage_path()はLaravelのヘルパー関数であり、DomPDFが実行されるPHP環境から正しくパスを解決します */
            src: url('{{ storage_path("fonts/ipaexg.ttf") }}') format('truetype');
        }

        /* 基本的なスタイルリセットとフォント設定 */
        body, h1, h2, h3, h4, h5, h6, table, th, td, p, span, div, strong {
            font-family: 'ipaexgothic', sans-serif !important;
            font-size: 10pt; /* 基本フォントサイズ */
            line-height: 1.5;
            color: #4b5563;
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* paddingとborderをwidth/heightに含める */
        }

        .max-w-cont { /* max-w-7xl mx-auto sm:px-6 lg:px-8 に相当 */
            max-width: 1024px; /* max-w-7xl */
            margin-left: auto; /* mx-auto */
            margin-right: auto; /* mx-auto */
            padding-left: 1.5rem; /* sm:px-6 */
            padding-right: 1.5rem; /* sm:px-6 */
        }
        @media (min-width: 1024px) { /* lg:px-8 に相当 */
            .max-w-cont {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        .bg-card { /* bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 に相当 */
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
            border-radius: 0.5rem; /* sm:rounded-lg */
            padding: 1.5rem; /* p-6 */
        }

        /* セクション間の区切り線 */
        .divider { /* bg-lime-100 h-px py-2 mt-1 mb-3 に相当 */
            background-color: #f0fdf4; /* bg-lime-100 */
            height: 1px; /* h-px */
            padding-top: 0.5rem; /* py-2 */
            padding-bottom: 0.5rem; /* py-2 */
            margin-top: 0.25rem; /* mt-1 */
            margin-bottom: 0.75rem; /* mb-3 */
        }

        /* 御見積書タイトル */
        .flex-col-cont { /* flex flex-col mb-0 に相当 */
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .main-title { /* text-3xl font-bold text-gray-800 mx-auto に相当 */
            font-size: 1.875rem; /* text-3xl (30px) */
            font-weight: 700; /* font-bold */
            margin-left: auto; /* mx-auto */
            margin-right: auto; /* mx-auto */
            text-align: center; /* flex mx-auto の効果を再現 */
        }

        /* 会社ロゴ */
        .comp-logo { /* mb-2 ml-auto rounded-md に相当 */
            margin-bottom: 0.5rem;
            margin-left: auto; /* For right alignment */
            border-radius: 0.25rem;
            display: block; /* ml-auto が効くように */
            width: 150px; /* 固定サイズ */
            height: 50px; /* 固定サイズ */
        }


        .info-table { /* divide-y divide-gray-200 text-sm に相当 */
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem; /* text-sm */
            /* 各セルのボーダーは個別に設定 */
        }
        .info-table th {
            text-align: center;
            background-color: #f0fdf4; /* bg-lime-100 */
            width: 20%;
        }
        .info-table td {
            text-align: left;
            width: 80%;
        }
        .info-table .bdr-4-w { /* border-4 border-white に相当 */
            border: 4px solid #fff;
        }
    /* 御見積金額合計セクション */
        .quote-total-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5rem;
            font-family: 'ipaexgothic', sans-serif;
        }

        .quote-total-label {
            width: 16%;
            background-color: #f0fdf4; /* 薄緑背景 */
            font-weight: 700;
            padding: 0.25rem; /* p-1 */
            border: 2px solid #a3e635; /* ライム色の枠線 */
            text-align: center;
            box-sizing: border-box;
        }

        .quote-total-value {
            width: 43%;
            font-size: 1.5rem; /* text-2xl */
            border: 2px solid #a3e635;
            border-left: none;
            padding: 0.75rem; /* p-3 */
            text-align: center;
            color: #1f2937; /* text-gray-900 */
            box-sizing: border-box;
        }

        .quote-total-spacer {
            width: 41%;
        }
        /* 明細テーブル */
        .items-table-wrap { /* overflow-x-auto mb-1 border に相当 */
            overflow-x: auto;
            margin-bottom: 0.25rem;
            border: 1px solid #e5e7eb; /* border */
            border-radius: 0.375rem; /* rounded-md */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
        }
        .items-table { /* min-w-full divide-y divide-gray-200 に相当 */
            min-width: 100%;
            border-collapse: collapse;
        }

        .items-table th, .items-table td { /* px-3 py-3 text-left text-xs font-medium uppercase tracking-wider に相当 */
            padding: 0.75rem;
            text-align: left;
            font-size: 0.75rem; /* text-xs */
            font-weight: 500; /* font-medium */
            text-transform: uppercase;
            letter-spacing: 0.05em; /* tracking-wider */
            white-space: nowrap; /* whitespace-nowrap */
            border-bottom: 1px solid #e5e7eb; /* divide-y divide-gray-200 の代わり */
            border-right: 1px solid #e5e7eb; /* 縦線のために追加 */
        }
        .items-table th:last-child, .items-table td:last-child {
            border-right: none; /* 最後の列の右線を削除 */
        }

        .items-table .text-right { text-align: right; }

        /* 合計フッター */
        .ftr-cell {
            padding: 0.75rem; /* px-3 py-3 */
            /* text-align will be set by specific classes */
        }
        .ftr-lbl-txt {
            font-size: 0.875rem; /* text-sm */
            font-weight: 700; /* font-bold */
            text-align: right !important;
        }
        .ftr-val-txt {
            font-size: 1.125rem; /* text-lg */
            font-weight: 700; /* font-bold */
            text-align: right !important;
        }
        .ftr-grnd-total-txt {
            font-size: 1.25rem; /* text-xl */
            font-weight: 800; /* font-extrabold */
            text-align: right !important;
        }
        .lbl-bg { /* total-label-bg */
            background-color: #f0fdf4; /* 薄い緑色 */
        }
        .val-bg-w { /* total-value-bg-white を val-bg-w に変更 */
            background-color: #ffffff; /* 金額部分の背景を白に */
        }

        /* 備考 */
        .notes-sec { /* mb-2 rounded-md shadow-sm font-normal に相当 */
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-weight: normal;
        }
        .notes-hdr { /* bg-lime-100 border p-2 に相当 */
            background-color: #f0fdf4;
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            color: #4b5563; /* text-gray-600 */
        }
        .notes-cont { /* text-gray-900 whitespace-pre-wrap border border-t-0 p-2 に相当 */
            white-space: pre-wrap;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 0.5rem;
        }

        /* アクションボタン */
        .action-btns { /* flex justify-end space-x-4 mt-4 に相当 */
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            gap: 1rem; /* space-x-4 */
        }
        .action-btns a { /* bg-indigo-500 hover:bg-indigo-600 text-white pt-1 pb-1.5 pr-2 pl-2 text-sm rounded-md に相当 */
            padding-top: 0.25rem;
            padding-bottom: 0.375rem; /* pt-1 pb-1.5 */
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
        <div class="max-w-cont text-gray-700">
            <div class="bg-card">
                                {{-- アクションボタン --}}
                <div class="flex justify-end my-3 space-x-4">
                    <a href="{{ route('quotes.edit', $quote) }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        編集
                    </a>
                    <a href="{{ route('quotes.index') }}"
                    class="bg-lime-500 hover:bg-lime-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        一覧に戻る
                    </a>
                    <a href="{{ url('/quotes/' . $quote->id . '/pdf-mpdf') }}"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        PDF出力
                    </a>
                </div>
                <div class="divider"></div>
                {{-- トップセクション：タイトル --}}
                <div class="flex-col-cont">
                    <h1 class="main-title">御見積書</h1>
                </div>
                
                {{-- 見積番号、発行日、顧客名 (2カラム) --}}
                {{-- 顧客名・見積番号・発行日をテーブル化 --}}
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 66.66%; font-size: 1.25rem; font-weight: 500; color: #1f2937; padding: 0.5rem 0.75rem;">
                            {{ $quote->client->name ?? 'N/A' }} 御中
                        </td>
                        <td style="width: 33.33%; font-size: 0.875rem; color: #4b5563; text-align: right; padding: 0.5rem 0.75rem;">
                            <p style="margin-bottom: 0.25rem;">見積番号：{{ $quote->quote_number }}</p>
                            <p style="margin-bottom: 0.25rem;">発行日：{{ \Carbon\Carbon::parse($quote->issue_date)->format('Y年m月d日') }}</p>
                        </td>
                    </tr>
                </table>

                {{-- プロジェクト名と会社情報 (2カラム) --}}
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        {{-- 左側 --}}
                        <td style="width: 60%; vertical-align: top; padding: 0.5rem 0.75rem;">
                            <p style="margin-bottom: 0.75rem;">下記のとおり、御見積もり申し上げます。</p>
                            <p style="margin-bottom: 0.75rem;">{{ $quote->project->name ?? 'N/A' }}</p>

                            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                                <tbody>
                                    <tr style="border: 4px solid #fff;">
                                        <th style="text-align: center; background-color: #f0fdf4; width: 25%; padding: 0.375rem 0.75rem;">件名</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $quote->subject }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th style="text-align: center; background-color: #f0fdf4; padding: 0.375rem 0.75rem;">納品予定日</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y年m月d日') : '未設定' }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th style="text-align: center; background-color: #f0fdf4; padding: 0.375rem 0.75rem;">有効期限</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $quote->expiry_date }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th style="text-align: center; background-color: #f0fdf4; padding: 0.375rem 0.75rem;">納品場所</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $quote->delivery_location ?: '未設定' }}</td>
                                    </tr>
                                    <tr style="border: 4px solid #fff;">
                                        <th style="text-align: center; background-color: #f0fdf4; padding: 0.375rem 0.75rem;">お支払条件</th>
                                        <td style="text-align: left; width: 75%; padding: 0.375rem 1rem;">{{ $quote->payment_terms ?: '未設定' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        {{-- 右側 --}}
                        <td style="width: 40%; vertical-align: top; padding: 0.5rem 0.75rem;">
                            <img src="https://placehold.co/150x50/E0E7FF/4338CA?text=YOUR+LOGO"
                                alt="Company Logo"
                                style="margin-bottom: 0.5rem; margin-left: auto; display: block; border-radius: 0.25rem; width: 150px; height: 50px;">
                            <p>株式会社フリースタイルエンターテイメント</p>
                            <p>〒710-0038</p>
                            <p>岡山県倉敷市新田2554</p>
                            <p>TEL：086-435-5557</p>
                            <p>E-mail：{{ Auth::user()->email ?? 'N/A' }}</p>
                            <p>担当：{{ Auth::user()->name ?? 'N/A' }}</p>
                            <p>適格登録番号：T62600010027085</p>
                        </td>
                    </tr>
                </table>

                {{-- 御見積金額合計 --}}
                <table class="quote-total-table">
                    <tr>
                        <td class="quote-total-label">
                            御見積金額<br>(消費税込)
                        </td>
                        <td class="quote-total-value">
                            ¥{{ number_format($quote->total_amount) }}
                        </td>
                        <td class="quote-total-spacer"></td>
                    </tr>
                </table>
                {{-- 明細テーブル --}}
                <div class="items-table-wrap">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">商品名</th>
                                <th style="width: 15%; text-align: right;">単価</th>
                                <th style="width: 5%; text-align: right;">数量</th>
                                <th style="width: 5%; text-align: right;">単位</th>
                                <th style="width: 15%; text-align: right;">小計 (税抜)</th>
                                <th style="width: 15%; text-align: right;">合計 (税込)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalSubtotal = 0;
                                $totalTax = 0;
                            @endphp
                            @foreach ($quote->items as $item)
                                @php
                                    $itemSubtotal = $item->price * $item->quantity;
                                    $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                                    $itemTotal = $itemSubtotal + $itemTax;
                                    
                                    $totalSubtotal += $itemSubtotal;
                                    $totalTax += $itemTax;
                                @endphp
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="text-right">¥{{ number_format($item->price) }}</td>
                                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td class="text-right">¥{{ number_format(round($itemSubtotal, 0)) }}</td>
                                    <td class="text-right">¥{{ number_format(round($itemTotal, 0)) }}</td>
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
                                <td class="ftr-cell ftr-grnd-total-txt val-bg-w">¥{{ number_format($quote->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 備考 --}}
                <div class="notes-sec">
                    <p class="notes-hdr">備考</p>
                    <p class="notes-cont">{{ $quote->notes ?: '-' }}</p>
                </div>
                <div class="divider"></div> {{-- 備考の下の区切り線 --}}

                {{-- アクションボタン --}}
                <div class="flex justify-end mt-4 space-x-4">
                    <a href="{{ route('quotes.edit', $quote) }}"
                    class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        編集
                    </a>
                    <a href="{{ route('quotes.index') }}"
                    class="bg-lime-500 hover:bg-lime-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        一覧に戻る
                    </a>
                    <a href="{{ url('/quotes/' . $quote->id . '/pdf-mpdf') }}"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold text-sm px-2 pt-1 pb-1.5 rounded-md">
                        PDF出力
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
