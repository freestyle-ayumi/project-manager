@php
    $authUser = Auth::user();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            {{ __('ユーザー一覧') }}
        </h2>
    </x-slot>

    <div class="py-2 text-gray-600">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">

                    {{-- 権限チェック --}}
                    @php
                        // 権限設定
                        $allowedRoles = ['master'];
                        
                        // ロール名が master、または developerカラムが 1 の場合に権限あり
                        $canAccess = $authUser && (in_array($authUser->role->name, $allowedRoles) || $authUser->developer == 1);
                    @endphp

                    @if(!$canAccess)
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            このページは管理者のみアクセス可能です。
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4 text-right">
                        <a href="{{ route('roles.index') }}" class="inline-flex items-center pt-2 pb-1.5 px-2 bg-gray-800 text-white rounded text-xs font-semibold hover:bg-gray-700">
                            ロール管理
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs font-medium">
                                <tr>
                                    <th class="px-2 py-2 text-left uppercase tracking-wider">名前</th>
                                    <th class="px-2 py-2 text-left uppercase tracking-wider">メールアドレス</th>
                                    <th class="px-2 py-2 text-left uppercase tracking-wider">
                                        <a href="{{ route('users.index', ['sort_role' => request('sort_role') === 'asc' ? 'desc' : 'asc']) }}" class="hover:underline">
                                            ロール
                                            @if(request('sort_role'))
                                                {{ request('sort_role') === 'asc' ? '↑' : '↓' }}
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-2 py-2 text-left uppercase tracking-wider">登録日時</th>
                                    <th class="px-2 py-2 text-center uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-gray-500 text-xs">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-2 py-2">{{ $user->name }}</td>
                                        <td class="px-2 py-2">{{ $user->email }}</td>
                                        <td class="px-2 py-2">{{ $user->role->description ?? 'N/A' }}</td>
                                        <td class="px-2 py-2">{{ $user->created_at?->format('y/m/d') ?? 'N/A' }}</td>
                                        <td class="py-2 text-right">
                                            <div class="flex justify-center">
                                                <a href="{{ route('users.edit', $user) }}" class="text-emerald-600 hover:text-emerald-400" title="編集">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('このユーザーを削除します。よろしいですか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-400" title="削除">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash ml-2" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ページネーション --}}
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
