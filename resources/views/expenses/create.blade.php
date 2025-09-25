<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規経費申請') }}
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

                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 申請者情報、申請日、全体プロジェクト --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            {{-- 申請者 --}}
                            <div>
                                <label for="applicant_id" class="block text-sm font-medium text-gray-700">申請者</label>
                                <input type="text" id="applicant_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" value="{{ Auth::user()->name }}" readonly>
                                <input type="hidden" name="applicant_id" value="{{ Auth::id() }}">
                            </div>
                            {{-- 申請日 --}}
                            <div>
                                <label for="application_date" class="block text-sm font-medium text-gray-700">申請日<span class="text-red-500">*</span></label>
                                <input type="date" name="application_date" id="application_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('application_date', $defaultApplicationDate) }}" required>
                            </div>
                            {{-- 全体プロジェクト --}}
                            <div>
                                <label for="project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (全体)</label>
                                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">選択しない</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 経費ステータス (初期値は「申請中」など) --}}
                        <input type="hidden" name="expense_status_id" value="{{ \App\Models\ExpenseStatus::where('name', '申請中')->first()->id ?? 1 }}">


                        <h3 class="font-bold text-xl mb-4 border-b pb-2">経費項目</h3>
                        <div id="items-container">
                            {{-- 既存の項目、または初期の1項目 --}}
                            @if (old('items'))
                                @foreach (old('items') as $index => $item)
                                    <div class="item-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 border border-gray-200 rounded-md relative">
                                        {{-- 費目 --}}
                                        <div>
                                            <label for="items_{{ $index }}_category" class="block text-sm font-medium text-gray-700">費目<span class="text-red-500">*</span></label>
                                            <select name="items[{{ $index }}][category]" id="items_{{ $index }}_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                                <option value="">選択してください</option>
                                                @foreach($expenseCategories as $category)
                                                    <option value="{{ $category }}" {{ old("items.{$index}.category") == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- 金額 --}}
                                        <div>
                                            <label for="items_{{ $index }}_amount" class="block text-sm font-medium text-gray-700">金額<span class="text-red-500">*</span></label>
                                            <input type="number" name="items[{{ $index }}][amount]" id="items_{{ $index }}_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-amount" value="{{ old("items.{$index}.amount") }}" required min="0">
                                        </div>
                                        {{-- 日付 --}}
                                        <div>
                                            <label for="items_{{ $index }}_date" class="block text-sm font-medium text-gray-700">日付<span class="text-red-500">*</span></label>
                                            <input type="date" name="items[{{ $index }}][date]" id="items_{{ $index }}_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old("items.{$index}.date") }}" required>
                                        </div>
                                        {{-- 支払先 --}}
                                        <div>
                                            <label for="items_{{ $index }}_payee" class="block text-sm font-medium text-gray-700">支払先<span class="text-red-500">*</span></label>
                                            <input type="text" name="items[{{ $index }}][payee]" id="items_{{ $index }}_payee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old("items.{$index}.payee") }}" required>
                                        </div>
                                        {{-- 関連プロジェクト (項目別) --}}
                                        <div class="md:col-span-2">
                                            <label for="items_{{ $index }}_project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (項目別)</label>
                                            <select name="items[{{ $index }}][project_id]" id="items_{{ $index }}_project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="">選択しない</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}" {{ old("items.{$index}.project_id") == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- 摘要 --}}
                                        <div class="md:col-span-2">
                                            <label for="items_{{ $index }}_description" class="block text-sm font-medium text-gray-700">摘要</label>
                                            <textarea name="items[{{ $index }}][description]" id="items_{{ $index }}_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("items.{$index}.description") }}</textarea>
                                        </div>
                                        {{-- 添付ファイル --}}
                                        <div class="md:col-span-2">
                                            <label for="items_{{ $index }}_file" class="block text-sm font-medium text-gray-700">添付ファイル (レシートなど)</label>
                                            <input type="file" name="items[{{ $index }}][file]" id="items_{{ $index }}_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            @if (old("items.{$index}.file_path"))
                                                <p class="text-xs text-gray-500 mt-1">既存ファイル: {{ basename(old("items.{$index}.file_path")) }}</p>
                                            @endif
                                        </div>

                                        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                {{-- 初期表示の1項目 --}}
                                <div class="item-row grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 border border-gray-200 rounded-md relative">
                                    {{-- 費目 --}}
                                    <div>
                                        <label for="items_0_category" class="block text-sm font-medium text-gray-700">費目<span class="text-red-500">*</span></label>
                                        <select name="items[0][category]" id="items_0_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                            <option value="">選択してください</option>
                                            @foreach($expenseCategories as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- 金額 --}}
                                    <div>
                                        <label for="items_0_amount" class="block text-sm font-medium text-gray-700">金額<span class="text-red-500">*</span></label>
                                        <input type="number" name="items[0][amount]" id="items_0_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-amount" value="0" required min="0">
                                    </div>
                                    {{-- 日付 --}}
                                    <div>
                                        <label for="items_0_date" class="block text-sm font-medium text-gray-700">日付<span class="text-red-500">*</span></label>
                                        <input type="date" name="items[0][date]" id="items_0_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $defaultApplicationDate }}" required>
                                    </div>
                                    {{-- 支払先 --}}
                                    <div>
                                        <label for="items_0_payee" class="block text-sm font-medium text-gray-700">支払先<span class="text-red-500">*</span></label>
                                        <input type="text" name="items[0][payee]" id="items_0_payee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>
                                    {{-- 関連プロジェクト (部門の代わり) --}}
                                    <div class="md:col-span-2">
                                        <label for="items_0_project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (項目別)</label>
                                        <select name="items[0][project_id]" id="items_0_project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="">選択しない</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- 摘要 --}}
                                    <div class="md:col-span-2">
                                        <label for="items_0_description" class="block text-sm font-medium text-gray-700">摘要</label>
                                        <textarea name="items[0][description]" id="items_0_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                    </div>
                                    {{-- 添付ファイル --}}
                                    <div class="md:col-span-2">
                                        <label for="items_0_file" class="block text-sm font-medium text-gray-700">添付ファイル (レシートなど)</label>
                                        <input type="file" name="items[0][file]" id="items_0_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    </div>

                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="add-item-button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4">
                            + 項目を追加
                        </button>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                {{-- 合計金額の表示を右寄せにするため、div全体を右寄せにするか、inputをflexで囲んでjustify-endを使う --}}
                                <div class="md:col-start-2 flex justify-end"> {{-- md:col-start-2 で2列目から開始し、flex justify-end で右寄せ --}}
                                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mr-4 self-center">合計金額:</label>
                                    {{-- 合計金額の枠を削除し、文字を大きくし、背景色をなくす --}}
                                    <input type="text" name="total_amount" id="total_amount" class="block w-auto border-none shadow-none bg-transparent cursor-not-allowed text-3xl font-bold text-gray-900 text-right" value="{{ old('total_amount', '¥0') }}" readonly>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="overall_reason" class="block text-sm font-medium text-gray-700">申請理由</label>
                                <textarea name="overall_reason" id="overall_reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('overall_reason') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                申請する
                            </button>
                            <a href="{{ route('expenses.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const itemsContainer = document.getElementById('items-container');
                const addItemButton = document.getElementById('add-item-button');
                let itemIndex = {{ old('items') ? count(old('items')) : 1 }}; // 既存の項目数から開始

                function calculateTotalAmount() {
                    let total = 0;
                    document.querySelectorAll('.item-amount').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                    document.getElementById('total_amount').value = '¥' + total.toLocaleString(); // フォーマットして"¥"を追加
                }

                function attachEventListenersToRow(row) {
                    row.querySelector('.item-amount').addEventListener('input', calculateTotalAmount);
                    row.querySelector('.remove-item-row').addEventListener('click', function () {
                        if (itemsContainer.children.length > 1) { // 少なくとも1つは残す
                            row.remove();
                            calculateTotalAmount();
                            updateItemIndexes(); // 削除後にインデックスを再調整
                        } else {
                            // カスタムモーダルUIの例 (Tailwind CSSを使用)
                            const messageBox = document.createElement('div');
                            messageBox.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
                            messageBox.innerHTML = `
                                <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full text-center">
                                    <p class="text-lg font-semibold mb-4">経費項目は最低1つ必要です。</p>
                                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" onclick="this.closest('.fixed').remove()">OK</button>
                                </div>
                            `;
                            document.body.appendChild(messageBox);
                        }
                    });
                }

                function createItemRow() {
                    const newRow = document.createElement('div');
                    newRow.classList.add('item-row', 'grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4', 'mb-6', 'p-4', 'border', 'border-gray-200', 'rounded-md', 'relative');
                    newRow.innerHTML = `
                        {{-- 費目 --}}
                        <div>
                            <label for="items_${itemIndex}_category" class="block text-sm font-medium text-gray-700">費目<span class="text-red-500">*</span></label>
                            <select name="items[${itemIndex}][category]" id="items_${itemIndex}_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">選択してください</option>
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- 金額 --}}
                        <div>
                            <label for="items_${itemIndex}_amount" class="block text-sm font-medium text-gray-700">金額<span class="text-red-500">*</span></label>
                            <input type="number" name="items[${itemIndex}][amount]" id="items_${itemIndex}_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-amount" value="0" required min="0">
                        </div>
                        {{-- 日付 --}}
                        <div>
                            <label for="items_${itemIndex}_date" class="block text-sm font-medium text-gray-700">日付<span class="text-red-500">*</span></label>
                            <input type="date" name="items[${itemIndex}][date]" id="items_${itemIndex}_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $defaultApplicationDate }}" required>
                        </div>
                        {{-- 支払先 --}}
                        <div>
                            <label for="items_${itemIndex}_payee" class="block text-sm font-medium text-gray-700">支払先<span class="text-red-500">*</span></label>
                            <input type="text" name="items[${itemIndex}][payee]" id="items_${itemIndex}_payee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        {{-- 関連プロジェクト (項目別) --}}
                        <div class="md:col-span-2">
                            <label for="items_${itemIndex}_project_id" class="block text-sm font-medium text-gray-700">関連プロジェクト (項目別)</label>
                            <select name="items[${itemIndex}][project_id]" id="items_${itemIndex}_project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">選択しない</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- 摘要 --}}
                        <div class="md:col-span-2">
                            <label for="items_${itemIndex}_description" class="block text-sm font-medium text-gray-700">摘要</label>
                            <textarea name="items[${itemIndex}][description]" id="items_${itemIndex}_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        {{-- 添付ファイル --}}
                        <div class="md:col-span-2">
                            <label for="items_${itemIndex}_file" class="block text-sm font-medium text-gray-700">添付ファイル (レシートなど)</label>
                            <input type="file" name="items[${itemIndex}][file]" id="items_${itemIndex}_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-item-row">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    return newRow;
                }

                function updateItemIndexes() {
                    document.querySelectorAll('.item-row').forEach((row, idx) => {
                        row.querySelectorAll('[name^="items["]').forEach(element => {
                            const name = element.getAttribute('name');
                            element.setAttribute('name', name.replace(/items\[\d+\]/, `items[${idx}]`));
                        });
                        row.querySelectorAll('[id^="items_"]').forEach(element => {
                            const id = element.getAttribute('id');
                            element.setAttribute('id', id.replace(/items_\d+_/, `items_${idx}_`));
                        });
                    });
                    itemIndex = document.querySelectorAll('.item-row').length;
                }

                addItemButton.addEventListener('click', () => {
                    const newRow = createItemRow();
                    itemsContainer.appendChild(newRow);
                    attachEventListenersToRow(newRow);
                    itemIndex++; // 新しい項目を追加するたびにインデックスを増やす
                });

                // 初期ロード時に既存の項目にイベントリスナーをアタッチ
                document.querySelectorAll('.item-row').forEach(row => {
                    attachEventListenersToRow(row);
                });

                calculateTotalAmount(); // 初期ロード時の合計金額を計算
            });
        </script>
    @endpush
</x-app-layout>
