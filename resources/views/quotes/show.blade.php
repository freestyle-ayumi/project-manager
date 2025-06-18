@extends('layouts.app') {{-- レイアウトファイル 'resources/views/layouts/app.blade.php' を継承していると仮定 --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">見積書詳細</h1>
        <div class="flex space-x-2">
            {{-- 編集ボタン --}}
            <a href="{{ route('quotes.edit', $quote) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                編集
            </a>
            {{-- 削除ボタン --}}
            <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('本当にこの見積書を削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    削除
                </button>
            </form>
            {{-- 一覧に戻るボタン --}}
            <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                一覧に戻る
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6 p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">見積書情報</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><strong class="font-semibold">見積書番号:</strong> {{ $quote->quote_number }}</p>
                <p class="text-gray-600"><strong class="font-semibold">発行日:</strong> {{ $quote->issue_date }}</p>
                <p class="text-gray-600"><strong class="font-semibold">有効期限:</strong> {{ $quote->valid_until }}</p>
                <p class="text-gray-600"><strong class="font-semibold">件名:</strong> {{ $quote->subject }}</p>
                <p class="text-gray-600"><strong class="font-semibold">合計金額:</strong> ¥{{ number_format($quote->total_amount) }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong class="font-semibold">プロジェクト:</strong> {{ $quote->project->name ?? 'N/A' }}</p>
                <p class="text-gray-600"><strong class="font-semibold">顧客:</strong> {{ $quote->client->name ?? 'N/A' }}</p>
                <p class="text-gray-600"><strong class="font-semibold">作成者:</strong> {{ $quote->user->name ?? 'N/A' }}</p>
                <p class="text-gray-600"><strong class="font-semibold">作成日時:</strong> {{ $quote->created_at->format('Y-m-d H:i') }}</p>
                <p class="text-gray-600"><strong class="font-semibold">更新日時:</strong> {{ $quote->updated_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-600"><strong class="font-semibold">備考:</strong> {{ $quote->notes ?? 'なし' }}</p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">明細</h2>
        @if($quote->items->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            項目名
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            単価
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            数量
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            単位
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            税率 (%)
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            小計
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            税額
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            備考
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quote->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->item_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ¥{{ number_format($item->price) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->unit ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->tax_rate }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ¥{{ number_format($item->subtotal) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ¥{{ number_format($item->tax) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->memo ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-600">この見積書には明細がありません。</p>
        @endif
    </div>
</div>
@endsection