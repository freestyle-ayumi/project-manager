<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('見積書詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- トップセクション：タイトル --}}
                <div class="flex flex-col">
                    {{-- タイトル --}}
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">御見積書</h1> {{-- mx-auto を text-center に変更して中央揃え --}}
                </div>
                
                {{-- 顧客名と自社情報の2カラムセクション --}}
                {{-- 親グリッドをmd:grid-cols-12にし、子要素のcol-spanを調整して右側を狭くする --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 mb-4 text-right items-center">
                    {{-- 左側: 顧客情報 --}}
                    <div class="md:col-span-8 mr-10"> {{-- 幅を広く (例: 12分割中8) --}}
                        <div class="mb-2">
                            <span class="text-gray-900">{{ $quote->client->name ?? 'N/A' }}</span><span class="font-medium text-gray-600 pl-3">御中</span>
                        </div>
                    </div>

                    {{-- 右側: 自社情報 --}}
                    <div class="md:col-span-4 text-sm text-gray-700 text-left md:mt-0 mt-6"> {{-- 幅を狭く (例: 12分割中4) --}}
                        {{-- 会社ロゴ (プレースホルダー) --}}
                        <img src="https://placehold.co/150x50/E0E7FF/4338CA?text=YOUR+LOGO" alt="Company Logo" class="mb-2 ml-auto rounded-md">
                        <div class="md:col-span-2">
                            <div class="mb-2">見積番号<span class="mr-1 ml-1">:</span>{{ $quote->quote_number }}</div>
                            <div class="mb-2">発行日<span class="mr-1 ml-1">:</span>{{ \Carbon\Carbon::parse($quote->issue_date)->format('Y年m月d日') }}</div>
                        </div>
                        <p>〒710-0038</p>
                        <p class="ml-4">岡山県倉敷市新田2554</p>
                        <p class="ml-4">株式会社フリースタイルエンターテイメント</p>
                        <p>TEL<span class="mr-1 ml-1">:</span>086-435-5557</p>
                        <p>E-mail<span class="mr-1 ml-1">:</span>{{ Auth::user()->email ?? 'N/A' }}</p>
                        <p>担当<span class="mr-1 ml-1">:</span>{{ Auth::user()->name ?? 'N/A' }}</p>
                        <p>適格登録番号<span class="mr-1 ml-1">:</span>T62600010027085</p>
                    </div>
                </div>
                {{-- 合計金額 --}}
                <div style="text-sm text-gray-900 font-bold border">
                    ¥{{ number_format($quote->total_amount) }}
                </div>
                {{-- 見積番号、発行日、件名などの主要なヘッダ情報 --}}
                {{-- 親グリッドをmd:grid-cols-12にし、子要素のcol-spanを調整 --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 mb-1 text-gray-700">
                    {{-- 左側: 見積書情報 --}}
                    <div class="md:col-span-8"> {{-- 顧客情報と同じ幅 (12分割中8) --}}
                            プロジェクト名<span class="mr-1 ml-1">:</span>{{ $quote->project->name ?? 'N/A' }}
                    </div>
                    {{-- 右側: 日付情報と件名 --}}
                    <div class="md:col-span-4 md:mt-0 mt-6 text-gray-700"> {{-- 自社情報と同じ幅 (12分割中4) --}}
                            有効期限<span class="mr-1 ml-1">:</span>{{ $quote->expiry_date }}
                    </div>
                </div>

                {{-- 納品関連情報 (3カラム表示) --}}
                <div class="mb-1 text-gray-600">
                    件名<span class="mr-1 ml-1">:</span>{{ $quote->subject }}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 mb-8 text-gray-600">
                    <div class="md:col-span-3 mb-4 md:mb-0">
                        <p>納品予定日<span class="mr-1 ml-1">:</span>{{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y年m月d日') : '未設定' }}</p>
                    </div>
                    <div class="md:col-span-6 mb-4 md:mb-0">
                        <p>納品場所<span class="mr-1 ml-1">:</span>{{ $quote->delivery_location ?: '未設定' }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <p>お支払条件<span class="mr-1 ml-1">:</span>{{ $quote->payment_terms ?: '未設定' }}</p>
                    </div>
                </div>

                {{-- 明細テーブル --}}
                <h3 class="text-x font-bold text-gray-800 mb-1 ml-1">明細</h3>
                <div class="overflow-x-auto mb-8 border rounded-md shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 45%;">項目名</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">単価</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">数量</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%;">単位</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">小計 (税抜)</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 15%;">合計 (税込)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalSubtotal = 0;
                                $totalTax = 0;
                            @endphp
                            @foreach ($quote->items as $item)
                                @php
                                    $itemSubtotal = $item->price * $item->quantity;
                                    $itemTax = $itemSubtotal * ($item->tax_rate / 100);
                                    $itemTotal = $itemSubtotal + $itemTax; // 税込みの行合計
                                    
                                    $totalSubtotal += $itemSubtotal;
                                    $totalTax += $itemTax;
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->item_name }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-right">¥{{ number_format($item->price) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($item->quantity) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->unit }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-right">¥{{ number_format(round($itemSubtotal, 0)) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 font-bold text-right">¥{{ number_format(round($itemTotal, 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="3"></td> {{-- 項目名,単価,数量 を結合して空にする --}}
                                <td colspan="2" class="px-3 py-3 text-right text-sm font-bold text-gray-700">小計 (税抜)</td> {{-- 単位, 小計(税抜) を結合して右寄せ --}}
                                <td class="px-3 py-3 text-right text-lg font-bold text-gray-900">¥{{ number_format(round($totalSubtotal, 0)) }}</td> {{-- 合計(税込) --}}
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td colspan="2" class="px-3 py-3 text-right text-sm font-bold text-gray-700">消費税</td>
                                <td class="px-3 py-3 text-right text-lg font-bold text-gray-900">¥{{ number_format(round($totalTax, 0)) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td colspan="2" class="px-3 py-3 text-right text-sm font-bold text-gray-700">合計金額 (税込)</td>
                                <td class="px-3 py-3 text-right text-xl font-extrabold text-gray-900">¥{{ number_format($quote->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 備考 --}}
                <div class="mb-8 p-4 border rounded-md bg-gray-50 shadow-sm">
                    <p class="font-medium text-gray-600 mb-2">備考</p>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $quote->notes ?: '特になし' }}</p>
                </div>

                {{-- アクションボタン --}}
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('quotes.edit', $quote) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                        編集
                    </a>
                    <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">
                        一覧に戻る
                    </a>
                    {{-- PDF出力ボタン (仮) --}}
                    {{-- 実際のPDF出力機能は追加のライブラリや設定が必要です --}}
                    <button onclick="alert('PDF出力機能は現在開発中です。')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md">
                        PDF出力
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
