<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('見積書詳細') }}
        </h2>
    </x-slot>

    {{-- PDF出力時にDomPDFがこのスタイルを使用できるように設定 --}}
    <style>
        @font-face {
            font-family: 'ipaexg';
            src: url('{{ storage_path("fonts/ipaexg.ttf") }}') format('truetype');
        }

        html, body, h1, h2, h3, h4, h5, h6, table, th, td, p, span, div, strong {
            font-family: 'ipaexg', sans-serif !important;
        }

    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-700">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- トップセクション：タイトル --}}
                <div class="flex flex-col mb-0">
                    <h1 class="text-3xl font-bold text-gray-800 mx-auto">御見積書</h1>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 mb-2 items-center ps-2">
                    {{-- 左側 --}}
                    <div class="md:col-span-8 text-xl mr-10 pt-2 pl-3 font-medium">
                        {{ $quote->client->name ?? 'N/A' }}<span class="pl-3">御中</span>
                    </div>
                    {{-- 右側 --}}
                    <div class="md:col-span-4 text-sm text-gray-700 text-left md:mt-0 text-right mr-1">
                        <p class="mb-1">見積番号<span class="mr-1 ml-1">:</span>{{ $quote->quote_number }}</p>
                        <p class="mb-1">発行日<span class="mr-1 ml-1">:</span>{{ \Carbon\Carbon::parse($quote->issue_date)->format('Y年m月d日') }}</p>
                    </div>
                </div>
                {{-- 顧客名と自社情報の2カラム --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 mb-2 items-center ps-2">
                    {{-- 左側 --}}
                    <div class="md:col-span-8 mb-2 ps-2">
                        <p class="mb-2">下記のとおり、御見積もり申し上げます。</p>
                        <p>{{ $quote->project->name ?? 'N/A' }}</p>
                        <div class="overflow-x-auto mb-1">
                            <table class="divide-y divide-gray-200 text-sm">
                                <tbody>
                                    <tr class="border-4 border-white">
                                        <th scope="col" class="px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100" style="width: 40%;">件名</th>
                                        <td scope="col" class="px-3 py-1.5 font-normal text-left uppercase tracking-wider" style="width: 60%;">{{ $quote->subject }}</td>
                                    </tr>
                                    <tr class="border-4 border-white">
                                        <th scope="col" class="px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100" style="width: 40%;">納品予定日</th>
                                        <td scope="col" class="px-3 py-1.5 font-normal text-left uppercase tracking-wider" style="width: 60%;">{{ $quote->delivery_date ? \Carbon\Carbon::parse($quote->delivery_date)->format('Y年m月d日') : '未設定' }}</td>
                                    </tr>
                                    <tr class="border-4 border-white">
                                        <th scope="col" class="px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100" style="width: 40%;">有効期限</th>
                                        <td scope="col" class="px-3 py-1.5 font-normal text-left uppercase tracking-wider" style="width: 60%;">{{ $quote->expiry_date }}</td>
                                    </tr>
                                    <tr class="border-4 border-white">
                                        <th scope="col" class="px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100" style="width: 40%;">納品場所</th>
                                        <td scope="col" class="px-3 py-1.5 font-normal text-left uppercase tracking-wider" style="width: 60%;">{{ $quote->delivery_location ?: '未設定' }}</td>
                                    </tr>
                                    <tr class="border-4 border-white">
                                        <th scope="col" class="px-3 py-1.5 font-normal text-center uppercase tracking-wider bg-lime-100" style="width: 40%;">お支払条件</th>
                                        <td scope="col" class="px-3 py-1.5 font-normal text-left uppercase tracking-wider" style="width: 60%;">{{ $quote->payment_terms ?: '未設定' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    {{-- 右側 --}}
                    <div class="md:col-span-4 text-sm text-left md:mt-0 mt-6">
                        {{-- 会社ロゴ (プレースホルダー) --}}
                        <img src="https://placehold.co/150x50/E0E7FF/4338CA?text=YOUR+LOGO" alt="Company Logo" class="mb-2 ml-auto rounded-md">
                        {{-- 見積番号と発行日はここに移動 --}}
                        <p>株式会社フリースタイルエンターテイメント</p>
                        <p>〒710-0038</p>
                        <p>岡山県倉敷市新田2554</p>
                        <p>TEL<span class="mr-1 ml-1">:</span>086-435-5557</p>
                        <p>E-mail<span class="mr-1 ml-1">:</span>{{ Auth::user()->email ?? 'N/A' }}</p>
                        <p>担当<span class="mr-1 ml-1">:</span>{{ Auth::user()->name ?? 'N/A' }}</p>
                        <p>適格登録番号<span class="mr-1 ml-1">:</span>T62600010027085</p>
                    </div>
                </div>
                    {{-- 見積金額合計 --}}
                    <div class="grid grid-cols-3 md:grid-cols-12 items-center mb-2 ps-2">
                        <div class="md:col-span-2 bg-lime-100 font-bold p-1 border text-center">
                            御見積金額<br>
                            (消費税込)</div>
                        <div class="md:col-span-5 text-2xl border border-l-0 p-3 text-center">
                            ¥{{ number_format($quote->total_amount) }}
                        </div>
                        <div class="md:col-span-5"></div>
                    </div>

                {{-- 明細テーブル --}}
                <div class="overflow-x-auto mb-1 border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-lime-100">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" style="width: 45%;">商品名</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider" style="width: 15%;">単価</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider" style="width: 5%;">数量</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider" style="width: 5%;">単位</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider" style="width: 15%;">小計 (税抜)</th>
                                <th scope="col" class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider" style="width: 15%;">合計 (税込)</th>
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
                                    $itemTotal = $itemSubtotal + $itemTax;
                                    
                                    $totalSubtotal += $itemSubtotal;
                                    $totalTax += $itemTax;
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">{{ $item->item_name }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right">¥{{ number_format($item->price) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right">{{ number_format($item->quantity) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm">{{ $item->unit }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right">¥{{ number_format(round($itemSubtotal, 0)) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right">¥{{ number_format(round($itemTotal, 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="grid md:grid-cols-12 items-center">
                    <div class="md:col-span-8"></div>
                    <div class="md:col-span-2 bg-lime-100 p-3 text-center">
                        小計 (税抜)</div>
                    <div class="md:col-span-2 text-2xl p-2 text-right text-lg border border-l-0">
                        ¥{{ number_format(round($totalSubtotal, 0)) }}
                    </div>
                </div>
                <div class="grid md:grid-cols-12 items-center">
                    <div class="md:col-span-8"></div>
                    <div class="md:col-span-2 bg-lime-100 p-2 text-center">
                        消費税</div>
                    <div class="md:col-span-2 text-2xl p-2 text-right text-lg border border-l-0 border-t-0">
                        ¥{{ number_format(round($totalTax, 0)) }}
                    </div>
                </div>
                <div class="grid md:grid-cols-12 items-center mb-2">
                    <div class="md:col-span-8"></div>
                    <div class="md:col-span-2 bg-lime-100 font-bold p-3 text-center">
                        合計金額 (税込)</div>
                    <div class="md:col-span-2 text-2xl p-2 text-right text-lg font-bold border border-l-0 border-t-0">
                        ¥{{ number_format($quote->total_amount) }}
                    </div>
                </div>

                {{-- 備考 --}}
                <div class=" mb-2 rounded-md shadow-sm  font-normal">
                    <p class="bg-lime-100 border p-2">備考</p>
                    <p class="text-gray-900 whitespace-pre-wrap border border-t-0 p-2">{{ $quote->notes ?: '-' }}</p>
                </div>

                {{-- アクションボタン --}}
                <div class="flex justify-end space-x-4 mt-4">
                    <a href="{{ route('quotes.edit', $quote) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white pt-1 pb-1.5 pr-2 pl-2 text-sm rounded-md">
                        編集
                    </a>
                    <a href="{{ route('quotes.index') }}" class="bg-lime-500 hover:bg-lime-600 text-white pt-1 pb-1.5 pr-2 pl-2 text-sm rounded-md">
                        一覧に戻る
                    </a>
                    {{-- PDF出力ボタンを更新 --}}
                    <a href="{{ route('quotes.generatePdf', $quote) }}" class="bg-red-500 hover:bg-red-600 text-white pt-1 pb-1.5 pr-2 pl-2 text-sm rounded-md">
                        PDF出力
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
