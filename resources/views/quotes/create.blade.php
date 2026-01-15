<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('新規見積書作成') }}
        </h2>
    </x-slot>

    <style>
        /* Chrome / Edge / Safari 用 */
        .no-spin::-webkit-inner-spin-button,
        .no-spin::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox 用 */
        .no-spin[type=number] {
        -moz-appearance: textfield;
        }

        /* さらに Edge / Chrome の矢印UIを無効化 */
        .no-spin {
        appearance: textfield;
        }
    </style>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">
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
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                            {{-- 見積番号 --}}
                            <div class="md:col-span-2">
                                <label for="quote_number" class="block text-sm font-medium text-gray-700">見積番号<span class="text-red-500">*</span></label>
                                <input type="text" name="quote_number" id="quote_number" 
                                    class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @error('quote_number') border-red-500 @enderror" 
                                    value="{{ old('quote_number', $defaultQuoteNumber ?? '') }}" 
                                    required>
                                    @error('quote_number')
                                    <p class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 関連イベント --}}
                            <div class="md:col-span-6">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連イベント<span class="text-red-500">*</span></label>
                                <select id="project_id" name="project_id" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @error('name') border-red-500 @enderror" required
                                    data-project-client-map='@json($projectClientMap ?? [])'
                                    data-all-clients-map='@json($allClientsMap ?? [])'>
                                    <option value="">イベントを選択してください</option>
                                    @foreach($projects as $project)
                                        @php
                                            $dateSuffix = $project->start_date ? ' (' . \Carbon\Carbon::parse($project->start_date)->format('n/j') . '～)' : '';
                                        @endphp
                                        <option value="{{ $project->id }}" {{ (old('project_id', $selectedProjectId ?? '') == $project->id) ? 'selected' : '' }}>
                                            {{ $project->name . $dateSuffix }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 顧客表示 --}}
                            <div class="md:col-span-4">
                                <label for="client_name_display" class="block text-sm font-medium text-gray-700">顧客</label>
                                <input type="text" id="client_name_display"class="block w-full py-1.5 border-gray-300 bg-gray-100 rounded-md focus:ring-opacity-50 text-sm cursor-not-allowed" readonly placeholder="イベントを選択してください">
                                <input type="hidden" name="client_id" id="client_id_hidden">
                                @error('client_id')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                            {{-- 件名 --}}
                            <div class="md:col-span-8">
                                <label for="subject" class="block text-sm font-medium text-gray-700">件名<span class="text-red-500">*</span></label>
                                <input type="text" id="subject" name="subject" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 有効期限 --}}
                            <div class="md:col-span-2">
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">有効期限</label>
                                <input type="text" name="expiry_date" id="expiry_date" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="{{ old('expiry_date', '3ヶ月') }}">
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 発行日 --}}
                            <div class="md:col-span-2">
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">
                                    発行日<span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        name="issue_date"
                                        id="issue_date"
                                        class="block w-full py-1.5 pl-3 pr-10 border-gray-300 rounded-md
                                            focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                        value="{{ old('issue_date', date('Y-m-d')) }}"
                                        required
                                    >
                                    <!-- カレンダーアイコン -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mb-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('issue_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- 納入予定日・納入場所・お支払条件 --}}
                        <div class="md:col-span-12 grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                            <div class="md:col-span-2">
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700">
                                    納入予定日
                                </label>
                                <div class="relative">
                                    <input type="text" name="delivery_date" id="delivery_date"
                                        class="block w-full py-1.5 pl-3 pr-10 border-gray-300 rounded-md
                                            focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                        value="{{ old('delivery_date') }}"
                                    >
                                    <!-- カレンダーアイコン -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                        </svg>
                                    </div>
                                </div>

                                @error('delivery_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-7">
                                <label for="delivery_location" class="block text-sm font-medium text-gray-700">納入場所</label>
                                <input type="text" name="delivery_location" id="delivery_location"  class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="{{ old('delivery_location') }}" maxlength="255">
                                @error('delivery_location')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700">お支払条件</label>
                                <input type="text" name="payment_terms" id="payment_terms" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="{{ old('payment_terms', '銀行振込') }}" maxlength="255">
                                @error('payment_terms')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- 備考 --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                            <div class="md:col-span-12">
                                <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea name="notes" id="notes" rows="3" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- 明細 --}}
                        <h4 class="font-bold text-lg ml-1 text-sm">明細</h4>
                        <div id="items-container" class="space-y-4 border p-2 rounded-md">
                            @php
                                $items = old('items', [[]]);
                                if (empty($items)) { $items = [[]]; }
                            @endphp

                            @foreach($items as $index => $item)
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end relative">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="">
                                    <div class="md:col-span-5">
                                        <label for="item_name_{{ $index }}" class="block text-sm font-medium text-gray-700">品名<span class="text-red-500">*</span></label>
                                        <input type="text" name="items[{{ $index }}][item_name]" id="item_name_{{ $index }}" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm @error('name') border-red-500 @enderror" value="{{ $item['item_name'] ?? '' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="price_{{ $index }}" class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1"
                                            name="items[{{ $index }}][price]"
                                            id="price_{{ $index }}"
                                            class="text-right block w-full py-1.5 border-gray-300 rounded-md
                                                focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm no-spin item-price"
                                            value="{{ $item['price'] ?? '' }}">
                                    </div>

                                    <div class="md:col-span-1">
                                        <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="quantity_{{ $index }}"
                                            class="text-right block w-full sm:pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-quantity"
                                            value="{{ $item['quantity'] ?? '1' }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="unit_{{ $index }}" class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" id="unit_{{ $index }}" class="block w-full pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm unit-no-spin" value="{{ $item['unit'] ?? '' }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="tax_rate_{{ $index }}" class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                                        <input type="number" step="1" name="items[{{ $index }}][tax_rate]" id="tax_rate_{{ $index }}"
                                            class="text-right block w-full sm:pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-tax-rate"
                                            value="{{ $item['tax_rate'] ?? '10' }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="subtotal_{{ $index }}" class="block text-sm font-medium text-gray-700">小計</label>
                                        <input type="text" id="subtotal_{{ $index }}"
                                            class="block w-full py-1.5 border-gray-300 rounded-md bg-gray-100 text-sm cursor-not-allowed item-subtotal"
                                            value="0" readonly>
                                    </div>
                                    <button type="button" class="absolute top-6 right-1 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- 行追加ボタンと合計 --}}
                        <div class="mx-2 my-2">
                            <button type="button" id="add-item-button" class="bg-indigo-500 hover:bg-indigo-700 text-white pt-2 pb-1.5 pr-2 pl-1 text-xs rounded-md mb-2 ml-auto block">＋行</button>
                            <div class="flex justify-end items-center mb-3 mr-1">
                                <p class="text-xl font-bold text-gray-800">合計金額: <span id="display-total-amount">¥0</span></p>
                                <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                            </div>
                            <div class="flex justify-end space-x-2 text-xs">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white pt-2 pb-1.5 pr-2 pl-2 rounded-md">作成</button>
                                <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white pt-2 pb-1.5 pr-2 pl-2 rounded-md">キャンセル</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>

    {{-- ページ用スクリプト --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // イベント選択 -> 顧客自動表示
            const projectSelect = document.getElementById('project_id');
            const clientNameDisplay = document.getElementById('client_name_display');
            const clientIdHidden = document.getElementById('client_id_hidden');
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
                const initialProjectId = "{{ old('project_id', request('project_id', $selectedProjectId ?? '')) }}";
                if (initialProjectId) projectSelect.value = initialProjectId;
                updateClientField();
            }

            // Flatpickr 設定
            if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.ja) flatpickr.localize(flatpickr.l10ns.ja);
            flatpickr("#issue_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('issue_date', date('Y-m-d')) }}" });
            flatpickr("#delivery_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('delivery_date', '') }}" });

            // 明細行の処理
            const itemsContainer = document.getElementById('items-container');
            const addItemButton = document.getElementById('add-item-button');
            let itemIndex = {{ old('items') ? count(old('items')) : (isset($quote) && !$quote->items->isEmpty() ? count($quote->items) : 0) }};

            function calculateRowTotal(row) {
                const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
                const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
                const taxRate = parseFloat(row.querySelector('.item-tax-rate')?.value) || 0;
                const subtotalInput = row.querySelector('.item-subtotal');
                const subtotal = Math.round(price * quantity * (1 + taxRate / 100));
                if (subtotalInput) subtotalInput.value = subtotal.toLocaleString();
                calculateTotals();
            }

            function calculateTotals() {
                let grandTotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
                    const quantity = parseInt(row.querySelector('.item-quantity')?.value) || 0;
                    const taxRate = parseFloat(row.querySelector('.item-tax-rate')?.value) || 0;
                    grandTotal += Math.round(price * quantity * (1 + taxRate / 100));
                });
                const disp = document.getElementById('display-total-amount');
                const hiddenTotal = document.getElementById('total_amount_input');
                if (disp) disp.textContent = '¥' + grandTotal.toLocaleString();
                if (hiddenTotal) hiddenTotal.value = grandTotal;
            }

            function attachEventListenersToRow(row) {
                row.querySelector('.item-price')?.addEventListener('input', () => calculateRowTotal(row));
                row.querySelector('.item-quantity')?.addEventListener('input', () => calculateRowTotal(row));
                row.querySelector('.item-tax-rate')?.addEventListener('input', () => calculateRowTotal(row));
                row.querySelector('.remove-item-row')?.addEventListener('click', () => { row.remove(); calculateTotals(); });
                calculateRowTotal(row);
            }

            function createItemRow(itemData = {}) {
                const newRow = document.createElement('div');
                newRow.className = 'item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end relative';
                newRow.innerHTML = `
                    <input type="hidden" name="items[${itemIndex}][id]" value="">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700">品名<span class="text-red-500">*</span></label>
                        <input type="text" name="items[${itemIndex}][item_name]" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="${itemData.item_name || ''}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">単価</label>
                        <input type="number" step="1" name="items[${itemIndex}][price]" class="text-right block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm no-spin item-price" value="${itemData.price || ''}">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">数量</label>
                        <input type="number" name="items[${itemIndex}][quantity]" class="text-right block w-full pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-quantity" value="${itemData.quantity || 1}">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">単位</label>
                        <input type="text" name="items[${itemIndex}][unit]" class="block w-full pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm unit-no-spin" value="${itemData.unit || ''}">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                        <input type="number" step="1" name="items[${itemIndex}][tax_rate]" class="text-right block w-full pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-tax-rate" value="${itemData.tax_rate ?? 10}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">小計</label>
                        <input type="text" class="block w-full py-1.5 border-gray-300 rounded-md bg-gray-100 text-sm cursor-not-allowed item-subtotal" value="0" readonly>
                    </div>
                    <button type="button" class="absolute top-6 right-1 text-red-500 hover:text-red-700 remove-item-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                itemsContainer.appendChild(newRow);
                attachEventListenersToRow(newRow);
                itemIndex++;
            }

            addItemButton.addEventListener('click', () => createItemRow());

            // 既存行にもイベントを付与
            document.querySelectorAll('.item-row').forEach(row => attachEventListenersToRow(row));
        });
    </script>
</x-app-layout>