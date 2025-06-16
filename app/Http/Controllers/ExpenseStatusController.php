<?php

namespace App\Http\Controllers;

use App\Models\ExpenseStatus; // 追加: ExpenseStatus モデルを使用するために追記
use Illuminate\Http\Request;

class ExpenseStatusController extends Controller
{
    /**
     * 経費ステータス一覧を表示する
     */
    public function index()
    {
        // データベースから経費ステータスデータを取得します
        $expenseStatuses = ExpenseStatus::all();

        // 取得したデータをビューに渡して表示
        return view('expense_statuses.index', compact('expenseStatuses'));
    }
}