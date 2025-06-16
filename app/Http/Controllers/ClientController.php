<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem; // QuoteItem モデルを使用するために追記
use App\Models\Project;   // Project モデルを使用するために追記
use App\Models\Client;    // Client モデルを使用するために追記
use App\Models\User;     // User モデルを使用するために追記
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 認証ユーザーIDを取得するために追記
use Illuminate\Support\Facades\DB;   // トランザクションのために追記

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
     * 新しい見積書作成フォームを表示する
     */
    public function create()
    {
        $projects = Project::all(); // 関連プロジェクトリスト
        $clients = Client::all();   // 関連顧客リスト
        $users = User::all();      // ユーザーリスト (必要に応じてAuth::user()に限定)

        return view('quotes.create', compact('projects', 'clients', 'users'));
    }

    /**
     * 新しい見積書をデータベースに保存する
     */
    public function store(Request $request)
    {
        // ① バリデーションルール
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1', // 明細が必須で配列であること
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.memo' => 'nullable|string',
        ]);

        // ② トランザクションを開始
        // ヘッダと明細の保存をアトミックに行うため
        DB::beginTransaction();

        try {
            // ③ ヘッダ情報を保存
            // total_amount は明細から計算するため、ここでは初期値0またはnullで設定
            $quote = Quote::create([
                'project_id' => $request->project_id,
                'client_id' => $request->client_id,
                'user_id' => Auth::id(), // 認証ユーザーのIDを設定
                'quote_number' => $request->quote_number,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'total_amount' => 0, // 初期値を設定（後で計算して更新）
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            // ④ 明細情報をループして保存
            foreach ($request->items as $itemData) {
                $price = $itemData['price'];
                $quantity = $itemData['quantity'];
                $taxRate = $itemData['tax_rate'];

                // 小計と税額を計算
                $subtotal = $price * $quantity;
                $tax = $subtotal * ($taxRate / 100);

                QuoteItem::create([
                    'quote_id' => $quote->id, // 作成した見積書ヘッダのIDを使用
                    'item_name' => $itemData['item_name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'],
                    'tax_rate' => $taxRate,
                    'tax' => $tax,
                    'subtotal' => $subtotal,
                    'memo' => $itemData['memo'],
                ]);
                $totalAmount += ($subtotal + $tax); // 合計金額に加算
            }

            // ⑤ ヘッダのtotal_amountを更新
            $quote->total_amount = $totalAmount;
            $quote->save();

            // ⑥ トランザクションをコミット
            DB::commit();

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に作成されました。');

        } catch (\Exception $e) {
            // ⑦ エラーが発生した場合はロールバック
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '見積書の作成中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * 特定の見積書とその明細を表示する
     */
    public function show(Quote $quote)
    {
        // ヘッダ情報とその関連（プロジェクト、顧客、ユーザー）をEager Load
        // 明細も一緒にEager Load
        $quote->load('project', 'client', 'user', 'items');

        return view('quotes.show', compact('quote'));
    }

    /**
     * 見積書編集フォームを表示する
     */
    public function edit(Quote $quote)
    {
        $quote->load('items'); // 既存の明細も読み込む

        $projects = Project::all();
        $clients = Client::all();
        $users = User::all();

        return view('quotes.edit', compact('quote', 'projects', 'clients', 'users'));
    }

    /**
     * 見積書をデータベースで更新する
     */
    public function update(Request $request, Quote $quote)
    {
        // ① バリデーションルール
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'required|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id, // 更新時は自分自身のIDを除外
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1', // 明細が必須で配列であること
            'items.*.id' => 'nullable|exists:quote_items,id', // 既存明細のID (あれば)
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.memo' => 'nullable|string',
        ]);

        // ② トランザクションを開始
        DB::beginTransaction();

        try {
            // ③ ヘッダ情報を更新
            $quote->update([
                'project_id' => $request->project_id,
                'client_id' => $request->client_id,
                'user_id' => Auth::id(), // 更新ユーザーのIDを設定
                'quote_number' => $request->quote_number,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'notes' => $request->notes,
                // total_amount は明細再保存後に再計算
            ]);

            // ④ 明細情報を更新
            // 既存の明細を一旦全て削除し、新しい明細を保存する（シンプルだが、IDが変わる）
            // より高度な方法は、既存のIDをチェックして更新/削除/追加を行う（より複雑）
            $quote->items()->delete(); // 既存の明細をすべて削除

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $price = $itemData['price'];
                $quantity = $itemData['quantity'];
                $taxRate = $itemData['tax_rate'];

                $subtotal = $price * $quantity;
                $tax = $subtotal * ($taxRate / 100);

                $quote->items()->create([ // ヘッダのリレーション経由で作成
                    'item_name' => $itemData['item_name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'],
                    'tax_rate' => $taxRate,
                    'tax' => $tax,
                    'subtotal' => $subtotal,
                    'memo' => $itemData['memo'],
                ]);
                $totalAmount += ($subtotal + $tax);
            }

            // ⑤ ヘッダのtotal_amountを更新
            $quote->total_amount = $totalAmount;
            $quote->save();

            // ⑥ トランザクションをコミット
            DB::commit();

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に更新されました。');

        } catch (\Exception $e) {
            // ⑦ エラーが発生した場合はロールバック
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '見積書の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * 見積書をデータベースから削除する
     */
    public function destroy(Quote $quote)
    {
        // 関連する明細も同時に削除されるように、Quoteモデルでcascade onDeleteを設定している場合
        // （外部キー制約でonDelete('cascade')を設定済み）は、ヘッダを削除するだけでOKです。
        // もし設定していない場合は、先に明細を削除する必要があります。
        // $quote->items()->delete(); // 外部キーのCASCADE設定がない場合
        $quote->delete();

        return redirect()->route('quotes.index')
                         ->with('success', '見積書が正常に削除されました。');
    }
}