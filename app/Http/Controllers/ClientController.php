<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // 一覧
    public function index()
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        return view('clients.index', compact('clients'));
    }

    // 新規登録フォーム
    // 新規作成フォーム表示
public function create()
{
    return view('clients.create');
}

// フォーム送信処理
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'contact_person_name' => 'nullable|string|max:255',
    ]);

    Client::create($validated);

    return redirect()->route('clients.index')->with('success', '顧客を登録しました。');
}

    // 編集フォーム
    public function edit(Client $client)
    {
        // 顧客データをビューに渡す
        return view('clients.edit', compact('client'));
    }

    // 更新
    public function update(Request $request, Client $client)
    {
        // バリデーション
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:clients,email,' . $client->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'notes'   => 'nullable|string',
        ]);

        // 更新
        $client->update($validated);

        // リダイレクトとメッセージ
        return redirect()->route('clients.index')->with('success', '顧客情報を更新しました。');
    }


    // 削除
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', '顧客を削除しました。');
    }
}
