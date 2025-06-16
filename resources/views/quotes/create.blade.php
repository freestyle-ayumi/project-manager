<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規見積書作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-xl mb-4">新しい見積書を作成</h3>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('quotes.store') }}" method="POST">
                        @csrf

                        {{-- ヘッダ情報 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (任意)</label>
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">プロジェクトを選択</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700">顧客 <span class="text-red-500">*</span></label>
                                <select name="client_id" id="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">顧客を選択</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quote_number" class="block text-sm font-medium text-gray-700">見積書番号 <span class="text-red-500">*</span></label>
                                <input type="text" name="quote_number" id="quote_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quote_number') }}" required>
                                @error('quote_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">発行日 <span class="text-red-500">*</span></label>
                                <input type="date" name="issue_date" id="issue_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">有効期限 <span class="text-red-500">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('expiry_date') }}" required>
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">ステータス <span class="text-red-500">*</span></label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="作成中" {{ old('status') == '作成中' ? 'selected' : '' }}>作成中</option>
                                    <option value="送信済み" {{ old('status') == '送信済み' ? 'selected' : '' }}>送信済み</option>
                                    <option value="承認済み" {{ old('status') == '承認済み' ? 'selected' : '' }}>承認済み</option>
                                    <option value="拒否済み" {{ old('status') == '拒否済み' ? 'selected' : '' }}>拒否済み</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 明細情報 --}}
                        <h4 class="font-bold text-lg mb-3">見積書明細 <span class="text-red-500">*</span></h4>
                        <div id="items-container">
                            {{-- ここに明細行がJavaScriptによって追加される --}}
                            @if (old('items'))
                                @foreach (old('items') as $index => $item)
                                    @include('quotes.partials.item_row', ['index' => $index, 'item' => $item])
                                @endforeach
                            @else
                                {{-- 初期表示用に1行追加 --}}
                                @include('quotes.partials.item_row', ['index' => 0, 'item' => []])
                            @endif
                        </div>

                        <button type="button" id="add-item-row" class="mt-4 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            明細行を追加
                        </button>

                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex justify-end items-center mb-2">
                                <span class="text-lg font-bold text-gray-800 mr-4">合計金額:</span>
                                <span id="display-total-amount" class="text-2xl font-extrabold text-indigo-700">¥0</span>
                            </div>
                            {{-- total_amountはhiddenフィールドで送信、コントローラで計算されるためreadonly --}}
                            <input type="hidden" name="total_amount" id="total_amount_input" value="{{ old('total_amount', 0) }}">
                        </div>


                        <div class="flex justify-end mt-8">
                            <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('キャンセル') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('見積書を作成') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for dynamic item rows and calculations --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-row');
                let itemIndex = {{ old('items') ? count(old('items')) : 1 }}; // 次に追加する行のインデックス

                // 明細行のテンプレート（バックティックで囲むと複数行文字列を扱いやすい）
                const itemRowTemplate = (index, item = {}) => `
                    <div class="item-row grid grid-cols-1 md:grid-cols-7 gap-4 border border-gray-200 p-4 rounded-md mb-4 bg-gray-50 relative">
                        <button type="button" class="remove-item-row absolute top-2 right-2 text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
                        <div class="md:col-span-2">
                            <label for="items_${index}_item_name" class="block text-sm font-medium text-gray-700">商品名 <span class="text-red-500">*</span></label>
                            <input type="text" name="items[${index}][item_name]" id="items_${index}_item_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${item.item_name || ''}" required>
                        </div>
                        <div>
                            <label for="items_${index}_price" class="block text-sm font-medium text-gray-700">単価 <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="items[${index}][price]" id="items_${index}_price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="${item.price || '0'}" required>
                        </div>
                        <div>
                            <label for="items_${index}_quantity" class="block text-sm font-medium text-gray-700">数量 <span class="text-red-500">*</span></label>
                            <input type="number" name="items[${index}][quantity]" id="items_${index}_quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="${item.quantity || '1'}" required>
                        </div>
                        <div>
                            <label for="items_${index}_unit" class="block text-sm font-medium text-gray-700">単位</label>
                            <input type="text" name="items[${index}][unit]" id="items_${index}_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${item.unit || ''}">
                        </div>
                        <div>
                            <label for="items_${index}_tax_rate" class="block text-sm font-medium text-gray-700">税率 (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="items[${index}][tax_rate]" id="items_${index}_tax_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="${item.tax_rate || '10'}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="items_${index}_memo" class="block text-sm font-medium text-gray-700">備考</label>
                            <textarea name="items[${index}][memo]" id="items_${index}_memo" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">${item.memo || ''}</textarea>
                        </div>
                        <div class="md:col-span-2 flex items-center justify-end">
                            <span class="text-sm font-medium text-gray-700 mr-2">小計: </span>
                            <span class="item-subtotal-display font-semibold">¥0</span>
                        </div>
                        <div class="md:col-span-2 flex items-center justify-end">
                            <span class="text-sm font-medium text-gray-700 mr-2">税額: </span>
                            <span class="item-tax-display font-semibold">¥0</span>
                        </div>
                    </div>
                `;

                // 明細行を追加する関数
                const addItemRow = (item = {}) => {
                    const newRow = document.createElement('div');
                    newRow.innerHTML = itemRowTemplate(itemIndex, item).trim();
                    itemsContainer.appendChild(newRow.firstChild); // trim()で空白を除去し、最初の要素だけ追加
                    itemIndex++;
                    attachEventListenersToRow(newRow.firstChild);
                    calculateTotals(); // 新しい行を追加したら合計を再計算
                };

                // 行内のイベントリスナーを設定する関数
                const attachEventListenersToRow = (row) => {
                    const priceInput = row.querySelector('.item-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const taxRateInput = row.querySelector('.item-tax-rate');
                    const removeButton = row.querySelector('.remove-item-row');

                    const inputs = [priceInput, quantityInput, taxRateInput];
                    inputs.forEach(input => {
                        if (input) {
                            input.addEventListener('input', calculateRowTotal);
                        }
                    });

                    if (removeButton) {
                        removeButton.addEventListener('click', function() {
                            row.remove();
                            calculateTotals(); // 行を削除したら合計を再計算
                        });
                    }

                    calculateRowTotal.call(priceInput); // 初期表示時に行の合計を計算
                };

                // 各行の小計と税額を計算し、全体の合計を更新する関数
                function calculateRowTotal() {
                    const row = this.closest('.item-row');
                    if (!row) return;

                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
                    const taxRate = parseFloat(row.querySelector('.item-tax-rate').value) || 0;

                    const subtotal = price * quantity;
                    const tax = subtotal * (taxRate / 100);
                    const rowTotal = subtotal + tax;

                    row.querySelector('.item-subtotal-display').textContent = '¥' + subtotal.toLocaleString();
                    row.querySelector('.item-tax-display').textContent = '¥' + tax.toLocaleString();

                    calculateTotals(); // 行の合計が変わったら全体の合計も更新
                }

                // 全体の合計金額を計算する関数
                function calculateTotals() {
                    let grandTotal = 0;
                    document.querySelectorAll('.item-row').forEach(row => {
                        const price = parseFloat(row.querySelector('.item-price').value) || 0;
                        const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
                        const taxRate = parseFloat(row.querySelector('.item-tax-rate').value) || 0;

                        const subtotal = price * quantity;
                        const tax = subtotal * (taxRate / 100);
                        grandTotal += (subtotal + tax);
                    });

                    document.getElementById('display-total-amount').textContent = '¥' + grandTotal.toLocaleString();
                    document.getElementById('total_amount_input').value = grandTotal; // hiddenフィールドに設定
                }

                // "明細行を追加" ボタンのイベントリスナー
                addItemButton.addEventListener('click', () => addItemRow());

                // ページロード時に既存の行にイベントリスナーを設定（バリデーションエラーでold()データがある場合など）
                document.querySelectorAll('.item-row').forEach(row => {
                    attachEventListenersToRow(row);
                });

                // 初期ロード時の合計計算
                calculateTotals();
            });
        </script>
    @endpush
</x-app-layout>