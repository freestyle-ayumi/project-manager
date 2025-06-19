<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem; // 修正: App->Models を App\Models に変更
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // トランザクションのために追加

class QuoteController extends Controller
{
    /**
     * 見積書一覧を表示する
     */
    public function index()
    {
        $quotes = Quote::with('project', 'client', 'user')->get();
        return view('quotes.index', compact('quotes'));
    }

    /**
     * 新規見積書作成フォームを表示する
     */
    public function create()
    {
        $projects = Project::with('client')->get(); // プロジェクトに関連する顧客もロード
        $clients = Client::all();

        // JavaScriptで使用するために、プロジェクトIDと顧客IDのマップを作成
        $projectClientMap = $projects->mapWithKeys(function ($project) {
            return [$project->id => $project->client_id];
        })->toArray();

        // JavaScriptで使用するために、全顧客のIDと名前のマップを作成
        $allClientsMap = $clients->pluck('name', 'id')->toArray();

        // 見積番号のデフォルト値を自動生成 (例: quoYYMMDDXX)
        // YMD (6桁) + ランダム2桁の数字
        $defaultQuoteNumber = 'quo' . date('ymd') . str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);

        return view('quotes.create', compact('projects', 'clients', 'projectClientMap', 'allClientsMap', 'defaultQuoteNumber'));
    }

    /**
     * 新規見積書をデータベースに保存する
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id', // 顧客は必須ではないためnullable
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|string|max:255', // 必須ではないためnullableに変更
            'delivery_date' => 'nullable|date',
            'delivery_location' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction(); // トランザクションを開始

        try {
            $quote = new Quote();
            $quote->project_id = $validated['project_id'];
            $quote->client_id = $validated['client_id'] ?? null; // null許容
            $quote->user_id = Auth::id();
            $quote->quote_number = $validated['quote_number'];
            $quote->issue_date = $validated['issue_date'];
            $quote->expiry_date = $validated['expiry_date'] ?? null; // null許容
            $quote->delivery_date = $validated['delivery_date'] ?? null;
            $quote->delivery_location = $validated['delivery_location'] ?? ''; // nullの代わりに空文字列を使用
            $quote->payment_terms = $validated['payment_terms'] ?? ''; // nullの代わりに空文字列を使用
            $quote->subject = $validated['subject'];
            $quote->notes = $validated['notes'];
            $quote->total_amount = 0; // 仮の初期値
            $quote->status = '登録済み';
            $quote->save();

            $totalAmount = 0;

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
                    'subtotal' => round($subtotal, 0), // 小数点以下を四捨五入
                    'tax' => round($taxAmount, 0),     // 小数点以下を四捨五入
                ]);
                $totalAmount += (round($subtotal, 0) + round($taxAmount, 0)); // 加算時も四捨五入した値を使用
            }

            $quote->total_amount = round($totalAmount, 0); // 合計も四捨五入
            $quote->save();

            DB::commit(); // トランザクションをコミット

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に登録されました。');

        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生した場合はロールバック
            return back()->withInput()->withErrors(['error' => '見積書の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * 特定の見積書詳細を表示する
     */
    public function show(Quote $quote)
    {
        $quote->load('project', 'client', 'user', 'items');
        return view('quotes.show', compact('quote'));
    }

    /**
     * 見積書編集フォームを表示する
     */
    public function edit(Quote $quote)
    {
        $quote->load('items');

        $projects = Project::with('client')->get();
        $clients = Client::all();

        $projectClientMap = $projects->mapWithKeys(function ($project) {
            return [$project->id => $project->client_id];
        })->toArray();
        
        // JavaScriptで使用するために、全顧客のIDと名前のマップを作成
        $allClientsMap = $clients->pluck('name', 'id')->toArray();

        return view('quotes.edit', compact('quote', 'projects', 'clients', 'projectClientMap', 'allClientsMap'));
    }

    /**
     * 見積書をデータベースで更新する
     */
    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id', // 顧客は必須ではないためnullable
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id,
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|string|max:255', // 必須ではないためnullableに変更
            'delivery_date' => 'nullable|date',
            'delivery_location' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:quote_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $quote->project_id = $validated['project_id'];
            $quote->client_id = $validated['client_id'] ?? null; // null許容
            $quote->user_id = Auth::id();
            $quote->quote_number = $validated['quote_number'];
            $quote->issue_date = $validated['issue_date'];
            $quote->expiry_date = $validated['expiry_date'] ?? null; // null許容
            $quote->delivery_date = $validated['delivery_date'] ?? null;
            $quote->delivery_location = $validated['delivery_location'] ?? ''; // nullの代わりに空文字列を使用
            $quote->payment_terms = $validated['payment_terms'] ?? ''; // nullの代わりに空文字列を使用
            $quote->subject = $validated['subject'];
            $quote->notes = $validated['notes'];
            $quote->save();

            $totalAmount = 0;
            $itemsToKeep = [];

            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                if (isset($itemData['id']) && $itemData['id']) {
                    $item = QuoteItem::find($itemData['id']);
                    if ($item && $item->quote_id === $quote->id) {
                        $item->update([
                            'item_name' => $itemData['item_name'],
                            'price' => $price,
                            'quantity' => $quantity,
                            'unit' => $itemData['unit'],
                            'tax_rate' => $taxRate,
                            'subtotal' => round($subtotal, 0),
                            'tax' => round($taxAmount, 0),
                        ]);
                        $itemsToKeep[] = $item->id;
                    }
                } else {
                    $newItem = $quote->items()->create([
                        'item_name' => $itemData['item_name'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'unit' => $itemData['unit'],
                        'tax_rate' => $taxRate,
                        'subtotal' => round($subtotal, 0),
                        'tax' => round($taxAmount, 0),
                    ]);
                    $itemsToKeep[] = $newItem->id;
                }
                $totalAmount += (round($subtotal, 0) + round($taxAmount, 0));
            }

            $existingItemIds = $quote->items->pluck('id')->toArray();
            $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
            if (!empty($itemsToDelete)) {
                QuoteItem::destroy($itemsToDelete);
            }

            $quote->total_amount = round($totalAmount, 0);
            $quote->save();

            DB::commit();

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に更新されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '見積書の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * 特定の見積書をデータベースから削除する
     */
    public function destroy(Quote $quote)
    {
        $quote->items()->delete();
        $quote->delete();

        return redirect()->route('quotes.index')
                         ->with('success', '見積書が正常に削除されました。');
    }
}
