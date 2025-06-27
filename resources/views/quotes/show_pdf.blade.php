<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>見積書</title>
    <style>
        body, h1, h2, h3, h4, h5, h6, table, th, td, p, span, div, strong {
            font-family: 'ipaexgothic', sans-serif !important;
        }

        body {
            font-size: 12px;
            color: #333;
            margin: 40px;
        }

        .title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 25px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px 6px;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .no-border {
            border: none !important;
        }

        .company-info {
            text-align: right;
            line-height: 1.6;
        }

        .logo {
            float: right;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="title">御見積書</div>

    <div class="section">
        <div class="text-right">
            {{ $quote->client->name ?? 'N/A' }} 御中
        </div>
        <div class="company-info">
            <img class="logo" src="https://placehold.co/150x50/E0E7FF/4338CA?text=YOUR+LOGO" alt="Company Logo">
            <p>見積番号：{{ $quote->quote_number }}</p>
            <p>発行日：{{ \Carbon\Carbon::parse($quote->issue_date)->format('Y年m月d日') }}</p>
            <p>株式会社フリースタイルエンターテイメント</p>
            <p>〒710-0038 岡山県倉敷市新田2554</p>
            <p>TEL：086-435-5557</p>
            <p>E-mail：{{ Auth::user()->email ?? 'N/A' }}</p>
            <p>担当：{{ Auth::user()->name ?? 'N/A' }}</p>
            <p>適格登録番号：T62600010027085</p>
        </div>
    </div>

    <div class="section">
        <p><strong>御見積金額（消費税込）：</strong> ￥{{ number_format($quote->total_amount) }}</p>
        <p><strong>案件名：</strong>{{ $quote->project->name ?? 'N/A' }}</p>
        <p><strong>件名：</strong>{{ $quote->subject }}</p>
        <p><strong>納品予定日：</strong>{{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y年m月d日') : '未設定' }}</p>
        <p><strong>有効期限：</strong>{{ $quote->expiry_date }}</p>
        <p><strong>納品場所：</strong>{{ $quote->delivery_location ?: '未設定' }}</p>
        <p><strong>お支払条件：</strong>{{ $quote->payment_terms ?: '未設定' }}</p>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th class="text-left">商品名</th>
                    <th class="text-right">単価</th>
                    <th class="text-right">数量</th>
                    <th class="text-right">単位</th>
                    <th class="text-right">小計 (税抜)</th>
                    <th class="text-right">合計 (税込)</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSubtotal = 0; $totalTax = 0; @endphp
                @foreach ($quote->items as $item)
                    @php
                        $itemSubtotal = $item->price * $item->quantity;
                        $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                        $itemTotal = $itemSubtotal + $itemTax;
                        $totalSubtotal += $itemSubtotal;
                        $totalTax += $itemTax;
                    @endphp
                    <tr>
                        <td class="text-left">{{ $item->item_name }}</td>
                        <td class="text-right">¥{{ number_format($item->price) }}</td>
                        <td class="text-right">{{ number_format($item->quantity) }}</td>
                        <td class="text-right">{{ $item->unit ?: '-' }}</td>
                        <td class="text-right">¥{{ number_format(round($itemSubtotal, 0)) }}</td>
                        <td class="text-right">¥{{ number_format(round($itemTotal, 0)) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="no-border">&nbsp;</td>
                    <td class="text-right">小計 (税抜)</td>
                    <td class="text-right">¥{{ number_format(round($totalSubtotal, 0)) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="no-border">&nbsp;</td>
                    <td class="text-right">消費税</td>
                    <td class="text-right">¥{{ number_format(round($totalTax, 0)) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="no-border">&nbsp;</td>
                    <td class="text-right">合計金額 (税込)</td>
                    <td class="text-right">¥{{ number_format($quote->total_amount) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <p><strong>備考：</strong></p>
        <p>{{ $quote->notes ?: '-' }}</p>
    </div>
</body>
</html>