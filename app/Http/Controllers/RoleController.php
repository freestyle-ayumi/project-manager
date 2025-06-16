<?php

namespace App\Http\Controllers;

use App\Models\Role; // 追加: Role モデルを使用するために追記
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * ロール一覧を表示する
     */
    public function index()
    {
        // データベースからロールデータを取得します
        $roles = Role::all();

        // 取得したデータをビューに渡して表示
        return view('roles.index', compact('roles'));
    }
}