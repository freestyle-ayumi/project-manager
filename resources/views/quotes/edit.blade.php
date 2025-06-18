<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('見積書編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-xl mb-4">見積書を編集</h3>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('quotes.update', $quote) }}" method="POST">
                        @csrf
                        @method('PATCH') {{-- 更新には PATCH メソッドを使用 --}}

                        {{-- ヘッダ情報 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (任意)</label>
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    data-project-client-map='@json($projectClientMap)'> {{-- プロジェクトと顧客のマップをJSONで渡す --}}
                                    <option value="">プロジェクトを選択</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ old('project_id', $quote->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700">顧客 (必須)</label>
                                <select name="client_id" id="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">顧客を選択</option>
                                    @foreach($clients as $client)
                                        <option class="client-option" value="{{ $client->id }}" {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="quote_number" class="block text-sm font-medium text-gray-700">見積番号 (必須)</label>
                                <input type="text" name="quote_number" id="quote_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quote_number', $quote->quote_number) }}" required>
                                @error('quote_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">発行日 (必須)</label>
                                <input type="date" name="issue_date" id="issue_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('issue_date', $quote->issue_date) }}" required>
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">有効期限 (例: 3ヶ月) (必須)</label>
                                <input type="text" id="expiry_date" name="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('expiry_date', $quote->expiry_date) }}" required>
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">件名 (必須)</label>
                                <input type="text" id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('subject', $quote->subject) }}" required>
                                @error('subject')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $quote->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- 明細情報 --}}
                        <h4 class="font-bold text-lg mb-3">明細</h4>
                        <div id="items-container" class="space-y-4 mb-6">
                            @foreach(old('items', $quote->items) as $index => $item)
                                @php
                                    // $itemがEloquentモデルの場合と配列の場合の両方に対応
                                    $itemId = $item->id ?? ($item['id'] ?? null);
                                    $itemName = $item->item_name ?? ($item['item_name'] ?? '');
                                    $itemPrice = $item->price ?? ($item['price'] ?? '');
                                    $itemQuantity = $item->quantity ?? ($item['quantity'] ?? '1');
                                    $itemUnit = $item->unit ?? ($item['unit'] ?? '');
                                    $itemTaxRate = $item->tax_rate ?? ($item['tax_rate'] ?? '10');
                                    $itemMemo = $item->memo ?? ($item['memo'] ?? '');
                                    // subtotalはJavaScriptで計算するため、ここでは古い値は表示しない
                                @endphp
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end border p-4 rounded-md relative">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $itemId }}">
                                    <div class="md:col-span-3">
                                        <label for="item_name_{{ $index }}" class="block text-sm font-medium text-gray-700">項目名</label>
                                        <input type="text" name="items[{{ $index }}][item_name]" id="item_name_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="{{ $itemName }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1" name="items[{{ $index }}][price]" id="price_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="{{ $itemPrice }}" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="quantity_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="{{ $itemQuantity }}" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" id="unit_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $itemUnit }}">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="tax_rate_{{ $index }}" class="block text-sm font-medium text-gray-700">税率 (%)</label>
                                        <input type="number" step="0.1" name="items[{{ $index }}][tax_rate]" id="tax_rate_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="{{ $itemTaxRate }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="subtotal_${{ $index }}" class="block text-sm font-medium text-gray-700">小計</label>
                                        {{-- JavaScriptで計算するため、valueは初期値0または計算結果で更新 --}}
                                        <input type="text" id="subtotal_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                                    </div>
                                    <div class="md:col-span-12">
                                        <label for="memo_${index}" class="block text-sm font-medium text-gray-700">備考</label>
                                        <textarea name="items[{{ $index }}][memo]" id="memo_${index}" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $itemMemo }}</textarea>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            {{-- 明細がない場合は初期行を追加 --}}
                            @if($quote->items->isEmpty() && !old('items'))
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end border p-4 rounded-md relative">
                                    <div class="md:col-span-3">
                                        <label for="item_name_0" class="block text-sm font-medium text-gray-700">項目名</label>
                                        <input type="text" name="items[0][item_name]" id="item_name_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="price_0" class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1" name="items[0][price]" id="price_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="quantity_0" class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[0][quantity]" id="quantity_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="1" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="unit_0" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[0][unit]" id="unit_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="tax_rate_0" class="block text-sm font-medium text-gray-700">税率 (%)</label>
                                        <input type="number" step="0.1" name="items[0][tax_rate]" id="tax_rate_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="10" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="subtotal_0" class="block text-sm font-medium text-gray-700">小計</label>
                                        <input type="text" id="subtotal_0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                                    </div>
                                    <div class="md:col-span-12">
                                        <label for="memo_0" class="block text-sm font-medium text-gray-700">備考</label>
                                        <textarea name="items[0][memo]" id="memo_0" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" id="add-item-button" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md mb-6">
                            明細行を追加
                        </button>

                        {{-- 合計金額表示 --}}
                        <div class="flex justify-end items-center mb-6">
                            <p class="text-xl font-bold text-gray-800">合計金額: <span id="display-total-amount">¥0</span></p>
                            <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                                更新
                            </button>
                            <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScriptセクション --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const projectSelect = document.getElementById('project_id');
                const clientSelect = document.getElementById('client_id');
                const initialClientOptions = Array.from(clientSelect.options); // 全ての顧客オプションを保持

                // projectClientMap をDOMから取得
                const projectClientMap = JSON.parse(projectSelect.dataset.projectClientMap || '{}');

                function filterClients() {
                    const selectedProjectId = projectSelect.value;
                    const previouslySelectedClientId = clientSelect.value; // 現在選択されている顧客IDを保持

                    // まず全てのオプションを削除（"顧客を選択"以外）
                    clientSelect.innerHTML = '<option value="">顧客を選択</option>';

                    if (selectedProjectId) {
                        const associatedClientId = projectClientMap[selectedProjectId];
                        if (associatedClientId) {
                            // 関連する顧客だけを追加
                            const clientOption = initialClientOptions.find(option => option.value == associatedClientId);
                            if (clientOption) {
                                clientSelect.add(clientOption.cloneNode(true));
                                clientSelect.value = associatedClientId; // 該当顧客を自動選択
                            }
                        }
                    } else {
                        // プロジェクトが選択されていない場合は全ての顧客オプションを復元
                        initialClientOptions.forEach(option => {
                            if (option.value !== "") { // "顧客を選択" オプションはスキップ
                                clientSelect.add(option.cloneNode(true));
                            }
                        });
                        // 以前選択されていた顧客を復元試行
                        if (previouslySelectedClientId && initialClientOptions.some(option => option.value == previouslySelectedClientId)) {
                            clientSelect.value = previouslySelectedClientId;
                        } else {
                            clientSelect.value = ""; // 「顧客を選択」に戻す
                        }
                    }
                }

                // プロジェクト選択ボックスの変更イベントをリッスン
                projectSelect.addEventListener('change', filterClients);

                // ページの初期ロード時、またはバリデーションエラーでold()データがある場合にフィルターを実行
                // old('project_id')が存在するか、または既存のquoteにproject_idが設定されている場合
                const initialProjectId = projectSelect.value;
                if (initialProjectId) {
                    filterClients();
                }


                // ---------------- 明細計算ロジック (create.blade.phpと共通) ----------------
                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-button');
                // itemIndexの初期値をold('items')がセットされている場合はその数、そうでなければquote.itemsの数、どちらもなければ1から開始
                let itemIndex = {{ old('items') ? count(old('items')) : ($quote->items->count() > 0 ? $quote->items->count() : 1) }};

                // 各行の小計を計算し、全体の合計を更新する関数
                const calculateRowTotal = (row) => {
                    const priceInput = row.querySelector('.item-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const taxRateInput = row.querySelector('.item-tax-rate');
                    const subtotalInput = row.querySelector('.item-subtotal');

                    if (!priceInput || !quantityInput || !taxRateInput || !subtotalInput) {
                        console.error("Missing input elements in row:", row);
                        return;
                    }

                    const price = parseFloat(priceInput.value) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    const taxRate = parseFloat(taxRateInput.value) || 0;

                    const subtotal = price * quantity;
                    const tax = subtotal * (taxRate / 100);
                    let rowTotal = subtotal + tax;

                    // 小計を四捨五入して整数にする
                    rowTotal = Math.round(rowTotal); 

                    subtotalInput.value = rowTotal.toLocaleString(); // 小数点以下なしで表示
                    calculateTotals(); // 行の合計が変わったら全体の合計も更新
                };

                // 各明細行にイベントリスナーを設定する関数
                function attachEventListenersToRow(row) {
                    const priceInput = row.querySelector('.item-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const taxRateInput = row.querySelector('.item-tax-rate');
                    const removeButton = row.querySelector('.remove-item-row');

                    if (priceInput) priceInput.addEventListener('input', () => calculateRowTotal(row));
                    if (quantityInput) quantityInput.addEventListener('input', () => calculateRowTotal(row));
                    if (taxRateInput) taxRateInput.addEventListener('input', () => calculateRowTotal(row));
                    
                    if (removeButton) {
                        removeButton.addEventListener('click', () => {
                            row.remove();
                            calculateTotals(); // 行が削除されたら全体の合計も更新
                        });
                    }

                    // 初期計算をここで行う（DOMロード時や新規追加時に実行）
                    calculateRowTotal(row); 
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

                    // 合計金額を四捨五入して整数にする
                    grandTotal = Math.round(grandTotal);

                    document.getElementById('display-total-amount').textContent = '¥' + grandTotal.toLocaleString(); // 小数点以下なしで表示
                    document.getElementById('total_amount_input').value = grandTotal; // hiddenフィールドに設定
                }

                // "明細行を追加" ボタンのイベントリスナー
                addItemButton.addEventListener('click', () => {
                    const newRow = createItemRow();
                    itemsContainer.appendChild(newRow);
                    attachEventListenersToRow(newRow); // 新しく追加した行にもイベントリスナーを設定
                });

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
