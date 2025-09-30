<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * ロール一覧を表示する
     */
    public function index()
    {
        // ページネーションで10件ずつ取得
        $roles = Role::orderBy('id')->paginate(10);

        // 取得したデータをビューに渡して表示
        return view('roles.index', compact('roles'));
    }
    
    public function create()
    {
        return view('roles.create'); // roles/create.blade.php を表示
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
        ]);

        return redirect()->route('roles.index')->with('success', 'ロールを追加しました。');
    }

}
