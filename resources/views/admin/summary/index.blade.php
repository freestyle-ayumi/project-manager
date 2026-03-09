<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('勤務集計') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.attendance.log') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded text-xs">
                    勤怠log
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 text-xs text-gray-500">
            <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                <form method="GET" class="mb-4 flex gap-2 items-center">
                    <div>
                        <input type="month" name="month" value="{{ $month }}" class="py-1.5 pr-2 pl-3 block rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">表示</button>
                </form>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs text-left font-medium">
                        <tr>
                            <th class="w-[25%] px-2 py-2 uppercase">氏名</th>
                            <th class="w-[25%] px-2 py-2 uppercase">出勤日数</th>
                            <th class="w-[25%] px-2 py-2 uppercase">合計勤務時間</th>
                            <th class="w-[25%] px-2 py-2 uppercase text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-gray-600">
                        @foreach($summaryData as $data)
                        <tr>
                            <td class="px-2 py-2 whitespace-nowrap">
                                {{ $data['name'] }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $data['days_worked'] }} 日</td>
                            <td class="px-2 py-2 whitespace-nowrap font-bold text-indigo-500">{{ $data['total_hours'] }}</td>
                            <td class="px-2 py-2 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-1">
                                    {{-- ① 表示 (詳細ページへ) --}}
                                    <a href="{{ route('admin.summary.show', [$data['id'], 'month' => $month]) }}" 
                                        class="bg-blue-50 text-blue-600 px-2 py-1 rounded border border-blue-200 hover:bg-blue-100" title="詳細表示">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M5 3a2 2 0 0 0-2 2v4.129a1.5 1.5 0 0 0-.861 1.665l1.72 8.598A2 2 0 0 0 5.819 21H18.18a2 2 0 0 0 1.961-1.608l1.72-8.598A1.5 1.5 0 0 0 21 9.13V7.5a2 2 0 0 0-2-2h-6.52l-1.399-1.75A2 2 0 0 0 9.52 3zm14.78 8H4.22l1.6 8h12.36zM5 9h14V7.5h-6.52a2 2 0 0 1-1.561-.75L9.519 5H5z"/></g></svg>
                                    </a>

                                    {{-- ② 修正 (ユーザー編集など) --}}
                                    <a href="#" class="bg-emerald-50 text-emerald-600 px-2 py-1 rounded border border-emerald-200 hover:bg-green-100" title="修正">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"><g fill="none"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M13.896 3.03a2 2 0 0 1 2.829 0l4.242 4.242a2 2 0 0 1 0 2.83L10.653 20.415a2 2 0 0 1-1.414.586H3.996a1 1 0 0 1-1-1v-5.243a2 2 0 0 1 .586-1.414zM17 17a1 1 0 0 1 .946.677c.06.177.2.316.377.377a1 1 0 0 1 0 1.892a.6.6 0 0 0-.377.377a1 1 0 0 1-1.892 0a.6.6 0 0 0-.377-.377a1 1 0 0 1 0-1.892c.177-.06.316-.2.377-.377l.062-.146A1 1 0 0 1 17 17M13.584 6.17l4.243 4.243l1.726-1.726l-4.243-4.243zM5 0a1 1 0 0 1 .946.677l.13.378c.3.879.99 1.57 1.87 1.87l.377.129a1 1 0 0 1 0 1.892l-.378.13c-.879.3-1.57.99-1.87 1.87l-.129.377a1 1 0 0 1-1.892 0l-.13-.378a3 3 0 0 0-1.87-1.87l-.377-.129a1 1 0 0 1 0-1.892l.378-.13c.879-.3 1.57-.99 1.87-1.87l.129-.377C4.222.285 4.552 0 5 0m0 3.196A5 5 0 0 1 4.196 4q.449.355.804.803q.356-.447.803-.803A5 5 0 0 1 5 3.196m-.004 15.805H9.24l7.174-7.174l-4.243-4.243l-7.174 7.174z"/></g></svg>
                                    </a>

                                    {{-- ③ ダウンロード (未実装) --}}
                                    <button class="bg-slate-50 text-slate-600 px-2 py-1 rounded border border-slate-200 opacity-50 cursor-not-allowed" disabled title="準備中">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M9.52 3a2 2 0 0 1 1.561.75l1.4 1.75H20a2 2 0 0 1 2 2V19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm0 2H4v14h16V7.5h-7.52a2 2 0 0 1-1.561-.75zM12 10a1 1 0 0 1 1 1v2.709l.414-.415a1 1 0 1 1 1.414 1.415l-2.12 2.12a1 1 0 0 1-1.415 0l-2.121-2.12a1 1 0 1 1 1.414-1.415l.414.415V11a1 1 0 0 1 1-1"/></g></svg>
                                    </button>

                                    {{-- ④ 削除 (注意が必要な操作なので赤色) --}}
                                    <button class="bg-red-50 text-red-600 px-2 py-1 rounded border border-red-200 hover:bg-red-100" title="削除">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24"><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07L4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z"/></g></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>