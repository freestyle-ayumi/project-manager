<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規経費申請') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 申請者・日付・イベント --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="application_date" class="block text-sm font-medium text-gray-700">申請日<span class="text-red-500">*</span></label>
                                <input type="text" name="application_date" id="application_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="{{ old('application_date', $defaultApplicationDate) }}" required>
                            </div>
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連イベント<span class="text-red-500">*</span></label>
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="applicant_id" class="block text-sm font-medium text-gray-700">申請者</label>
                                <input type="text" id="applicant_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" value="{{ Auth::user()->name }}" readonly>
                                <input type="hidden" name="applicant_id" value="{{ Auth::id() }}">
                            </div>
                        </div>

                        <input type="hidden" name="expense_status_id" value="{{ \App\Models\ExpenseStatus::where('name', '申請中')->first()->id ?? 1 }}">

                        <h3 class="font-bold text-xl mb-1">経費項目</h3>
                        <div id="items-container" class="border border-gray-200 rounded-md p-4">
                            {{-- ラベル行 --}}
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-2 text-sm font-medium text-gray-700 pl-2">日付<span class="text-red-500">*</span></div>
                                <div class="col-span-4 text-sm font-medium text-gray-700 pl-2">品名<span class="text-red-500">*</span></div>
                                <div class="col-span-1 text-sm font-medium text-gray-700 pl-2">単価<span class="text-red-500">*</span></div>
                                <div class="col-span-1 text-sm font-medium text-gray-700 pl-2">数量</div>
                                <div class="col-span-1 text-sm font-medium text-gray-700 pl-2">単位</div>
                                <div class="col-span-1 text-sm font-medium text-gray-700 pl-2">税率 (%)<span class="text-red-500">*</span></div>
                                <div class="col-span-2 text-sm font-medium text-gray-700 pl-2">小計</div>
                            </div>

                            {{-- 初期項目 --}}
                            <div class="item-row grid grid-cols-12 gap-2 mb-2 relative">
                                <div class="col-span-2">
                                    <input type="text" name="items[0][date]" class="block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="{{ $defaultApplicationDate }}" required>
                                </div>
                                <div class="col-span-4">
                                    <input type="text" name="items[0][item_name]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="品名" required>
                                </div>
                                <div class="col-span-1">
                                    <input type="number" name="items[0][price]" class="block w-full rounded-md border-gray-300 shadow-sm item-price" value="0" min="0" required>
                                </div>
                                <div class="col-span-1">
                                    <input type="number" name="items[0][quantity]" class="block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="1" min="1">
                                </div>
                                <div class="col-span-1">
                                    <input type="text" name="items[0][unit]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="個">
                                </div>
                                <div class="col-span-1">
                                    <input type="number" name="items[0][tax_rate]" class="block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="10" min="0" required>
                                </div>
                                <div class="col-span-2">
                                    <input type="text" name="items[0][subtotal]" class="block w-full rounded-md border-gray-300 shadow-sm item-subtotal bg-gray-100 cursor-not-allowed" value="0" readonly>
                                </div>
                                {{-- 削除ボタンを col-span 外にして絶対配置 --}}
                                <button type="button" class="absolute top-1/2 right-1 -translate-y-1/2 text-red-500 hover:text-red-700 remove-item-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-span-2 relative flex justify-end">
                            <button type="button" id="add-item-button" class="flex justify-end items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 mt-4">
                                + 項目を追加
                            </button>
                        </div>

                        <div class="px-4 pb-4 pt-8 border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div class="md:col-start-2 flex justify-end">
                                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mr-4 self-center">合計金額:</label>
                                    <input type="text" id="total_amount" class="block w-auto border-none shadow-none bg-transparent cursor-not-allowed text-3xl font-bold text-gray-900 text-right" value="¥0" readonly>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    申請する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.flatpickr', { dateFormat: "Y/m/d" });

            function updateSubtotal(row) {
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const taxRate = parseFloat(row.querySelector('.item-tax-rate').value) || 0;
                const subtotal = price * quantity * (1 + taxRate / 100);
                row.querySelector('.item-subtotal').value = subtotal.toLocaleString();
            }

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.item-subtotal').forEach(sub => {
                    total += parseFloat(sub.value.replace(/,/g,'')) || 0;
                });
                document.getElementById('total_amount').value = '¥' + total.toLocaleString();
            }

            const itemsContainer = document.getElementById('items-container');
            itemsContainer.querySelectorAll('.item-row').forEach(row => {
                ['input.item-price','input.item-quantity','input.item-tax-rate'].forEach(sel => {
                    row.querySelector(sel).addEventListener('input', () => {
                        updateSubtotal(row);
                        updateTotal();
                    });
                });
                updateSubtotal(row);
            });
            updateTotal();

            let itemIndex = {{ old('items') ? count(old('items')) : 1 }};
            const addItemButton = document.getElementById('add-item-button');

            addItemButton.addEventListener('click', function () {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                const formattedToday = `${yyyy}/${mm}/${dd}`;

                const template = document.createElement('div');
                template.className = 'item-row grid grid-cols-12 gap-2 mb-2 relative';
                template.innerHTML = `
                    <div class="col-span-2">
                        <input type="text" name="items[${itemIndex}][date]" class="block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="${formattedToday}" required>
                    </div>
                    <div class="col-span-4">
                        <input type="text" name="items[${itemIndex}][item_name]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="品名" required>
                    </div>
                    <div class="col-span-1">
                        <input type="number" name="items[${itemIndex}][price]" class="block w-full rounded-md border-gray-300 shadow-sm item-price" value="0" min="0" required>
                    </div>
                    <div class="col-span-1">
                        <input type="number" name="items[${itemIndex}][quantity]" class="block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="1" min="1">
                    </div>
                    <div class="col-span-1">
                        <input type="text" name="items[${itemIndex}][unit]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="個">
                    </div>
                    <div class="col-span-1">
                        <input type="number" name="items[${itemIndex}][tax_rate]" class="block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="10" min="0" required>
                    </div>
                    <div class="col-span-2">
                        <input type="text" name="items[${itemIndex}][subtotal]" class="block w-full rounded-md border-gray-300 shadow-sm item-subtotal bg-gray-100 cursor-not-allowed" value="0" readonly>
                    </div>
                    {{-- 削除ボタンを col-span 外にして絶対配置 --}}
                    <button type="button" class="absolute top-1/2 right-1 -translate-y-1/2 text-red-500 hover:text-red-700 remove-item-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    `;
                itemsContainer.appendChild(template);

                flatpickr(template.querySelector('.flatpickr'), { dateFormat: "Y/m/d" });

                ['.item-price', '.item-quantity', '.item-tax-rate'].forEach(sel => {
                    template.querySelector(sel).addEventListener('input', () => {
                        updateSubtotal(template);
                        updateTotal();
                    });
                });

                itemIndex++;
            });

            itemsContainer.addEventListener('click', function(e){
                if(e.target.closest('.remove-item-row')){
                    e.target.closest('.item-row').remove();
                    updateTotal();
                }
            });
        });
    </script>
</x-app-layout>
