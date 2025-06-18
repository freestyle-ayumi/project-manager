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
            'valid_until' => 'required|date|after_or_equal:issue_date',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1', // 明細は必須で配列
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.memo' => 'nullable|string',
        ]);

        // トランザクションを開始
        // DB::beginTransaction(); // トランザクションは不要な場合もありますが、複雑な処理では推奨

        try {
            // 見積書本体を作成
            $quote = new Quote();
            $quote->project_id = $validated['project_id'];
            $quote->client_id = $validated['client_id'];
            $quote->user_id = Auth::id(); // ログインユーザーIDを設定
            $quote->quote_number = $validated['quote_number'];
            $quote->issue_date = $validated['issue_date'];
            $quote->valid_until = $validated['valid_until'];
            $quote->subject = $validated['subject'];
            $quote->notes = $validated['notes'];
            $quote->total_amount = 0; // 仮の初期値
            $quote->save();

            $totalAmount = 0;

            // 見積明細を保存
            foreach ($validated['items'] as $itemData) {
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
                    'subtotal' => round($subtotal, 2), // 小数点以下2桁に丸める
                    'tax' => round($taxAmount, 2),     // 小数点以下2桁に丸める
                    'memo' => $itemData['memo'],
                ]);
                $totalAmount += ($subtotal + $taxAmount);
            }

            // 合計金額を更新
            $quote->total_amount = round($totalAmount, 2);
            $quote->save();

            // DB::commit(); // トランザクションをコミット

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に登録されました。');

        } catch (\Exception $e) {
            // DB::rollBack(); // エラーが発生した場合はロールバック
            return back()->withInput()->withErrors(['error' => '見積書の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * 特定の見積書詳細を表示する
     */
    public function show(Quote $quote)
    {
        // 見積書と関連するデータ（プロジェクト、顧客、ユーザー、明細）を読み込む
        $quote->load('project', 'client', 'user', 'items');

        return view('quotes.show', compact('quote'));
    }

    /**
     * 見積書編集フォームを表示する
     */
    public function edit(Quote $quote)
    {
        $quote->load('items'); // 編集フォームに明細も渡すためにロード

        $projects = Project::all();
        $clients = Client::all();

        return view('quotes.edit', compact('quote', 'projects', 'clients'));
    }

    /**
     * 見積書をデータベースで更新する
     */
    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id, // 更新時は自身のIDを除く
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:quote_items,id', // 既存明細のID
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.memo' => 'nullable|string',
        ]);

        // DB::beginTransaction(); // トランザクションは不要な場合もありますが、複雑な処理では推奨

        try {
            // 見積書本体を更新
            $quote->project_id = $validated['project_id'];
            $quote->client_id = $validated['client_id'];
            $quote->user_id = Auth::id(); // 更新者としてログインユーザーIDを設定
            $quote->quote_number = $validated['quote_number'];
            $quote->issue_date = $validated['issue_date'];
            $quote->expiry_date = $validated['expiry_date'];
            $quote->subject = $validated['subject'];
            $quote->notes = $validated['notes'];
            // total_amount は明細の更新後に計算されるため、ここではまだ設定しない
            $quote->save();

            $totalAmount = 0;
            $itemsToKeep = [];

            // 見積明細を更新または新規追加
            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                if (isset($itemData['id']) && $itemData['id']) {
                    // 既存明細の更新
                    $item = QuoteItem::find($itemData['id']);
                    if ($item && $item->quote_id === $quote->id) { // 念のため見積書IDも確認
                        $item->update([
                            'item_name' => $itemData['item_name'],
                            'price' => $price,
                            'quantity' => $quantity,
                            'unit' => $itemData['unit'],
                            'tax_rate' => $taxRate,
                            'subtotal' => round($subtotal, 2),
                            'tax' => round($taxAmount, 2),
                            'memo' => $itemData['memo'],
                        ]);
                        $itemsToKeep[] = $item->id;
                    }
                } else {
                    // 新規明細を追加
                    $newItem = $quote->items()->create([
                        'item_name' => $itemData['item_name'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'unit' => $itemData['unit'],
                        'tax_rate' => $taxRate,
                        'subtotal' => round($subtotal, 2),
                        'tax' => round($taxAmount, 2),
                        'memo' => $itemData['memo'],
                    ]);
                    $itemsToKeep[] = $newItem->id;
                }
                $totalAmount += ($subtotal + $taxAmount);
            }

            // 削除された明細を処理 (リクエストに含まれない既存の明細を削除)
            $existingItemIds = $quote->items->pluck('id')->toArray();
            $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
            if (!empty($itemsToDelete)) {
                QuoteItem::destroy($itemsToDelete);
            }

            // 見積書合計金額を最終的に更新
            $quote->total_amount = round($totalAmount, 2);
            $quote->save();

            // DB::commit(); // トランザクションをコミット

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に更新されました。');

        } catch (\Exception $e) {
            // DB::rollBack(); // エラーが発生した場合はロールバック
            return back()->withInput()->withErrors(['error' => '見積書の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
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