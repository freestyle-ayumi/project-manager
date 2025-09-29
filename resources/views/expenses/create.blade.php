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
                                <input type="text" name="application_date" id="application_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="{{ old('application_date', $defaultApplicationDate) }}" required>
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

                        <h3 class="font-bold text-xl mb-1">経費項目</h3>
                        <div id="items-container" class="border border-gray-200 rounded-md p-4">
                        {{-- ラベル行（固定） --}}
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-2 text-sm font-medium text-gray-700 pl-2">日付<span class="text-red-500">*</span></div>
                            <div class="col-span-8 text-sm font-medium text-gray-700 pl-2">品目<span class="text-red-500">*</span></div>
                            <div class="col-span-2 text-sm font-medium text-gray-700 pl-2">金額<span class="text-red-500">*</span></div>
                        </div>

                        {{-- 初期項目 --}}
                        <div class="item-row grid grid-cols-12 gap-2 mb-2 relative">
                            <div class="col-span-2">
                                <input type="text" name="items[0][date]" class="block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="{{ $defaultApplicationDate }}" required>
                            </div>
                            <div class="col-span-8">
                                <input type="text" name="items[0][category]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="品目" required>
                            </div>
                            <div class="col-span-2 relative">
                                <input type="number" name="items[0][amount]" class="block w-full rounded-md border-gray-300 shadow-sm item-amount pr-8" value="0" required min="0">
                                <button type="button" class="absolute top-1/2 right-1 -translate-y-1/2 text-red-500 hover:text-red-700 remove-item-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                        <button type="button" id="add-item-button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 mt-4">
                            + 項目を追加
                        </button>

                        </div>



                        <div class="px-4 pb-4 pt-8 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div class="md:col-start-2 flex justify-end">
                                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mr-4 self-center">合計金額:</label>
                                    <input type="text" name="total_amount" id="total_amount" class="block w-auto border-none shadow-none bg-transparent cursor-not-allowed text-3xl font-bold text-gray-900 text-right" value="{{ old('total_amount', '¥0') }}" readonly>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    申請する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- flatpickr 用スクリプト --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.flatpickr', { dateFormat: "Y/m/d" });

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.item-amount').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                document.getElementById('total_amount').value = '¥' + total.toLocaleString();
            }

            document.querySelectorAll('.item-amount').forEach(input => input.addEventListener('input', updateTotal));
            updateTotal();

            let itemIndex = {{ old('items') ? count(old('items')) : 1 }};
            const addItemButton = document.getElementById('add-item-button');
            const itemsContainer = document.getElementById('items-container');

            addItemButton.addEventListener('click', function () {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedToday = `${yyyy}/${mm}/${dd}`;

            const template = `
            <div class="item-row grid grid-cols-12 gap-2 mb-2 relative">
                <div class="col-span-2">
                    <input type="text" name="items[${itemIndex}][date]" class="block w-full rounded-md border-gray-300 shadow-sm flatpickr" value="${formattedToday}" required>
                </div>
                <div class="col-span-8">
                    <input type="text" name="items[${itemIndex}][category]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="品目" required>
                </div>
                <div class="col-span-2 relative">
                    <input type="number" name="items[${itemIndex}][amount]" class="block w-full rounded-md border-gray-300 shadow-sm item-amount pr-8" value="0" required min="0">
                    <button type="button" class="absolute top-1/2 right-1 -translate-y-1/2 text-red-500 hover:text-red-700 remove-item-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>`;

            itemsContainer.insertAdjacentHTML('beforeend', template);
            flatpickr(itemsContainer.lastElementChild.querySelector('.flatpickr'), { dateFormat: "Y/m/d" });
            itemsContainer.lastElementChild.querySelector('.item-amount').addEventListener('input', updateTotal);
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
