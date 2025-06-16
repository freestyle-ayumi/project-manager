<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * 見積書一覧を表示する
     */
    public function index()
    {
        // データベースから見積書データを取得します
        // 関連するプロジェクト、顧客、ユーザー（作成者）の情報も一緒に取得
        $quotes = Quote::with('project', 'client', 'user')->get();

        // 取得したデータをビューに渡して表示
        return view('quotes.index', compact('quotes'));
    }

    /**
     * 新規見積書作成フォームを表示する
     */
    public function create()
    {
        // フォームで選択肢として使用するプロジェクトと顧客のデータを取得
        $projects = Project::all();
        $clients = Client::all();

        return view('quotes.create', compact('projects', 'clients'));
    }

    /**
     * 新規見積書をデータベースに保存する
     */
    public function store(Request $request)
    {
        // 見積書本体のバリデーションルール
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number', // 見積番号はユニーク
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0', // 合計金額は必須で数値、0以上
        ]);

        // 見積明細のバリデーションルール
        $request->validate([
            'items' => 'required|array|min:1', // 明細行が最低1つは必要
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100', // 税率は0～100%
            'items.*.memo' => 'nullable|string',
        ]);

        // 合計金額の再計算（フロントエンドの計算はあくまで目安として、サーバーサイドで再計算）
        $calculatedTotalAmount = 0;
        foreach ($request->items as $itemData) {
            $price = (float)$itemData['price'];
            $quantity = (int)$itemData['quantity'];
            $taxRate = (float)$itemData['tax_rate'];

            $subtotal = $price * $quantity;
            $taxAmount = $subtotal * ($taxRate / 100);
            $calculatedTotalAmount += ($subtotal + $taxAmount);
        }

        // 四捨五入などの処理が必要であればここで実装
        $calculatedTotalAmount = round($calculatedTotalAmount);


        // 見積書本体の作成
        $quote = Quote::create([
            'project_id' => $validated['project_id'],
            'client_id' => $validated['client_id'],
            'user_id' => Auth::id(), // ログイン中のユーザーIDを自動設定
            'quote_number' => $validated['quote_number'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'total_amount' => $calculatedTotalAmount, // 再計算した合計金額を使用
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // 見積明細の保存
        foreach ($request->items as $itemData) {
            $price = (float)$itemData['price'];
            $quantity = (int)$itemData['quantity'];
            $taxRate = (float)$itemData['tax_rate'];

            $subtotal = $price * $quantity;
            $taxAmount = $subtotal * ($taxRate / 100);

            $quote->items()->create([
                'item_name' => $itemData['item_name'],
                'price' => $price,
                'quantity' => $quantity,
                'unit' => $itemData['unit'],
                'tax_rate' => $taxRate,
                'subtotal' => round($subtotal), // 小計
                'tax' => round($taxAmount),     // 税額
                'memo' => $itemData['memo'],
            ]);
        }

        return redirect()->route('quotes.index')
                         ->with('success', '見積書が正常に作成されました。');
    }

    /**
     * 特定の見積書詳細を表示する
     */
    public function show(Quote $quote)
    {
        // 関連するプロジェクト、顧客、ユーザー、明細をEager Load
        $quote->load('project', 'client', 'user', 'items');
        return view('quotes.show', compact('quote'));
    }

    /**
     * 特定の見積書編集フォームを表示する
     */
    public function edit(Quote $quote)
    {
        // フォームで選択肢として使用するプロジェクトと顧客のデータを取得
        $projects = Project::all();
        $clients = Client::all();

        // 関連する明細をEager Load
        $quote->load('items');

        return view('quotes.edit', compact('quote', 'projects', 'clients'));
    }

    /**
     * 特定の見積書をデータベースで更新する
     */
    public function update(Request $request, Quote $quote)
    {
        // 見積書本体のバリデーションルール
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id, // 更新時は自分自身を除外
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // 見積明細のバリデーションルール
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:quote_items,id', // 既存明細のID
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.memo' => 'nullable|string',
        ]);

        // 合計金額の再計算（フロントエンドの計算はあくまで目安として、サーバーサイドで再計算）
        $calculatedTotalAmount = 0;
        foreach ($request->items as $itemData) {
            $price = (float)$itemData['price'];
            $quantity = (int)$itemData['quantity'];
            $taxRate = (float)$itemData['tax_rate'];

            $subtotal = $price * $quantity;
            $taxAmount = $subtotal * ($taxRate / 100);
            $calculatedTotalAmount += ($subtotal + $taxAmount);
        }
        $calculatedTotalAmount = round($calculatedTotalAmount);

        // 見積書本体の更新
        $quote->update([
            'project_id' => $validated['project_id'],
            'client_id' => $validated['client_id'],
            'quote_number' => $validated['quote_number'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'total_amount' => $calculatedTotalAmount, // 再計算した合計金額を使用
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // 明細の更新・新規追加・削除
        $existingItemIds = $quote->items->pluck('id')->toArray();
        $itemsToKeep = [];

        foreach ($request->items as $itemData) {
            if (isset($itemData['id']) && $itemData['id']) {
                // 既存の明細を更新
                $item = $quote->items()->find($itemData['id']);
                if ($item) {
                    $price = (float)$itemData['price'];
                    $quantity = (int)$itemData['quantity'];
                    $taxRate = (float)$itemData['tax_rate'];
                    $subtotal = $price * $quantity;
                    $taxAmount = $subtotal * ($taxRate / 100);

                    $item->update([
                        'item_name' => $itemData['item_name'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'unit' => $itemData['unit'],
                        'tax_rate' => $taxRate,
                        'subtotal' => round($subtotal),
                        'tax' => round($taxAmount),
                        'memo' => $itemData['memo'],
                    ]);
                    $itemsToKeep[] = $item->id;
                }
            } else {
                // 新規明細を追加
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                $newItem = $quote->items()->create([
                    'item_name' => $itemData['item_name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'],
                    'tax_rate' => $taxRate,
                    'subtotal' => round($subtotal),
                    'tax' => round($taxAmount),
                    'memo' => $itemData['memo'],
                ]);
                $itemsToKeep[] = $newItem->id;
            }
        }

        // 削除された明細を処理 (リクエストに含まれない既存の明細を削除)
        $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
        if (!empty($itemsToDelete)) {
            QuoteItem::destroy($itemsToDelete);
        }

        return redirect()->route('quotes.index')
                         ->with('success', '見積書が正常に更新されました。');
    }

    /**
     * 特定の見積書をデータベースから削除する
     */
    public function destroy(Quote $quote)
    {
        // 関連する明細も一緒に削除 (hasManyリレーションのcascadeOnDelete設定があれば不要だが、明示的に削除)
        $quote->items()->delete();
        $quote->delete();

        return redirect()->route('quotes.index')
                         ->with('success', '見積書が正常に削除されました。');
    }
}