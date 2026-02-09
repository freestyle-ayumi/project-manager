<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            場所管理
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 text-gray-900">
                <!-- 新規追加ボタン -->
                <div class="mb-2 text-right">
                    <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center pt-2 pb-1.5 pr-2 pl-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ＋新規場所
                    </a>
                </div>

                <!-- 一覧テーブル -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-left text-gray-600 text-xs font-medium">
                            <tr>
                                <th class="px-2 py-1 uppercase tracking-wider">場所</th>
                                <th class="px-2 py-1 uppercase tracking-wider">緯度</th>
                                <th class="px-2 py-1 uppercase tracking-wider">経度</th>
                                <th class="px-2 py-1 uppercase tracking-wider">許容距離 (m)</th>
                                <th class="px-2 py-1 uppercase tracking-wider">操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-500 text-xs">
                            @forelse($locations as $location)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 py-1 whitespace-nowra">
                                        {{ $location->name }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        {{ number_format($location->latitude, 6) }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        {{ number_format($location->longitude, 6) }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        {{ $location->allowed_radius }}<span class="text-slate-400"> m</span>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap flex items-center gap-x-0.5">
                                        <a href="{{ route('admin.locations.edit', $location) }}" class="text-emerald-600 hover:text-emerald-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-400 mt-1" title="削除" onclick="return confirm('本当に削除しますか？')">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        登録場所がありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- ページネーション -->
                <div>
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>