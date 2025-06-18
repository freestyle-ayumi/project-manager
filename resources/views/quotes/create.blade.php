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
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    data-project-client-map='@json($projectClientMap)'> {{-- プロジェクトと顧客のマップをJSONで渡す --}}
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

                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700">顧客 (必須)</label>
                                <select name="client_id" id="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">顧客を選択</option>
                                    @foreach($clients as $client)
                                        <option class="client-option" value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                <input type="text" name="quote_number" id="quote_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quote_number') }}" required>
                                @error('quote_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">発行日 (必須)</label>
                                <input type="date" name="issue_date" id="issue_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">有効期限 (例: 3ヶ月) (必須)</label>
                                <input type="text" id="expiry_date" name="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('expiry_date', '3ヶ月') }}" required>
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">件名 (必須)</label>
                                <input type="text" id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
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
                                    <div class="md:col-span-3">
                                        <label for="item_name_{{ $index }}" class="block text-sm font-medium text-gray-700">項目名</label>
                                        <input type="text" name="items[{{ $index }}][item_name]" id="item_name_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="{{ $item['item_name'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1" name="items[{{ $index }}][price]" id="price_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="{{ $item['price'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="quantity_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="{{ $item['quantity'] ?? '1' }}" required>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" id="unit_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $item['unit'] ?? '' }}">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="tax_rate_{{ $index }}" class="block text-sm font-medium text-gray-700">税率 (%)</label>
                                        <input type="number" step="0.01" name="items[{{ $index }}][tax_rate]" id="tax_rate_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="{{ $item['tax_rate'] ?? '10' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="subtotal_${{ $index }}" class="block text-sm font-medium text-gray-700">小計</label>
                                        {{-- JavaScriptで計算するため、valueは初期値0または計算結果で更新 --}}
                                        <input type="text" id="subtotal_${{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                                    </div>
                                    <div class="md:col-span-12">
                                        <label for="memo_${index}" class="block text-sm font-medium text-gray-700">備考</label>
                                        <textarea name="items[{{ $index }}][memo]" id="memo_${index}" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $item['memo'] ?? '' }}</textarea>
                                    </div>
                                    {{-- 最初の1行以外、または複数行ある場合にのみ削除ボタンを表示 --}}
                                    @if ($loop->index > 0 || count($items) > 1) 
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            @endforeach
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
                                作成
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
                const clientSelect = document.getElementById('client_id');
                const initialClientOptions = Array.from(clientSelect.options); 

                const projectClientMap = JSON.parse(projectSelect.dataset.projectClientMap || '{}');
                console.log("projectClientMap:", projectClientMap); // デバッグログ

                function filterClients() {
                    const selectedProjectId = projectSelect.value;
                    const previouslySelectedClientId = clientSelect.value; 
                    console.log("filterClients called. selectedProjectId:", selectedProjectId, "previouslySelectedClientId:", previouslySelectedClientId); // デバッグログ

                    clientSelect.innerHTML = '<option value="">顧客を選択</option>';

                    if (selectedProjectId) {
                        const associatedClientId = projectClientMap[selectedProjectId];
                        console.log("Associated Client ID for project:", associatedClientId); // デバッグログ

                        if (associatedClientId) {
                            const clientOption = initialClientOptions.find(option => option.value == associatedClientId);
                            if (clientOption) {
                                clientSelect.add(clientOption.cloneNode(true));
                                clientSelect.value = associatedClientId; 
                                console.log("Client auto-selected:", associatedClientId); // デバッグログ
                            } else {
                                console.warn("Associated client option not found in initialClientOptions for ID:", associatedClientId); // デバッグログ
                            }
                        } else {
                            console.log("No associated client found in map for project ID:", selectedProjectId); // デバッグログ
                        }
                    } else {
                        initialClientOptions.forEach(option => {
                            if (option.value !== "") { 
                                clientSelect.add(option.cloneNode(true));
                            }
                        });
                        const oldClientId = "{{ old('client_id') }}";
                        if (oldClientId) {
                            clientSelect.value = oldClientId;
                            console.log("Restored old client ID:", oldClientId); // デバッグログ
                        } else {
                            clientSelect.value = ""; 
                            console.log("No project selected, reset client dropdown."); // デバッグログ
                        }
                    }
                }

                projectSelect.addEventListener('change', filterClients);

                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-button');
                let itemIndex = {{ old('items') ? count(old('items')) : 1 }};
                console.log("Initial itemIndex for items:", itemIndex); // デバッグログ


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
                        <div class="md:col-span-3">
                            <label for="item_name_${itemIndex}" class="block text-sm font-medium text-gray-700">項目名</label>
                            <input type="text" name="items[${itemIndex}][item_name]" id="item_name_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="${itemData.item_name || ''}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="price_${itemIndex}" class="block text-sm font-medium text-gray-700">単価</label>
                            <input type="number" step="1" name="items[${itemIndex}][price]" id="price_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="${itemData.price || ''}" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="quantity_${itemIndex}" class="block text-sm font-medium text-gray-700">数量</label>
                            <input type="number" name="items[${itemIndex}][quantity]" id="quantity_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="${itemData.quantity || '1'}" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="unit_${itemIndex}" class="block text-sm font-medium text-gray-700">単位</label>
                            <input type="text" name="items[${itemIndex}][unit]" id="unit_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${itemData.unit || ''}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tax_rate_${itemIndex}" class="block text-sm font-medium text-gray-700">税率 (%)</label>
                            <input type="number" step="0.01" name="items[${itemIndex}][tax_rate]" id="tax_rate_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="${itemData.tax_rate || '10'}" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="subtotal_${itemIndex}" class="block text-sm font-medium text-gray-700">小計</label>
                            <input type="text" id="subtotal_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                        </div>
                        <div class="md:col-span-12">
                            <label for="memo_${itemIndex}" class="block text-sm font-medium text-gray-700">備考</label>
                            <textarea name="items[${itemIndex}][memo]" id="memo_${itemIndex}" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">${itemData.memo || ''}</textarea>
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

                // ページの初期ロード時、またはバリデーションエラーでold()データがある場合にフィルターを実行
                if (projectSelect.value) {
                    filterClients();
                } else {
                     // プロジェクトが何も選択されていない場合でも、全ての顧客オプションを適切に表示させる
                    clientSelect.innerHTML = '<option value="">顧客を選択</option>';
                    initialClientOptions.forEach(option => {
                        if (option.value !== "") { 
                            clientSelect.add(option.cloneNode(true));
                        }
                    });
                    const oldClientId = "{{ old('client_id') }}";
                    if (oldClientId) {
                        clientSelect.value = oldClientId;
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
