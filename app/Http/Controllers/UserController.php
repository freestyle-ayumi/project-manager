<?php

namespace App\Http\Controllers;

use App\Models\User; // 追加: User モデルを使用するために追記
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * ユーザー一覧を表示する
     */
    public function index()
    {
        // データベースからユーザーデータを取得します
        // ロール情報も一緒に取得 (Userモデルにroleリレーションを定義する場合)
        $users = User::with('role')->get();

        // 取得したデータをビューに渡して表示
        return view('users.index', compact('users'));
    }
}