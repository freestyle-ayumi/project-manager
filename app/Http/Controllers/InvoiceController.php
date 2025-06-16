<?php

namespace App\Http\Controllers;

use App\Models\Invoice; // 追加: Invoice モデルを使用するために追記
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * 請求書一覧を表示する
     */
    public function index()
    {
        // データベースから請求書データを取得します
        // 関連するプロジェクト、顧客、ユーザー（作成者）の情報も一緒に取得
        $invoices = Invoice::with('project', 'client', 'user')->get();

        // 取得したデータをビューに渡して表示
        return view('invoices.index', compact('invoices'));
    }
}