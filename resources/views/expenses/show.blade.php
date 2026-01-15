  <x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
                {{ __('経費申請') }} <span class="text-gray-400 font-normal text-sm">- 申請データ No.{{ $expense->id }}</span>
            </h2>
            <a href="{{ route('expenses.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors">
                一覧へ戻る
            </a>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- 左側メイン：申請内容 --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- ヘッダー情報 --}}
                    <div class="bg-white p-5 rounded-md border border-gray-200">
                        <!-- イベント名 -->
                        <div class="text-xl font-bold text-gray-900">
                           <span class="text-gray-400 text-xs font-normal">該当イベント：</span> {{ $expense->project->name ?? '-' }}
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 text-sm">
                            <!-- 申請者 -->
                            <div class="text-gray-700">
                                <span class="text-gray-400 text-xs">申請者：</span>{{ $expense->user->name ?? 'N/A' }}
                            </div>

                            <!-- 申請日 -->
                            <div class="text-gray-700">
                               <span class="text-gray-400 text-xs">申請日：</span> {{ $expense->date ? \Carbon\Carbon::parse($expense->date)->format('y.m.d') : 'N/A' }}
                            </div>

                            <!-- ステータスバッジ -->
                            <div class="text-right">
                                <span class="px-4 py-1 border-l-4 font-bold text-xs tracking-widest bg-gray-50 inline-block
                                    {{ $expense->expense_status_id === 1 ? 'border-amber-400 text-amber-500' : '' }}
                                    {{ $expense->expense_status_id === 2 ? 'border-gray-400 text-gray-600' : '' }}
                                    {{ $expense->expense_status_id === 3 ? 'border-rose-500 text-rose-600' : '' }}
                                    {{ $expense->expense_status_id === 4 ? 'border-red-600 text-white bg-red-600' : '' }}
                                    {{ !in_array($expense->expense_status_id, [1,2,3,4]) ? 'border-gray-400 text-gray-600' : '' }}">
                                    {{ $expense->status->name ?? 'PENDING' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 明細テーブル：実線と余白で見やすく --}}
                    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden p-4">
                        <div class="p-2 border-b border-gray-100">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">経費申請項目</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr class="text-right text-xs text-gray-600">
                                        <th class="px-3 py-2 text-left font-bold uppercase tracking-tighter">品名 / 内容</th>
                                        <th class="px-3 py-2 font-bold uppercase tracking-tighter">単価</th>
                                        <th class="px-3 py-2 font-bold uppercase tracking-tighter">数量</th>
                                        <th class="px-3 py-2 font-bold uppercase tracking-tighter">税率</th>
                                        <th class="px-3 py-2 font-bold uppercase tracking-tighter">小計</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-500">
                                    @forelse($expense->items as $item)
                                        @php
                                            $subtotal = $item->subtotal ?? ($item->price * ($item->quantity ?: 0) * (1 + (($item->tax_rate ?? 0) / 100)));
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition text-xs">
                                            <td class="px-3 py-1">{{ $item->item_name ?? ($item->description ?? '-') }}</td>
                                            <td class="px-3 py-1 text-right">¥{{ number_format($item->price) }}</td>
                                            <td class="px-3 py-1 text-right">{{ $item->quantity }} <span class="block text-[10px] text-gray-300">{{ $item->unit }}</span></td>
                                            <td class="px-3 py-1 text-right">{{ number_format($item->tax_rate ?? 0, 0) }}%</td>
                                            <td class="px-3 py-1 text-right">¥{{ number_format($subtotal) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 text-center text-sm italic">データがありません.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 右側：経理操作と合計金額 --}}
                <div class="space-y-6">
                    {{-- 合計金額 --}}
                    <div class="bg-gray-900 p-5 pb-4 rounded-sm flex flex-col items-center justify-center">
                        <div class="text-center">
                            <span class="text-gray-400 text-xs uppercase tracking-widest mr-3">合計</span>
                            <span class="text-4xl font-light text-white leading-none tracking-wide">
                                <span class="text-red-400 text-xl mr-1">¥</span>{{ number_format($expense->amount ?? $expense->items->sum(fn($i) => $i->subtotal ?? ($i->price * $i->quantity * (1 + ($i->tax_rate ?? 0)/100)))) }}
                            </span>
                        </div>
                    </div>

                    {{-- 経理・管理者用メニュー --}}
                    @php
                        $isAccountant = (auth()->user()->role && auth()->user()->role->name === 'accounting')
                                    || auth()->user()->developer == 1;
                    @endphp

                    @if($isAccountant)
                    <div class="bg-white p-6 border border-gray-200 rounded-md">
                        <form action="{{ route('expenses.updateStatus', $expense) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="expense_status_id" class="w-full border-gray-200 bg-gray-50 pr-1 text-center text-xs tracking-widest focus:border-gray-900 focus:ring-0">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $expense->expense_status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full bg-gray-900 text-white py-3 text-xs uppercase hover:bg-red-900 transition" style="letter-spacing: 2px;">
                                ステータス変更
                            </button>
                        </form>
                    </div>
                @endif

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('expenses.edit', $expense) }}" class="text-center py-3 text-xs text-indigo-600 text-xs bg-white border border-gray-200 hover:bg-indigo-600 hover:text-white transition rounded-sm">
                            修正
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>