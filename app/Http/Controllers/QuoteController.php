<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // DomPDFファサードをインポート

class QuoteController extends Controller
{
    /**
     * 見積書一覧を表示する
     */
    public function index(Request $request)
    {
        // Eloquentクエリを開始し、必要なリレーションをEagerロード
        $quotes = Quote::with(['project', 'client', 'user']);

        // キーワード検索
        if ($request->filled('search')) {
            $search = $request->input('search');
            $quotes->where(function ($query) use ($search) {
                $query->where('quote_number', 'like', '%' . $search . '%')
                      ->orWhere('subject', 'like', '%' . $search . '%')
                      ->orWhereHas('client', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('project', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            });
        }

        // プロジェクトタイプフィルター
        $projectFilter = $request->input('project_filter', 'current'); // デフォルトは 'current'

        if ($projectFilter === 'current') {
            $today = Carbon::today();
            // 開催中とこれから開催するプロジェクト (終了日が今日以降)
            $quotes->whereHas('project', function ($q) use ($today) {
                $q->where('end_date', '>=', $today);
            });
        } elseif ($projectFilter === 'past') {
            $today = Carbon::today();
            // 過去のプロジェクト (終了日が今日より前)
            $quotes->whereHas('project', function ($q) use ($today) {
                $q->where('end_date', '<', $today);
            });
        }
        // 'all' の場合は、プロジェクトに関するフィルタリングは行わない

        // ページネーションを適用
        $quotes = $quotes->orderBy('issue_date', 'desc')->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    /**
     * 新規見積書作成フォームを表示する
     */
    public function create()
    {
        $projects = Project::with('client')->get();
        $clients = Client::all();

        $projectClientMap = $projects->mapWithKeys(function ($project) {
            return [$project->id => $project->client_id];
        })->toArray();

        $allClientsMap = $clients->pluck('name', 'id')->toArray();

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
            'client_id' => 'nullable|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|string|max:255',
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

        DB::beginTransaction();

        try {
            $quote = new Quote();
            $quote->fill($validated); // fillメソッドでバリデート済みのデータを一括設定
            $quote->user_id = Auth::id();
            $quote->status = '登録済み';
            $quote->total_amount = 0; // 仮の初期値
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
                    'subtotal' => round($subtotal, 0),
                    'tax' => round($taxAmount, 0),
                ]);
                $totalAmount += (round($subtotal, 0) + round($taxAmount, 0));
            }

            $quote->total_amount = round($totalAmount, 0);
            $quote->save();

            DB::commit();

            return redirect()->route('quotes.index')
                             ->with('success', '見積書が正常に登録されました。');

        } catch (\Exception $e) {
            DB::rollBack();
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
            'client_id' => 'nullable|exists:clients,id',
            'quote_number' => 'required|string|max:255|unique:quotes,quote_number,' . $quote->id,
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|string|max:255',
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
            $quote->fill($validated); // fillメソッドでバリデート済みのデータを一括設定
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

    /**
     * 見積書をPDFとして生成してダウンロードする
     */
    public function generatePdf(Quote $quote)
    {
        // 見積書とその関連データをロード
        $quote->load('project', 'client', 'user', 'items');

        // ビューからPDFを生成
        $pdf = Pdf::loadView('quotes.show_pdf', compact('quote'))->setOptions(['defaultFont' => 'ipaexgothic']);

        // ファイル名を生成
        $filename = '見積書_' . $quote->quote_number . '.pdf';

        // PDFをダウンロード
        return $pdf->download($filename);
    }
}
