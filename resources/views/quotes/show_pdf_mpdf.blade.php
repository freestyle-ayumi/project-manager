<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    
<div class="bg-card">
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
                                <th style="width: 15%;" class="text-right">単価</th>
                                <th style="width: 5%;" class="text-right">数量</th>
                                <th style="width: 5%;" class="text-right">単位</th>
                                <th style="width: 15%;" class="text-right">小計 (税抜)</th>
                                <th style="width: 15%;" class="text-right">合計 (税込)</th>
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
                                <td class="ftr-cell ftr-val-txt val-bg-w text-right">¥{{ number_format(round($totalSubtotal, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="ftr-cell"></td> {{-- 空のセルで右寄せを調整 --}}
                                <td class="ftr-cell ftr-lbl-txt lbl-bg">消費税</td>
                                <td class="ftr-cell ftr-val-txt val-bg-w text-right">¥{{ number_format(round($totalTax, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="ftr-cell"></td> {{-- 空のセルで右寄せを調整 --}}
                                <td class="ftr-cell ftr-lbl-txt lbl-bg">合計金額 (税込)</td>
                                <td class="ftr-cell ftr-grnd-total-txt val-bg-w text-right">¥{{ number_format($quote->total_amount) }}</td>
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
            </div>
        </body>
</html>
