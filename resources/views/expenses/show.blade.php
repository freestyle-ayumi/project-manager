<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            経費申請詳細
        </h2>
    </x-slot>

    <div class="py-4 text-gray-600">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6" style="@media (max-width: 400px) {padding: 0.5rem;}">

                {{-- 上部：申請者・日付・プロジェクト・ステータス --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white border border-slate-200 p-6 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-6 border-b pb-3">
                            <h3 class="font-bold text-2xl">{{ $expense->user->name ?? 'N/A' }} の申請</h3>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100">
                                {{ $expense->status->name ?? 'ステータスなし' }}
                            </span>
                        </div>
                        <dl class="space-y-4">
                            <div>
                                <dt class="font-medium">申請日</dt>
                                <dd class="ml-0">
                                    {{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('Y/m/d') : ($expense->created_at ? \Carbon\Carbon::parse($expense->created_at)->format('Y/m/d') : 'N/A') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium">関連プロジェクト</dt>
                                <dd class="ml-0">{{ $expense->project->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">経費合計</dt>
                                <dd class="ml-0">¥{{ number_format($expense->amount ?? $expense->items->sum(function($i){ return $i->subtotal ?? ($i->price * $i->quantity * (1 + ($i->tax_rate ?? 0)/100)); })) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
<pre class="mb-6 p-4 bg-gray-100 rounded-lg text-sm text-gray-700 overflow-auto">
{{ print_r($expense->toArray(), true) }}
</pre>
                {{-- 経費項目テーブル --}}
                <h4 class="font-bold text-xl pl-1 pb-2">経費項目</h4>
                <div class="overflow-x-auto mb-8 rounded-lg shadow-sm">
                    <table class="min-w-full border border-gray-200" style="border-collapse: collapse;">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">品名</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">単価</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">数量</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">単位</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">税率</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border border-gray-200">小計</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($expense->items as $item)
                                @php
                                    $subtotal = $item->subtotal ?? ($item->price * ($item->quantity ?: 0) * (1 + (($item->tax_rate ?? 0) / 100)));
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-700 border border-gray-200">{{ $item->item_name ?? ($item->description ?? '-') }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-700 border border-gray-200">¥{{ number_format($item->price ?? 0) }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-700 border border-gray-200">{{ $item->quantity ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 border border-gray-200">{{ $item->unit ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-700 border border-gray-200">{{ number_format($item->tax_rate ?? 0, 1) }}%</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-700 border border-gray-200">¥{{ number_format($subtotal) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 border border-gray-200">経費項目がありません。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- アクションボタン --}}
                <div class="mt-6 flex space-x-4 justify-end">
                    <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        編集
                    </a>
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        一覧に戻る
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
