<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Project;
use App\Models\Client;
use App\Models\DeliveryLog; // テーブルがなければ後でコメントアウト
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    // ■ 納品書一覧を表示する
    public function index(Request $request)
    {
        $today = Carbon::today();
        $sixMonthsAgo = $today->copy()->subMonths(6);
        $sixMonthsLater = $today->copy()->addMonths(6);

        // キーワード検索
        $search = $request->input('search');

        // 並び替え
        $sort = $request->input('sort', 'latest');

        // プロジェクトフィルター（all / before / current / past / active）
        $projectFilter = $request->input('project_filter', 'all');

        // ベースクエリ（前後半年のプロジェクトのみ）
        $deliveries = Delivery::with(['project'])
            ->whereHas('project', function ($q) use ($sixMonthsAgo, $sixMonthsLater) {
                $q->whereBetween('start_date', [$sixMonthsAgo, $sixMonthsLater]);
            })
            ->when($search, function ($query, $search) {
                $query->where('deliveries.subject', 'like', "%{$search}%") // title → subject
                    ->orWhereHas('project', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

        // フィルター処理
        if ($projectFilter !== 'all') {
            $deliveries->whereHas('project', function ($q) use ($today, $projectFilter) {
                if ($projectFilter === 'before') {
                    $q->where('start_date', '>', $today);

                } elseif ($projectFilter === 'current') {
                    $q->where('start_date', '<=', $today)
                    ->whereRaw('COALESCE(end_date, start_date) >= ?', [$today]);

                } elseif ($projectFilter === 'past') {
                    $q->whereRaw('COALESCE(end_date, start_date) < ?', [$today]);

                } elseif ($projectFilter === 'active') {
                    $q->where(function ($q2) use ($today) {
                        $q2->where('start_date', '>', $today)
                        ->orWhere(function ($q3) use ($today) {
                            $q3->where('start_date', '<=', $today)
                                ->whereRaw('COALESCE(end_date, start_date) >= ?', [$today]);
                        });
                    });
                }
            });
        }

        // 並び替え
        if ($sort === 'oldest') {
            $deliveries->orderBy('created_at', 'asc');
        } else {
            $deliveries->orderBy('created_at', 'desc');
        }

        // ページネーション
        $deliveries = $deliveries->paginate(20)->appends($request->query());

        return view('deliveries.index', compact('deliveries', 'search', 'sort', 'projectFilter'));
    }

    // 新規納品書作成フォームを表示する
    public function create(Request $request)
    {
        $today = Carbon::today();
        $sixMonthsAgo = $today->copy()->subMonths(6);

        $projects = Project::with('client')
            ->whereDate('start_date', '>=', $sixMonthsAgo) // 半年前以降
            ->orderBy('start_date', 'asc')
            ->get();

        $clients = Client::all();

        $projectClientMap = $projects->mapWithKeys(function ($project) {
            return [$project->id => $project->client_id];
        })->toArray();

        $allClientsMap = $clients->pluck('name', 'id')->toArray();

        $defaultDeliveryNumber = date('ymd') . str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);

        // URL から project_id を取得
        $selectedProjectId = $request->input('project_id');

        // もし project_id が指定されていれば、対応する client_id もセット
        $selectedClientId = null;
        if ($selectedProjectId) {
            $project = Project::with('client')->find($selectedProjectId);
            if ($project) {
                $selectedClientId = $project->client_id;
            }
        }

        return view('deliveries.create', compact(
            'projects',
            'clients',
            'projectClientMap',
            'allClientsMap',
            'defaultDeliveryNumber',
            'selectedProjectId',
            'selectedClientId'
        ));
    }


    // 新規納品書をデータベースに保存する
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                Rule::unique('deliveries', 'project_id'), // 新規作成時はこの形式が正しい
            ],
            'client_id' => 'nullable|exists:clients,id',
            'delivery_number' => 'required|string|max:255|unique:deliveries,delivery_number',
            'delivery_date' => 'required|date', // issue_date 相当
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
            $delivery = new Delivery();
            $delivery->fill($validated);
            $delivery->user_id = Auth::id();
            $delivery->status = '作成済み';
            $delivery->total_amount = 0;
            $delivery->save();

            $totalAmount = 0;

            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                $delivery->items()->create([
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

            $delivery->total_amount = round($totalAmount, 0);
            $delivery->save();

            // ログに保存（DeliveryLog テーブルがあれば有効）
            DeliveryLog::create([
                'delivery_id' => $delivery->id,
                'user_id' => Auth::id(),
                'action' => '作成',
            ]);

            DB::commit();

            return redirect()->route('deliveries.index')
                             ->with('success', '納品書が正常に登録されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '納品書の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    // 特定の納品書詳細を表示する
    public function show(Delivery $delivery)
    {
        $delivery->load(['project', 'client', 'user', 'items', 'logs' => function ($query) {
        $query->orderBy('created_at', 'desc');
        }, 'logs.user']);

        return view('deliveries.show', compact('delivery'));
    }

    // 納品書編集フォームを表示する
    public function edit(Delivery $delivery)
    {
        $delivery->load('items');

        $today = Carbon::today();
        $sixMonthsAgo = $today->copy()->subMonths(6);

        $projects = Project::with('client')
            ->whereDate('start_date', '>=', $sixMonthsAgo) // 半年前以降
            ->orderBy('start_date', 'asc')
            ->get();
        $clients = Client::all();

        $projectClientMap = $projects->mapWithKeys(function ($project) {
            return [$project->id => $project->client_id];
        })->toArray();
        
        $allClientsMap = $clients->pluck('name', 'id')->toArray();

        return view('deliveries.edit', compact('delivery', 'projects', 'clients', 'projectClientMap', 'allClientsMap'));
    }

    // 納品書をデータベースで更新する
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                Rule::unique('deliveries', 'project_id')->ignore($delivery->id), // 重複を許さず、自分だけ許す
            ],
            'client_id' => 'nullable|exists:clients,id',
            'delivery_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('deliveries', 'delivery_number')->ignore($delivery->id), // こちらも同様にignoreが必要
            ],
            'delivery_date' => 'required|date',
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
            $delivery->fill($validated); // fillメソッドでバリデート済みのデータを一括設定
            $delivery->save();

            $totalAmount = 0;
            $itemsToKeep = [];

            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                if (isset($itemData['id']) && $itemData['id']) {
                    $item = DeliveryItem::find($itemData['id']);
                    if ($item && $item->delivery_id === $delivery->id) {
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
                    $newItem = $delivery->items()->create([
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

            $existingItemIds = $delivery->items->pluck('id')->toArray();
            $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
            if (!empty($itemsToDelete)) {
                DeliveryItem::destroy($itemsToDelete);
            }

            $delivery->total_amount = round($totalAmount, 0);
            $delivery->save();

            // ログに保存
            DeliveryLog::create([
                'delivery_id' => $delivery->id,
                'user_id' => Auth::id(),
                'action' => '更新',
            ]);

            DB::commit();

            return redirect()->route('deliveries.index')
                             ->with('success', '納品書が更新されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '納品書の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    // 特定の納品書をデータベースから削除する
    public function destroy(Delivery $delivery)
    {
        DB::beginTransaction();
        try {
            // ログに保存
            DeliveryLog::create([
                'delivery_id' => $delivery->id, // 削除された納品書のID
                'user_id' => Auth::id(),
                'action' => '削除-納品番号: ' . $delivery->delivery_number,
            ]);
            $delivery->items()->delete();
            $delivery->delete();

            DB::commit();

            return redirect()->route('deliveries.index')
                             ->with('success', '納品書が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '納品書の削除中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    public function downloadPdf($id)
    {
        try {
            // 対象の納品データ取得（関連も一括で）
            $delivery = Delivery::with(['client', 'items', 'project'])->findOrFail($id);

            // mPDF 用フォント設定
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'notosansjp',
                'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
                'fontdata' => [
                    'notosansjp' => [
                        'R' => 'NotoSansJP-Regular.ttf',
                        'B' => 'NotoSansJP-Bold.ttf',
                    ],
                ],
            ]);

            // CSS 読み込み（存在する場合のみ）
            $cssPath = public_path('css/pdf.css');
            if (file_exists($cssPath)) {
                $stylesheet = file_get_contents($cssPath);
                $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
            }

            // Blade ビューを HTML にレンダリング
            $html = view('deliveries.show_pdf_mpdf', compact('delivery'))->render();
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            // ファイル名生成（日本語文字は避ける）
            $deliveryNumber = $delivery->delivery_number;
            $issueDate = \Carbon\Carbon::parse($delivery->delivery_date)->format('Ymd');
            $filename = $deliveryNumber . '-' . $issueDate . '.pdf';

            // 保存先パス
            $savePath = 'deliveries/' . $filename;

            // PDF をストレージに保存（public ディスク）
            Storage::disk('public')->put($savePath, $mpdf->Output('', 'S'));

            // 公開 URL を生成
            $fullUrl = asset('storage/' . $savePath);

            // 納品書の pdf_path を更新
            $delivery->pdf_path = $fullUrl;
            $delivery->save();

            // 操作ログを残す
            DeliveryLog::create([
                'delivery_id' => $delivery->id,
                'user_id' => Auth::id(),
                'action' => '<a href="' . $fullUrl . '" target="_blank" class="text-blue-500 hover:underline">PDF出力</a>',
            ]);

            // PDF をダウンロードさせる
            return $mpdf->Output($filename, 'D');

        } catch (\Throwable $e) {
            // 例外を Laravel のログに残す
            \Log::error('PDF生成エラー: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // ユーザーに戻す
            return back()->withErrors(['error' => 'PDF生成中にエラーが発生しました。']);
        }
    }
    // app/Http/Controllers/DeliveryController.php
        public function downloadPdfMpdf(Delivery $delivery)
    {
        try {
            // PDF生成用フォント設定
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'notosansjp',
                'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
                'fontdata' => [
                    'notosansjp' => [
                        'R' => 'NotoSansJP-Regular.ttf',
                        'B' => 'NotoSansJP-Bold.ttf',
                    ],
                ],
            ]);

            // CSS 読み込み
            $cssPath = public_path('css/pdf.css');
            if (file_exists($cssPath)) {
                $mpdf->WriteHTML(file_get_contents($cssPath), \Mpdf\HTMLParserMode::HEADER_CSS);
            }

            // Bladeビューをレンダリング
            $html = view('deliveries.show_pdf_mpdf', compact('delivery'))->render();
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            // ファイル名作成
            $filename = $delivery->delivery_number . '-' . \Carbon\Carbon::parse($delivery->delivery_date)->format('Ymd') . '.pdf';
            $savePath = 'deliveries/' . $filename;

            // 保存
            Storage::disk('public')->put($savePath, $mpdf->Output('', 'S'));

            // 公開 URL
            $fullUrl = asset('storage/' . $savePath);

            // delivery テーブルに PDF パス保存
            $delivery->pdf_path = $fullUrl;
            $delivery->save();

            // ログに保存
            $delivery->logs()->create([
                'user_id' => auth()->id(),
                'action' => '<a href="' . $fullUrl . '" target="_blank">PDF出力</a>',
            ]);

            // PDFをブラウザにダウンロードさせる
            return $mpdf->Output($filename, 'D');

        } catch (\Throwable $e) {
            \Log::error('PDF生成エラー: ' . $e->getMessage());
            return back()->withErrors(['error' => 'PDF生成中にエラーが発生しました。']);
        }
    }
    public function toggleStatus(Delivery $delivery)
    {
        if ($delivery->status === '送信済み') {
            return response()->json(['status' => $delivery->status]);
        }

        $delivery->status = $delivery->nextStatus();
        $delivery->save();

        // ログ（任意）
        if (class_exists(\App\Models\DeliveryLog::class)) {
            \App\Models\DeliveryLog::create([
                'delivery_id' => $delivery->id,
                'user_id' => Auth::id(),
                'action' => 'ステータス変更: ' . $delivery->status,
            ]);
        }

        return response()->json(['status' => $delivery->status]);
    }

}