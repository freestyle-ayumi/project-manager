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
                                <input type="text" name="quote_number" id="quote_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quote_number', $defaultQuoteNumber ?? '') }}" required>
                                @error('quote_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 関連プロジェクト: 幅6 --}}
                            <div class="md:col-span-6">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト<span class="text-red-500">*</span></label>
                                <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required
                                    data-project-client-map='@json($projectClientMap ?? [])'
                                    data-all-clients-map='@json($allClientsMap ?? [])'>
                                    <option value="">プロジェクトを選択してください</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ (old('project_id', $selectedProjectId ?? '') == $project->id) ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 顧客: 幅4（表示のみ） --}}
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

                            {{-- 発行日: 幅2 (Flatpickr を当てる、デフォルトは本日) --}}
                            <div class="md:col-span-2">
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">
                                    発行日<span class="text-red-500">*</span>
                                </label>
                                {{-- type="text" にして Flatpickr を当てる --}}
                                <input
                                    type="text"
                                    name="issue_date"
                                    id="issue_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    value="{{ old('issue_date', date('Y/m/d')) }}"
                                    required
                                >
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            </div>
<div class="md:col-span-12 grid grid-cols-1 md:grid-cols-12 gap-6">
<!-- 納品予定日 / 納品場所 / お支払条件 -->
                                {{-- 納品予定日 (日付): 幅2 --}}
                                <div class="md:col-span-2">
                                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">納品予定日</label>
                                    <input
                                        type="text"
                                        name="delivery_date"
                                        id="delivery_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        value="{{ old('delivery_date') }}"
                                    >
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
                                    <input 
                                        type="text" 
                                        name="payment_terms" 
                                        id="payment_terms" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                        value="{{ old('payment_terms', '銀行振込') }}" 
                                        maxlength="255"
                                    >
                                    @error('payment_terms')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 明細情報 --}}
                        <h4 class="font-bold text-lg ml-3">明細</h4>
                        <div id="items-container" class="space-y-4 mx-6 mt-1">
                            @php
                                $items = old('items', [[]]);
                                if (empty($items)) { $items = [[]]; }
                            @endphp

                            @foreach($items as $index => $item)
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end border p-4 rounded-md relative">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="">
                                    <div class="md:col-span-5">
                                        <label for="item_name_{{ $index }}" class="block text-sm font-medium text-gray-700">品名<span class="text-red-500">*</span></label>
                                        <input type="text" name="items[{{ $index }}][item_name]" id="item_name_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="{{ $item['item_name'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1" name="items[{{ $index }}][price]" id="price_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="{{ $item['price'] ?? '' }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="quantity_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="{{ $item['quantity'] ?? '1' }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" id="unit_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $item['unit'] ?? '' }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="tax_rate_{{ $index }}" class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                                        <input type="number" step="1" name="items[{{ $index }}][tax_rate]" id="tax_rate_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="{{ $item['tax_rate'] ?? '10' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="subtotal_{{ $index }}" class="block text-sm font-medium text-gray-700">小計</label>
                                        <input type="text" id="subtotal_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed item-subtotal" value="0" readonly>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                                                    
                            <div class="m-6 mt-4">
                                <button type="button" id="add-item-button" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold pt-2 pb-1.5 pr-2 pl-1 text-sm rounded-md mb-6 ml-auto block">
                                ＋行
                            </button>
                                {{-- 合計金額 --}}
                            <div class="flex justify-end items-center mb-3 mr-1">
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
</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Flatpickr と ページ用スクリプトをフッタに差し込む --}}
    @push('scripts')
        <!-- Flatpickr CSS/JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('quotes.create: JS loaded');

            // --- project -> client 自動表示 ---
            const projectSelect = document.getElementById('project_id');
            const clientNameDisplay = document.getElementById('client_name_display');
            const clientIdHidden = document.getElementById('client_id_hidden');

            // controller から渡したマップ
            const projectClientMap = @json($projectClientMap ?? []);
            const allClientsMap = @json($allClientsMap ?? []);

            function updateClientField() {
                if (!projectSelect) return;
                const selectedProjectId = projectSelect.value;
                if (selectedProjectId && projectClientMap[selectedProjectId]) {
                    const clientId = String(projectClientMap[selectedProjectId]);
                    clientNameDisplay.value = allClientsMap[clientId] ?? '';
                    clientIdHidden.value = clientId;
                } else {
                    clientNameDisplay.value = '';
                    clientIdHidden.value = '';
                }
            }

            if (projectSelect) {
                projectSelect.addEventListener('change', updateClientField);

                // 初期選択: old() -> request('project_id') -> controller渡し（$selectedProjectId）の順
                const initialProjectId = "{{ old('project_id', request('project_id', $selectedProjectId ?? '')) }}";
                if (initialProjectId) {
                    projectSelect.value = initialProjectId;
                }
                updateClientField();
            }

            // --- Flatpickr 設定（日本語ローカライズ） ---
            // localize を先に呼ぶ
            if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.ja) {
                flatpickr.localize(flatpickr.l10ns.ja);
            }

            flatpickr("#issue_date", {
                dateFormat: "Y/m/d",
                allowInput: true,
                defaultDate: "{{ old('issue_date', date('Y/m/d')) }}"
            });

            flatpickr("#delivery_date", {
                dateFormat: "Y/m/d",
                allowInput: true,
                defaultDate: "{{ old('delivery_date', '') }}"
            });

            // --- 明細行・小計・合計の処理 ---
            const itemsContainer = document.getElementById('items-container');
            const addItemButton = document.getElementById('add-item-button');
            let itemIndex = {{ old('items') ? count(old('items')) : (isset($quote) && !$quote->items->isEmpty() ? count($quote->items) : 1) }};

            function calculateRowTotal(row) {
                const priceInput = row.querySelector('.item-price');
                const quantityInput = row.querySelector('.item-quantity');
                const taxRateInput = row.querySelector('.item-tax-rate');
                const subtotalInput = row.querySelector('.item-subtotal');

                if (!priceInput || !quantityInput || !taxRateInput || !subtotalInput) return;

                const price = parseFloat(priceInput.value) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const taxRate = parseFloat(taxRateInput.value) || 0;

                const subtotal = price * quantity;
                const tax = subtotal * (taxRate / 100);
                const rowTotal = Math.round(subtotal + tax);

                subtotalInput.value = rowTotal.toLocaleString();
                calculateTotals();
            }

            function calculateTotals() {
                let grandTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
                    const taxRate = parseFloat(row.querySelector('.item-tax-rate').value) || 0;
                    const subtotal = price * quantity;
                    const tax = subtotal * (taxRate / 100);
                    grandTotal += subtotal + tax;
                });
                grandTotal = Math.round(grandTotal);
                const disp = document.getElementById('display-total-amount');
                const hiddenTotal = document.getElementById('total_amount_input');
                if (disp) disp.textContent = '¥' + grandTotal.toLocaleString();
                if (hiddenTotal) hiddenTotal.value = grandTotal;
            }

            function attachEventListenersToRow(row) {
                const priceInput = row.querySelector('.item-price');
                const quantityInput = row.querySelector('.item-quantity');
                const taxRateInput = row.querySelector('.item-tax-rate');
                const removeButton = row.querySelector('.remove-item-row');

                if (priceInput) priceInput.addEventListener('input', () => calculateRowTotal(row));
                if (quantityInput) quantityInput.addEventListener('input', () => calculateRowTotal(row));
                if (taxRateInput) taxRateInput.addEventListener('input', () => calculateRowTotal(row));
                if (removeButton) removeButton.addEventListener('click', () => { row.remove(); calculateTotals(); });

                // 初期計算
                calculateRowTotal(row);
            }

            function createItemRow(itemData = {}) {
                const newRow = document.createElement('div');
                newRow.className = 'item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end border p-4 rounded-md relative';
                newRow.innerHTML = `
                    <input type="hidden" name="items[${itemIndex}][id]" value="${itemData.id || ''}">
                    <div class="md:col-span-5">
                        <label for="item_name_${itemIndex}" class="block text-sm font-medium text-gray-700">品名<span class="text-red-500">*</span></label>
                        <input type="text" name="items[${itemIndex}][item_name]" id="item_name_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-name" value="${itemData.item_name || ''}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="price_${itemIndex}" class="block text-sm font-medium text-gray-700">単価</label>
                        <input type="number" step="1" name="items[${itemIndex}][price]" id="price_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="${itemData.price || ''}">
                    </div>
                    <div class="md:col-span-1">
                        <label for="quantity_${itemIndex}" class="block text-sm font-medium text-gray-700">数量</label>
                        <input type="number" name="items[${itemIndex}][quantity]" id="quantity_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="${itemData.quantity ?? '1'}">
                    </div>
                    <div class="md:col-span-1">
                        <label for="unit_${itemIndex}" class="block text-sm font-medium text-gray-700">単位</label>
                        <input type="text" name="items[${itemIndex}][unit]" id="unit_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="${itemData.unit || ''}">
                    </div>
                    <div class="md:col-span-1">
                        <label for="tax_rate_${itemIndex}" class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                        <input type="number" step="1" name="items[${itemIndex}][tax_rate]" id="tax_rate_${itemIndex}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="${itemData.tax_rate || '10'}">
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

            if (addItemButton) {
                addItemButton.addEventListener('click', () => {
                    const newRow = createItemRow();
                    itemsContainer.appendChild(newRow);
                    attachEventListenersToRow(newRow);
                });
            }

            document.querySelectorAll('.item-row').forEach(row => attachEventListenersToRow(row));
            calculateTotals();
        });
        </script>
    @endpush
</x-app-layout>
