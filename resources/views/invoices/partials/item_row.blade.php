{{--
    このファイルは動的に明細行を追加するためにJavaScriptから利用されます。
    既存のold()データがある場合はそれを表示し、ない場合は初期値を表示します。
    変数:
    - $index: 明細行のインデックス (例: 0, 1, 2...)
    - $item: old()データから渡される明細のデータ配列 (存在しない場合は空配列)
--}}
<div class="item-row grid grid-cols-1 md:grid-cols-7 gap-4 border border-gray-200 p-4 rounded-md mb-4 bg-gray-50 relative">
    <button type="button" class="remove-item-row absolute top-2 right-2 text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
    <div class="md:col-span-2">
        <label for="items_{{ $index }}_item_name" class="block text-sm font-medium text-gray-700">品目 <span class="text-red-500">*</span></label>
        <input type="text" name="items[{{ $index }}][item_name]" id="items_{{ $index }}_item_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $item['item_name'] ?? '' }}" required>
    </div>
    <div>
        <label for="items_{{ $index }}_price" class="block text-sm font-medium text-gray-700">単価 </label>
        <input type="number" step="0.01" name="items[{{ $index }}][price]" id="items_{{ $index }}_price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-price" value="{{ $item['price'] ?? '0' }}" >
    </div>
    <div>
        <label for="items_{{ $index }}_quantity" class="block text-sm font-medium text-gray-700">数量 </label>
        <input type="number" name="items[{{ $index }}][quantity]" id="items_{{ $index }}_quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-quantity" value="{{ $item['quantity'] ?? '1' }}" requred>
    </div>
    <div>
        <label for="items_{{ $index }}_unit" class="block text-sm font-medium text-gray-700">単位</label>
        <input type="text" name="items[{{ $index }}][unit]" id="items_{{ $index }}_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $item['unit'] ?? '' }}">
    </div>
    <div>
        <label for="items_{{ $index }}_tax_rate" class="block text-sm font-medium text-gray-700">税率 (%) </label>
        <input type="number" step="0.01" name="items[{{ $index }}][tax_rate]" id="items_{{ $index }}_tax_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm item-tax-rate" value="{{ $item['tax_rate'] ?? '10' }}" >
    </div>
    <div class="md:col-span-2">
        <label for="items_{{ $index }}_memo" class="block text-sm font-medium text-gray-700">備考</label>
        <textarea name="items[{{ $index }}][memo]" id="items_{{ $index }}_memo" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $item['memo'] ?? '' }}</textarea>
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