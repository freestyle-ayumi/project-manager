<?php

namespace App\Http\Controllers;

use App\Models\User; // 追加: User モデルを使用するために追記
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * ユーザー一覧を表示する
     */
    public function index(Request $request)
    {
        $sortRole = $request->get('sort_role'); // ロールでのソートキー

        $users = User::with('role');

        if ($sortRole) {
            // role_id でソート
            $users = $users->join('roles', 'users.role_id', '=', 'roles.id')
                        ->orderBy('roles.name', $sortRole)
                        ->select('users.*');
        } else {
            $users = $users->orderBy('id', 'asc'); // デフォルトはID順
        }

        $users = $users->paginate(15)->withQueryString(); // ページネーション

        return view('users.index', compact('users', 'sortRole'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'role_id'));

        return redirect()->route('users.index')->with('success', 'ユーザーを更新しました。');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'ユーザーを削除しました。');
}

}