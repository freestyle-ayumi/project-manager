<?php

namespace App\Http\Controllers;

use App\Models\Delivery; // 追加: Delivery モデルを使用するために追記
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * 納品物一覧を表示する
     */
    public function index()
    {
        // データベースから納品物データを取得します
        // 関連するプロジェクト、顧客、ユーザー（作成者）の情報も一緒に取得
        $deliveries = Delivery::with('project', 'client', 'user')->get();

        // 取得したデータをビューに渡して表示
        return view('deliveries.index', compact('deliveries'));
    }
}