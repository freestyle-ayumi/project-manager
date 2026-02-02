<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('経費編集') }}
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

                    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- ヘッダ情報 --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-2">
                            {{-- 申請日 --}}
                            <div class="md:col-span-2">
                                <label for="application_date" class="block text-sm font-medium text-gray-700">申請日<span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="application_date" id="application_date"
                                        class="block w-full py-1.5 pl-3 pr-10 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                        value="{{ old('application_date', $expense->date) }}" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-10 4h10m-6 4h6M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('application_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 関連イベント --}}
                            <div class="md:col-span-6">
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連イベント<span class="text-red-500">*</span></label>
                                <select name="project_id" id="project_id" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" required>
                                    <option value="">イベントを選択してください</option>
                                    @foreach($projects as $project)
                                        @php
                                            $dateSuffix = $project->start_date ? ' (' . \Carbon\Carbon::parse($project->start_date)->format('n/j') . '～)' : '';
                                        @endphp
                                        <option value="{{ $project->id }}" {{ old('project_id', $expense->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name . $dateSuffix }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 申請者（編集時は変更不可） --}}
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">申請者</label>
                                <input type="text" class="block w-full py-1.5 border-gray-300 bg-gray-100 rounded-md focus:ring-opacity-50 text-sm cursor-not-allowed" value="{{ $expense->user->name ?? Auth::user()->name }}" readonly>
                                <input type="hidden" name="applicant_id" value="{{ $expense->user_id ?? Auth::id() }}">
                            </div>
                        </div>

                        {{-- ステータス選択（編集時のみ表示） --}}
                        <div class="mb-4">
                            <label for="expense_status_id" class="block text-sm font-medium text-gray-700">ステータス<span class="text-red-500">*</span></label>
                            <select name="expense_status_id" id="expense_status_id" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" required>
                                <option value="">ステータスを選択してください</option>
                                @foreach ($expenseStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('expense_status_id', $expense->expense_status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_status_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 明細（createからほぼコピー） --}}
                        <h4 class="font-bold text-lg ml-1 text-sm">明細</h4>
                        <div id="items-container" class="space-y-4 border p-2 rounded-md">
                            @php
                                // 編集時は既存のitemsを優先、old()があればそれを使う
                                $items = old('items', $expense->items->toArray() ?? [[]]);
                                if (empty($items)) { $items = [[]]; }
                            @endphp

                            @foreach($items as $index => $item)
                                <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end relative">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                                    <div class="md:col-span-5">
                                        <label class="block text-sm font-medium text-gray-700">品名・内容<span class="text-red-500">*</span></label>
                                        <input type="text" name="items[{{ $index }}][item_name]" class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" value="{{ old("items.$index.item_name", $item['item_name'] ?? '') }}" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">単価</label>
                                        <input type="number" step="1" min="0" name="items[{{ $index }}][price]" 
                                            class="text-right block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm no-spin item-price" 
                                            value="{{ old("items.$index.price", $item['price'] ?? '') }}"
                                            oninput="this.value = Math.floor(this.value)" required>  <!-- ← 小数点切り捨て -->
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700">数量</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" class="text-right block w-full sm:pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-quantity" value="{{ old("items.$index.quantity", $item['quantity'] ?? 1) }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700">単位</label>
                                        <input type="text" name="items[{{ $index }}][unit]" class="block w-full pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm unit-no-spin" value="{{ old("items.$index.unit", $item['unit'] ?? '') }}">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700">税率 (%)<span class="text-red-500">*</span></label>
                                        <input type="number" step="1" min="0" max="100" name="items[{{ $index }}][tax_rate]" 
                                            class="text-right block w-full sm:pr-0 py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm item-tax-rate" 
                                            value="{{ old("items.$index.tax_rate", $item['tax_rate'] ?? 10) }}"
                                            oninput="this.value = Math.floor(this.value)" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">小計</label>
                                        <input type="text" class="block w-full py-1.5 border-gray-300 rounded-md bg-gray-100 text-sm cursor-not-allowed item-subtotal" value="{{ number_format($item['subtotal'] ?? 0) }}" readonly>
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
                        <div class="mx-2 my-2 text-xs">
                            <button type="button" id="add-item-button" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold pt-2 pb-1.5 pr-2 pl-1 rounded-md mb-2 ml-auto block">＋行</button>
                            <div class="flex justify-end items-center mb-3 mr-1">
                                <p class="text-xl font-bold text-gray-800">合計金額: <span id="display-total-amount">¥{{ number_format($expense->amount ?? 0) }}</span></p>
                                <input type="hidden" name="total_amount" id="total_amount_input" value="{{ old('total_amount', $expense->amount ?? 0) }}">
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white pt-2 pb-1.5 pr-2 pl-2 rounded-md">更新</button>
                                <a href="{{ route('expenses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white pt-2 pb-1.5 pr-2 pl-2 rounded-md">キャンセル</a>
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

    {{-- JavaScript（createからほぼそのまま移植 + 初期合計計算） --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Flatpickr 設定
            if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.ja) flatpickr.localize(flatpickr.l10ns.ja);
            flatpickr("#application_date", { dateFormat: "Y-m-d", allowInput: true, defaultDate: "{{ old('application_date', $expense->date) }}" });

            // 明細行の処理
            const itemsContainer = document.getElementById('items-container');
            const addItemButton = document.getElementById('add-item-button');
            let itemIndex = {{ count(old('items', $expense->items ?? [])) }};

            function calculateRowTotal(row) {
                if (!row) return;

                // 要素を取得
                const priceEl    = row.querySelector('.item-price');
                const quantityEl = row.querySelector('.item-quantity');
                const taxRateEl  = row.querySelector('.item-tax-rate');
                const subtotalEl = row.querySelector('.item-subtotal');

                // 値を取得（parseIntで整数化）
                const price    = parseInt(priceEl?.value)    || 0;
                const quantity = parseInt(quantityEl?.value) || 0;
                const taxRate  = parseInt(taxRateEl?.value)  || 0;

                const subtotal = Math.round(price * quantity * (1 + taxRate / 100));

                if (subtotalEl) {
                    subtotalEl.value = subtotal.toLocaleString('ja-JP');
                }

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
                calculateRowTotal(row); // 初期計算
            }

            function createItemRow(itemData = {}) {
                const newRow = document.createElement('div');
                newRow.className = 'item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end relative';
                newRow.innerHTML = `
                    <input type="hidden" name="items[${itemIndex}][id]" value="">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700">品名・内容<span class="text-red-500">*</span></label>
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

            // 既存行にイベント付与 + 初期合計計算
            document.querySelectorAll('.item-row').forEach(row => attachEventListenersToRow(row));
            calculateTotals(); // ページ読み込み時に合計を表示
        });
    </script>
</x-app-layout>