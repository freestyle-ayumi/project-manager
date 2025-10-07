<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ユーザー編集
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" style="@media (max-width: 400px) {padding: 0.5rem;}">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700">名前</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="border rounded w-full px-3 py-2">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700">メール</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="border rounded w-full px-3 py-2">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700">ロール</label>
                            <select name="role_id" class="border rounded w-full px-3 py-2">
                                @foreach(\App\Models\Role::all() as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">更新</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
