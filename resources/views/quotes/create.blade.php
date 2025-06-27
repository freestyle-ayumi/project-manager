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
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                            {{-- 見積番号: 幅2 --}}
                            <div class="md:col-span-2">
                                <label for="quote_number" class="block text-sm font-medium text-gray-700">見積番号<span class="text-red-500">*</span></label>
                                {{-- $defaultQuoteNumber があればそれを初期値とし、old()データがあればold()優先 --}}
                                <input type="text" name="quote_number" id="quote_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quote_number', $defaultQuoteNumber ?? '') }}" required>
                                @error('quote_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 関連プロジェクト: 幅6 (必須項目) --}}
                            <div class="md:col-span-6">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト<span class="text-red-500">*</span></label>
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    data-project-client-map='@json($projectClientMap)'
                                    data-all-clients-map='@json($allClientsMap)' required>
                                    <option value="">プロジェクトを選択</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 顧客: 幅4 --}}
                            <div class="md:col-span-4">
                                <label for="client_name_display" class="block text-sm font-medium text-gray-700">顧客</label>
                                <input type="text" id="client_name_display" class="mt-1 block w-full rounded-md bg-gray-100 text-gray-900 px-3 py-2 border border-gray-300 cursor-not-allowed" readonly placeholder="プロジェクトを選択してください">
                                <input type="hidden" name="client_id" id="client_id_hidden">
                                @error('client_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 件名: 幅8 --}}
                            <div class="md:col-span-8">
                                <label for="subject" class="block text-sm font-medium text-gray-700">件名<span class="text-red-500">*</span></label>
                                <input type="text" id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 有効期限: 幅2 --}}
                            <div class="md:col-span-2">
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">有効期限</label>
                                <input type="text" name="expiry_date" id="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('expiry_date', '3ヶ月') }}">
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 発行日: 幅2 --}}
                            <div class="md:col-span-2">
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">発行日<span class="text-red-500">*</span></label>
                                <input type="date" name="issue_date" id="issue_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 納品予定日、納品場所、お支払条件を3カラム表示 (2:7:3) --}}
                            <div class="md:col-span-12 grid grid-cols-1 md:grid-cols-12 gap-6">
                                {{-- 納品予定日 (日付): 幅2 --}}
                                <div class="md:col-span-2">
                                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">納品予定日</label>
                                    <input type="date" name="delivery_date" id="delivery_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('delivery_date') }}">
                                    @error('delivery_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- 納品場所 (255文字テキスト): 幅7 --}}
                                <div class="md:col-span-7">
                                    <label for="delivery_location" class="block text-sm font-medium text-gray-700">納品場所</label>
                                    <input type="text" name="delivery_location" id="delivery_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('delivery_location') }}" maxlength="255">
                                    @error('delivery_location')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- お支払条件 (255文字テキスト): 幅3 --}}
                                <div class="md:col-span-3">
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700">お支払条件</label>
                                    <input type="text" name="payment_terms" id="payment_terms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('payment_terms') }}" maxlength="255">
                                    @error('payment_terms')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- 備考: 幅12 --}}
                            <div class="md:col-span-12">
                                <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- 明細情報 --}}
                        <h4 class="font-bold text-lg mb-3">明細</h4>
                        <div id="items-container" class="space-y-4 mb-6">
                            @php
                                $items = old('items', [[]]); // oldデータがなければ空の配列を持つ配列で初期化 (最初の1行を表示するため)
                                if (empty($items)) {
                                    $items = [[]]; // 完全に空の場合も1行表示
                                }
                            @endphp

                            @foreach($items as $index => $item)
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end border p-4 rounded-md relative">
                                    {{-- hidden input for item ID (新規作成では不要だが、更新と共通化のため残す) --}}
                                    <input type="hidden" name="items[{{ $index }}][id]" value="">
                                    <div class="md:col-span-5"> {{-- 項目名: 幅5 --}}
                                        <label for="item_name_{{ $index }}" class="block text-sm font-medium text-gray-700">項目名<span class="text-red-500">*</span></label>
                                        <input type="text" name="items[{{ $index }}][item_name]" id="item_name_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="{{ $item['item_name'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-2"> {{-- 単価: 幅2 --}}
                                        <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700">単価<span class="text-red-500">*</span></label>
                                        <input type="number" step="1" name="items[{{ $index }}][price]" id="price_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="{{ $item['price'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-1"> {{-- 数量: 幅1 --}}
                                        <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">数量<span class="text-red-500">*</span></label>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="quantity_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="{{ $item['quantity'] ?? '1' }}" required>
                                    </div>
                                    <div class="md:col-span-1"> {{-- 単位: 幅1 --}}
                                        <label for="unit_${{ $index }}" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" id="unit_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $item['unit'] ?? '' }}">
                                    </div>
                                    <div class="md:col-span-1"> {{-- 税率: 幅1 --}}
                                        <label for="tax_rate_${{ $index }}" class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                                        <input type="number" step="1" name="items[{{ $index }}][tax_rate]" id="tax_rate_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="{{ $item['tax_rate'] ?? '10' }}" required>
                                    </div>
                                    <div class="md:col-span-2"> {{-- 小計: 幅2 --}}
                                        <label for="subtotal_${{ $index }}" class="block text-sm font-medium text-gray-700">小計</label>
                                        {{-- JavaScriptで計算するため、valueは初期値0または計算結果で更新 --}}
                                        <input type="text" id="subtotal_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-item-button" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold pt-2 pb-1.5 pr-2 pl-1 text-sm rounded-md mb-6">
                            ＋行
                        </button>

                        {{-- 合計金額表示 --}}
                        <div class="flex justify-end items-center mb-6">
                            <p class="text-xl font-bold text-gray-800">合計金額: <span id="display-total-amount">¥0</span></p>
                            <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold pt-2 pb-1.5 pr-2 pl-2 text-sm rounded-md">
                                作成
                            </button>
                            <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold pt-2 pb-1.5 pr-2 pl-2 text-sm rounded-md">
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
            console.log("create.blade.php JavaScript loaded.");

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

                rowTotal = Math.round(rowTotal); 

                subtotalInput.value = rowTotal.toLocaleString(); 
                calculateTotals(); 
            };

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

                grandTotal = Math.round(grandTotal);

                document.getElementById('display-total-amount').textContent = '¥' + grandTotal.toLocaleString(); 
                document.getElementById('total_amount_input').value = grandTotal; 
            }


            document.addEventListener('DOMContentLoaded', function () {
                const projectSelect = document.getElementById('project_id');
                const clientNameDisplay = document.getElementById('client_name_display'); // テキスト表示用input
                const clientIdHidden = document.getElementById('client_id_hidden'); // 隠しフィールド

                const projectClientMap = JSON.parse(projectSelect.dataset.projectClientMap || '{}');
                const allClientsMap = JSON.parse(projectSelect.dataset.allClientsMap || '{}'); // 全顧客名マップ

                console.log("projectClientMap:", projectClientMap); // デバッグログ
                console.log("allClientsMap:", allClientsMap); // デバッグログ

                function updateClientField() {
                    const selectedProjectId = projectSelect.value;
                    console.log("updateClientField called. selectedProjectId:", selectedProjectId); // デバッグログ

                    if (selectedProjectId) {
                        const associatedClientId = projectClientMap[selectedProjectId];
                        if (associatedClientId) {
                            const clientName = allClientsMap[associatedClientId];
                            if (clientName) {
                                clientNameDisplay.value = clientName; // inputに顧客名を設定
                                clientIdHidden.value = associatedClientId; // 隠しフィールドに顧客IDを設定
                                // 顧客は必須ではないため required は削除
                                // clientIdHidden.required = true; 
                                console.log("Client auto-selected and displayed as text:", clientName); // デバッグログ
                            } else {
                                console.warn("Client name not found in allClientsMap for ID:", associatedClientId); // デバッグログ
                                clientNameDisplay.value = "顧客名が見つかりません"; // エラーメッセージ
                                clientIdHidden.value = ""; // 隠しフィールドをクリア
                                // clientIdHidden.required = true; 
                            }
                        } else {
                            console.log("No associated client found in map for project ID:", selectedProjectId); // デバッグログ
                            clientNameDisplay.value = "プロジェクトに顧客が紐付いていません"; // メッセージ
                            clientIdHidden.value = ""; // 隠しフィールドをクリア
                            // clientIdHidden.required = true; 
                        }
                    } else {
                        // プロジェクトが選択されていない場合
                        clientNameDisplay.value = ""; // テキスト表示をクリア
                        clientNameDisplay.placeholder = "プロジェクトを選択してください"; // プレースホルダーを設定
                        clientIdHidden.value = ""; // 隠しフィールドをクリア
                        // clientIdHidden.required = true; 
                        console.log("No project selected, client field cleared."); // デバッグログ
                    }
                }

                // 初期ロード時とプロジェクト選択時のイベントリスナー
                projectSelect.addEventListener('change', updateClientField);

                // ページロード時の初期設定
                // old('project_id')が存在する場合も考慮してupdateClientFieldを呼び出す
                const oldProjectId = "{{ old('project_id') }}";
                if (oldProjectId) {
                    projectSelect.value = oldProjectId;
                }
                updateClientField(); // ページロード時に一度実行して初期値を設定


                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-button');
                let itemIndex = {{ old('items') ? count(old('items')) : (isset($quote) && !$quote->items->isEmpty() ? count($quote->items) : 1) }}; // 編集画面でのitemIndex初期値調整
                console.log("Initial itemIndex for items:", itemIndex); // デバッグログ


                function attachEventListenersToRow(row) {
                    const priceInput = row.querySelector('.item-price');
                    const quantityInput = row.querySelector('.item-quantity');
                    const taxRateInput = row.querySelector('.item-tax-rate');
                    const subtotalInput = row.querySelector('.item-subtotal'); 

                    if (!priceInput || !quantityInput || !taxRateInput || !subtotalInput) {
                        console.error("Missing input elements in row:", row);
                        return;
                    }

                    if (priceInput) priceInput.addEventListener('input', () => calculateRowTotal(row));
                    if (quantityInput) quantityInput.addEventListener('input', () => calculateRowTotal(row));
                    if (taxRateInput) taxRateInput.addEventListener('input', () => calculateRowTotal(row));
                    
                    const removeButton = row.querySelector('.remove-item-row');
                    if (removeButton) {
                        removeButton.addEventListener('click', () => {
                            row.remove();
                            calculateTotals(); 
                        });
                    }

                    calculateRowTotal(row); 
                }

                function createItemRow(itemData = {}) {
                    const newRow = document.createElement('div');
                    newRow.classList.add('item-row', 'grid', 'grid-cols-1', 'md:grid-cols-12', 'gap-4', 'items-end', 'border', 'p-4', 'rounded-md', 'relative');
                    newRow.innerHTML = `
                        <input type="hidden" name="items[${itemIndex}][id]" value="${itemData.id || ''}">
                        <div class="md:col-span-5">
                            <label for="item_name_${itemIndex}" class="block text-sm font-medium text-gray-700">項目名<span class="text-red-500">*</span></label>
                            <input type="text" name="items[${itemIndex}][item_name]" id="item_name_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="${itemData.item_name || ''}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="price_${itemIndex}" class="block text-sm font-medium text-gray-700">単価<span class="text-red-500">*</span></label>
                            <input type="number" step="1" name="items[${itemIndex}][price]" id="price_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="${itemData.price || ''}" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="quantity_${itemIndex}" class="block text-sm font-medium text-gray-700">数量<span class="text-red-500">*</span></label>
                            <input type="number" name="items[${itemIndex}][quantity]" id="quantity_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="${itemData.quantity || '1'}" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="unit_${itemIndex}" class="block text-sm font-medium text-gray-700">単位</label>
                            <input type="text" name="items[${itemIndex}][unit]" id="unit_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${itemData.unit || ''}">
                        </div>
                        <div class="md:col-span-1">
                            <label for="tax_rate_${itemIndex}" class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                            <input type="number" step="1" name="items[${itemIndex}][tax_rate]" id="tax_rate_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="${itemData.tax_rate || '10'}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="subtotal_${itemIndex}" class="block text-sm font-medium text-gray-700">小計</label>
                            <input type="text" id="subtotal_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                        </div>
                        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    itemIndex++;
                    return newRow;
                }

                addItemButton.addEventListener('click', () => {
                    const newRow = createItemRow();
                    itemsContainer.appendChild(newRow);
                    attachEventListenersToRow(newRow);
                });

                document.querySelectorAll('.item-row').forEach(row => {
                    attachEventListenersToRow(row);
                });

                calculateTotals();
            });
        </script>
    @endpush
</x-app-layout>
