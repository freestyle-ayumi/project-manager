<?php

namespace App\Http\Controllers;

use App\Models\Expense; // 追加: Expense モデルを使用するために追記
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * 経費一覧を表示する
     */
    public function index()
    {
        // データベースから経費データを取得します
        // 関連するプロジェクト、ユーザー、経費ステータスの情報も一緒に取得
        $expenses = Expense::with('project', 'user', 'status')->get();

        // 取得したデータをビューに渡して表示
        return view('expenses.index', compact('expenses'));
    }
}