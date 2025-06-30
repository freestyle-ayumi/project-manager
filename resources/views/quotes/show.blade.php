<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('見積書詳細') }}
        </h2>
    </x-slot>

    {{-- 共通CSS定義: Tailwindを使わず、DomPDF対応の標準CSSでレイアウトを構築 --}}
    <style>
        /* 日本語フォントの埋め込み (PDF用にも共通で使えるように) */
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
            color: #333;
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* paddingとborderをwidth/heightに含める */
        }

        .py-12-outer-padding { /* py-12 に相当 */
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .max-w-7xl-container { /* max-w-7xl mx-auto sm:px-6 lg:px-8 に相当 */
            max-width: 1024px; /* max-w-7xl */
            margin-left: auto; /* mx-auto */
            margin-right: auto; /* mx-auto */
            padding-left: 1.5rem; /* sm:px-6 */
            padding-right: 1.5rem; /* sm:px-6 */
        }
        @media (min-width: 1024px) { /* lg:px-8 に相当 */
            .max-w-7xl-container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        .text-gray-700-color { /* text-gray-700 に相当 */
            color: #4b5563;
        }

        .bg-white-card { /* bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 に相当 */
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
            border-radius: 0.5rem; /* sm:rounded-lg */
            padding: 1.5rem; /* p-6 */
        }

        .header-divider { /* bg-lime-100 h-px py-2 mt-1 mb-3 に相当 */
            background-color: #f0fdf4; /* bg-lime-100 */
            height: 1px; /* h-px */
            padding-top: 0.5rem; /* py-2 */
            padding-bottom: 0.5rem; /* py-2 */
            margin-top: 0.25rem; /* mt-1 */
            margin-bottom: 0.75rem; /* mb-3 */
        }

        .flex-col-container { /* flex flex-col mb-0 に相当 */
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .quote-title { /* text-3xl font-bold text-gray-800 mx-auto に相当 */
            font-size: 1.875rem; /* text-3xl (30px) */
            font-weight: 700; /* font-bold */
            color: #1f2937; /* text-gray-800 */
            margin-left: auto; /* mx-auto */
            margin-right: auto; /* mx-auto */
            text-align: center; /* flex mx-auto の効果を再現 */
        }
        
        /* 2カラムレイアウト用コンテナ (Tailwind grid grid-cols-1 md:grid-cols-12 gap-x-6 items-center ps-2 に相当) */
        .two-column-section {
            overflow: hidden; /* floatをクリアするため */
            margin-left: -0.75rem; /* gap-x-6 (1.5rem) の半分 */
            margin-right: -0.75rem; /* gap-x-6 (1.5rem) の半分 */
            padding-left: 0.5rem; /* ps-2 */
            margin-bottom: 0.5rem; /* 基本の mb-2 */
            align-items: center; /* items-center はfloatでは直接再現できないため、flexboxの代わり */
        }
        .two-column-section.first-block { /* 最初のブロックの margin-bottom */
            margin-bottom: 1.5rem; /* mb-6 */
        }

        .column-left { /* md:col-span-8 text-xl mr-10 pt-2 pl-3 font-medium に相当 */
            float: left;
            width: calc(66.666667% - 1.5rem); /* md:col-span-8 と gap-x-6 を考慮 */
            padding-left: 0.75rem; /* gap-x-6 の半分 */
            padding-right: 0.75rem; /* gap-x-6 の半分 */
            box-sizing: border-box; /* paddingを含めて幅を計算 */

            font-size: 1.25rem; /* text-xl */
            margin-right: 2.5rem; /* mr-10 */
            padding-top: 0.5rem; /* pt-2 */
            padding-left: 0.75rem; /* pl-3 */
            font-weight: 500; /* font-medium */
            color: #1f2937; /* text-gray-900 */
        }

        .column-right { /* md:col-span-4 text-sm text-gray-700 text-left md:mt-0 text-right mr-1 に相当 */
            float: left;
            width: calc(33.333333% - 1.5rem); /* md:col-span-4 と gap-x-6 を考慮 */
            padding-left: 0.75rem; /* gap-x-6 の半分 */
            padding-right: 0.75rem; /* gap-x-6 の半分 */
            box-sizing: border-box; /* paddingを含めて幅を計算 */

            font-size: 0.875rem; /* text-sm */
            color: #4b5563; /* text-gray-700 */
            text-align: right; /* text-right */
            margin-right: 0.25rem; /* mr-1 */
            /* md:mt-0 mt-6 はfloatでは直接再現が難しいが、コンテンツの配置で調整 */
        }
        .column-right p {
            margin-bottom: 0.25rem; /* mb-1 */
        }
        .column-right .separator { /* mr-1 ml-1 に相当 */
            margin-right: 0.25rem;
            margin-left: 0.25rem;
        }

        /* 会社ロゴ */
        .company-logo { /* mb-2 ml-auto rounded-md に相当 */
            margin-bottom: 0.5rem;
            margin-left: auto;
            border-radius: 0.25rem;
            display: block; /* ml-auto が効くように */
            width: 150px; /* 固定サイズ */
            height: 50px; /* 固定サイズ */
        }
        /* 顧客名と自社情報の2カラムの2番目のブロック調整 */
        .two-column-section.second-block .column-left-content p {
            margin-bottom: 0.75rem; /* mb-3 */
        }

        /* 見積情報テーブル（件名、納品予定日など） */
        .quote-info-table-wrapper { /* overflow-x-auto に相当 */
            overflow-x: auto;
        }
        .quote-info-table { /* divide-y divide-gray-200 text-sm に相当 */
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem; /* text-sm */
            /* 各セルのボーダーは個別に設定 */
        }
        .quote-info-table th, .quote-info-table td { /* px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100 / text-left に相当 */
            padding: 0.375rem 0.75rem;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .quote-info-table th {
            text-align: center;
            background-color: #f0fdf4; /* bg-lime-100 */
            width: 40%;
        }
        .quote-info-table td {
            text-align: left;
            width: 60%;
        }
        .quote-info-table .border-4-white { /* border-4 border-white に相当 */
            border: 4px solid #fff;
        }


        /* 見積金額合計 */
        .total-amount-display-section { /* grid grid-cols-3 md:grid-cols-12 items-center text-gray-700 mb-2 ps-2 に相当 */
            display: table; /* float クリアのため table に変更 */
            width: 100%;
            border-collapse: collapse; /* ボーダーの表示を制御 */
            margin-bottom: 0.5rem; /* mb-2 */
            padding-left: 0.5rem; /* ps-2 */
            color: #4b5563; /* text-gray-700 */
        }
        .total-amount-box { /* md:col-span-2 bg-lime-100 font-bold p-1 border border-2 border-lime-600 text-center に相当 */
            display: table-cell; /* float から変更 */
            vertical-align: middle; /* 高さ揃えのため */
            width: 16.66%; /* md:col-span-2 (2/12) */
            background-color: #f0fdf4; /* bg-lime-100 */
            font-weight: 700; /* font-bold */
            padding: 0.25rem; /* p-1 */
            border: 2px solid #a3e635; /* border border-2 border-lime-600 */
            text-align: center;
            box-sizing: border-box;
        }
        .total-amount-value { /* md:col-span-5 text-2xl border border-2 border-lime-600 border-l-0 p-3 text-center に相当 */
            display: table-cell; /* float から変更 */
            vertical-align: middle; /* 高さ揃えのため */
            width: 41.66%; /* md:col-span-5 (5/12) */
            font-size: 1.5rem; /* text-2xl */
            border: 2px solid #a3e635; /* border border-2 border-lime-600 */
            border-left: none; /* border-l-0 */
            padding: 0.75rem; /* p-3 */
            text-align: center;
            color: #1f2937; /* text-gray-900 */
            box-sizing: border-box;
        }
        .total-amount-spacer { /* md:col-span-5 の空divに相当 */
            display: table-cell; /* float から変更 */
            width: 41.68%; /* md:col-span-5 (5/12) + 残りの幅を調整 */
        }


        /* 明細テーブル */
        .items-table-wrapper { /* overflow-x-auto mb-1 border に相当 */
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
        .items-table thead { /* bg-lime-100 に相当 */
            background-color: #f0fdf4;
        }
        .items-table th, .items-table td { /* px-3 py-3 text-left text-xs font-medium uppercase tracking-wider に相当 */
            padding: 0.75rem;
            text-align: left;
            font-size: 0.75rem; /* text-xs */
            font-weight: 500; /* font-medium */
            text-transform: uppercase;
            letter-spacing: 0.05em; /* tracking-wider */
            white-space: nowrap; /* whitespace-nowrap */
            color: #4b5563; /* text-gray-600 */
            border-bottom: 1px solid #e5e7eb; /* divide-y divide-gray-200 の代わり */
            border-right: 1px solid #e5e7eb; /* 縦線のために追加 */
        }
        .items-table th:last-child, .items-table td:last-child {
            border-right: none; /* 最後の列の右線を削除 */
        }
        .items-table tbody { /* bg-white divide-y divide-gray-200 に相当 */
            background-color: #fff;
        }
        .items-table tbody td {
            font-size: 0.875rem; /* text-sm */
            color: #1f2937; /* text-gray-900 */
            border-bottom: 1px solid #e5e7eb; /* divide-y divide-gray-200 の代わり */
        }
        .items-table .text-right { text-align: right; }

        /* 合計フッター */
        .total-summary-section-container { /* grid md:grid-cols-12 items-center に相当 */
            overflow: hidden; /* float クリア */
        }
        .total-summary-row { /* grid md:grid-cols-12 items-center に相当 */
            overflow: hidden; /* float クリア */
            margin-bottom: 0.5rem; /* mb-2 */
        }
        .total-summary-spacer-col { /* md:col-span-8 の空divに相当 */
            float: left;
            width: calc(66.666667% - 0.75rem); /* md:col-span-8 の幅とgap */
            margin-right: 0.75rem; /* gap */
        }
        .total-summary-label-col { /* md:col-span-2 bg-lime-100 p-3 text-center に相当 */
            float: left;
            width: calc(16.666667% - 0.75rem); /* md:col-span-2 の幅とgap */
            background-color: #f0fdf4; /* bg-lime-100 */
            padding: 0.75rem; /* p-3 */
            text-align: center;
            color: #4b5563; /* text-gray-700 */
            font-weight: 700; /* font-bold */
            border: 1px solid #e5e7eb; /* border */
            border-right: none; /* border-l-0 の代わり */
        }
        .total-summary-value-col { /* md:col-span-2 text-2xl p-2 text-right text-lg border border-l-0 に相当 */
            float: left;
            width: calc(16.666667% + 0.75rem); /* md:col-span-2 の幅とgap */
            font-size: 1.25rem; /* text-2xl text-lg */
            padding: 0.5rem; /* p-2 */
            text-align: right;
            color: #1f2937; /* text-gray-900 */
            border: 1px solid #e5e7eb; /* border */
            border-left: none; /* border-l-0 */
        }
        .total-summary-value-col.border-top-none { /* border-t-0 に相当 */
            border-top: none;
        }
        .total-summary-value-col.final-total-value { /* text-xl font-extrabold に相当 */
            font-size: 1.25rem;
            font-weight: 800;
        }

        /* 備考 */
        .notes-section { /* mb-2 rounded-md shadow-sm font-normal に相当 */
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            font-weight: normal;
        }
        .notes-header { /* bg-lime-100 border p-2 に相当 */
            background-color: #f0fdf4;
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            color: #4b5563; /* text-gray-600 */
        }
        .notes-content { /* text-gray-900 whitespace-pre-wrap border border-t-0 p-2 に相当 */
            color: #1f2937;
            white-space: pre-wrap;
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 0.5rem;
        }

        /* アクションボタン */
        .action-buttons { /* flex justify-end space-x-4 mt-4 に相当 */
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
            gap: 1rem; /* space-x-4 */
        }
        .action-buttons a { /* bg-indigo-500 hover:bg-indigo-600 text-white pt-1 pb-1.5 pr-2 pl-2 text-sm rounded-md に相当 */
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
        .action-buttons a.edit-button { background-color: #4f46e5; } /* bg-indigo-500 */
        .action-buttons a.edit-button:hover { background-color: #4338ca; } /* hover:bg-indigo-600 */
        .action-buttons a.back-button { background-color: #84cc16; } /* bg-lime-500 */
        .action-buttons a.back-button:hover { background-color: #65a30d; } /* hover:bg-lime-600 */
        .action-buttons a.pdf-button { background-color: #ef4444; } /* bg-red-500 */
        .action-buttons a.pdf-button:hover { background-color: #dc2626; } /* hover:bg-red-600 */

    </style>

    <div class="py-12-outer-padding">
        <div class="max-w-7xl-container text-gray-700-color">
            <div class="bg-white-card">
                <div class="header-divider"></div>
                {{-- トップセクション：タイトル --}}
                <div class="flex-col-container">
                    <h1 class="quote-title">御見積書</h1>
                </div>
                
                {{-- 見積番号、発行日、顧客名 (2カラム) --}}
                <div class="two-column-section first-block">
                    {{-- 左側 --}}
                    <div class="column-left">
                        {{ $quote->client->name ?? 'N/A' }}<span class="pl-3">御中</span>
                    </div>
                    {{-- 右側 --}}
                    <div class="column-right">
                        <p>見積番号<span class="separator">:</span>{{ $quote->quote_number }}</p>
                        <p>発行日<span class="separator">:</span>{{ \Carbon\Carbon::parse($quote->issue_date)->format('Y年m月d日') }}</p>
                    </div>
                </div>

                {{-- プロジェクト名と会社情報 (2カラム) --}}
                <div class="two-column-section second-block">
                    {{-- 左側 --}}
                    <div class="column-left second-block">
                        <p>下記のとおり、御見積もり申し上げます。</p>
                        <p>{{ $quote->project->name ?? 'N/A' }}</p>
                        <div class="overflow-x-auto quote-info-table-wrapper">
                            <table class="quote-info-table">
                                <tbody>
                                    <tr class="border-4-white">
                                        <th>件名</th>
                                        <td>{{ $quote->subject }}</td>
                                    </tr>
                                    <tr class="border-4-white">
                                        <th>納品予定日</th>
                                        <td>{{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y年m月d日') : '未設定' }}</td>
                                    </tr>
                                    <tr class="border-4-white">
                                        <th>有効期限</th>
                                        <td>{{ $quote->expiry_date }}</td>
                                    </tr>
                                    <tr class="border-4-white">
                                        <th>納品場所</th>
                                        <td>{{ $quote->delivery_location ?: '未設定' }}</td>
                                    </tr>
                                    <tr class="border-4-white">
                                        <th>お支払条件</th>
                                        <td>{{ $quote->payment_terms ?: '未設定' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    {{-- 右側 --}}
                    <div class="column-right">
                        {{-- 会社ロゴ (プレースホルダー) --}}
                        <img src="https://placehold.co/150x50/E0E7FF/4338CA?text=YOUR+LOGO" alt="Company Logo" class="company-logo">
                        <p>株式会社フリースタイルエンターテイメント</p>
                        <p>〒710-0038</p>
                        <p>岡山県倉敷市新田2554</p>
                        <p>TEL<span class="separator">:</span>086-435-5557</p>
                        <p>E-mail<span class="separator">:</span>{{ Auth::user()->email ?? 'N/A' }}</p>
                        <p>担当<span class="separator">:</span>{{ Auth::user()->name ?? 'N/A' }}</p>
                        <p>適格登録番号<span class="separator">:</span>T62600010027085</p>
                    </div>
                </div>

                {{-- 御見積金額合計 --}}
                <div class="total-amount-display-section">
                    <div class="total-amount-box">
                        御見積金額<br>
                        (消費税込)</div>
                    <div class="total-amount-value">
                        ¥{{ number_format($quote->total_amount) }}
                    </div>
                    <div class="total-amount-spacer"></div>
                </div>

                {{-- 明細テーブル --}}
                <div class="items-table-wrapper">
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
                        <tfoot class="bg-lime-100">
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-right text-sm font-bold">小計 (税抜)</td>
                                <td colspan="2" class="px-3 py-3 text-right text-lg font-bold text-gray-900">¥{{ number_format(round($totalSubtotal, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-right text-sm font-bold">消費税</td>
                                <td colspan="2" class="px-3 py-3 text-right text-lg font-bold text-gray-900">¥{{ number_format(round($totalTax, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-right text-sm font-bold">合計金額 (税込)</td>
                                <td colspan="2" class="px-3 py-3 text-right text-xl font-extrabold text-gray-900">¥{{ number_format($quote->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 備考 --}}
                <div class="notes-section">
                    <p class="notes-header">備考</p>
                    <p class="notes-content">{{ $quote->notes ?: '-' }}</p>
                </div>
                <div class="header-divider"></div> {{-- 備考の下の区切り線 --}}

                {{-- アクションボタン --}}
                <div class="action-buttons">
                    <a href="{{ route('quotes.edit', $quote) }}" class="edit-button">
                        編集
                    </a>
                    <a href="{{ route('quotes.index') }}" class="back-button">
                        一覧に戻る
                    </a>
                    {{-- PDF出力ボタンを更新 --}}
                    <a href="{{ route('quotes.generatePdf', $quote) }}" class="pdf-button">
                        PDF出力
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
