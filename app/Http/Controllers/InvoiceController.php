<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\Client;
use App\Models\InvoiceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    // ■ 請求書一覧を表示する
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
        $invoices = Invoice::with(['project'])
            ->whereHas('project', function ($q) use ($sixMonthsAgo, $sixMonthsLater) {
                $q->whereBetween('start_date', [$sixMonthsAgo, $sixMonthsLater]);
            })
            ->when($search, function ($query, $search) {
                $query->where('invoices.subject', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

        // フィルター処理
        if ($projectFilter !== 'all') {
            $invoices->whereHas('project', function ($q) use ($today, $projectFilter) {
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
            $invoices->orderBy('created_at', 'asc');
        } else {
            $invoices->orderBy('created_at', 'desc');
        }

        // ページネーション
        $invoices = $invoices->paginate(20)->appends($request->query());

        return view('invoices.index', compact('invoices', 'search', 'sort', 'projectFilter'));
    }

    // 新規請求書作成フォームを表示する
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

        $defaultInvoiceNumber = date('ymd') . str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);

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

        return view('invoices.create', compact(
            'projects',
            'clients',
            'projectClientMap',
            'allClientsMap',
            'defaultInvoiceNumber',
            'selectedProjectId',
            'selectedClientId'
        ));
    }


    // 新規請求書をデータベースに保存する
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                Rule::unique('invoices', 'project_id'),
            ],
            'client_id' => 'nullable|exists:clients,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number',
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
            $invoice = new Invoice();
            // $invoice->fill($validated);  ← これを削除！！itemsが消える原因！！
            $invoice->project_id = $validated['project_id'];
            $invoice->client_id = $validated['client_id'] ?? null;
            $invoice->invoice_number = $validated['invoice_number'];
            $invoice->issue_date = $validated['issue_date'];
            $invoice->expiry_date = $validated['expiry_date'];
            $invoice->delivery_date = $validated['delivery_date'];
            $invoice->delivery_location = $validated['delivery_location'];
            $invoice->payment_terms = $validated['payment_terms'];
            $invoice->subject = $validated['subject'];
            $invoice->notes = $validated['notes'];
            $invoice->user_id = Auth::id();
            $invoice->status = '作成済み';
            $invoice->total_amount = 0;
            $invoice->save();

            $totalAmount = 0;

            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                $invoice->items()->create([
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

            $invoice->total_amount = round($totalAmount, 0);
            $invoice->save();

            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'user_id' => Auth::id(),
                'action' => '作成',
            ]);

            DB::commit();

            return redirect()->route('invoices.index')
                            ->with('success', '請求書が正常に登録されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '請求書の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    // 特定の請求書詳細を表示する
    public function show(Invoice $invoice)
    {
        $invoice->load(['project', 'client', 'user', 'items', 'logs' => function ($query) {
        $query->orderBy('created_at', 'desc');
        }, 'logs.user']);

        return view('invoices.show', compact('invoice'));
    }

    // 請求書編集フォームを表示する
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

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

        return view('invoices.edit', compact('invoice', 'projects', 'clients', 'projectClientMap', 'allClientsMap'));
    }

    // 請求書をデータベースで更新する
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                Rule::unique('invoices', 'project_id')->ignore($invoice->id), // 重複を許さず、自分だけ許す
            ],
            'client_id' => 'nullable|exists:clients,id',
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoices', 'invoice_number')->ignore($invoice->id), // こちらも同様にignoreが必要
            ],
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
            $invoice->fill($validated); // fillメソッドでバリデート済みのデータを一括設定
            $invoice->save();

            $totalAmount = 0;
            $itemsToKeep = [];

            foreach ($validated['items'] as $itemData) {
                $price = (float)$itemData['price'];
                $quantity = (int)$itemData['quantity'];
                $taxRate = (float)$itemData['tax_rate'];
                $subtotal = $price * $quantity;
                $taxAmount = $subtotal * ($taxRate / 100);

                if (isset($itemData['id']) && $itemData['id']) {
                    $item = InvoiceItem::find($itemData['id']);
                    if ($item && $item->invoice_id === $invoice->id) {
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
                    $newItem = $invoice->items()->create([
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

            $existingItemIds = $invoice->items->pluck('id')->toArray();
            $itemsToDelete = array_diff($existingItemIds, $itemsToKeep);
            if (!empty($itemsToDelete)) {
                InvoiceItem::destroy($itemsToDelete);
            }

            $invoice->total_amount = round($totalAmount, 0);
            $invoice->save();

            // ログに保存
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'user_id' => Auth::id(),
                'action' => '更新',
            ]);

            DB::commit();

            return redirect()->route('invoices.index')
                             ->with('success', '請求書が更新されました。');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => '請求書の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    // 特定の請求書をデータベースから削除する
    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();
        try {
            // ログに保存
            InvoiceLog::create([
                'invoice_id' => $invoice->id, // 削除された請求書のID
                'user_id' => Auth::id(),
                'action' => '削除-請求番号: ' . $invoice->invoice_number,
            ]);
            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                             ->with('success', '請求書が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '請求書の削除中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    public function downloadPdf($id)
    {
        try {
            // 対象の請求データ取得（関連も一括で）
            $invoice = Invoice::with(['client', 'items', 'project'])->findOrFail($id);

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
            $html = view('invoices.show_pdf_mpdf', compact('invoice'))->render();
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            // ファイル名生成（日本語文字は避ける）
            $invoiceNumber = $invoice->invoice_number;
            $issueDate = \Carbon\Carbon::parse($invoice->issue_date)->format('Ymd');
            $filename = $invoiceNumber . '-' . $issueDate . '.pdf';

            // 保存先パス
            $savePath = 'invoices/' . $filename;

            // PDF をストレージに保存（public ディスク）
            Storage::disk('public')->put($savePath, $mpdf->Output('', 'S'));

            // 公開 URL を生成
            $fullUrl = asset('storage/' . $savePath);

            // 請求書の pdf_path を更新
            $invoice->pdf_path = $fullUrl;
            $invoice->save();

            // 操作ログを残す
            InvoiceLog::create([
                'invoice_id' => $invoice->id,
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
    // app/Http/Controllers/InvoiceController.php
    public function downloadPdfMpdf(Invoice $invoice)
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
            $html = view('invoices.show_pdf_mpdf', compact('invoice'))->render();
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            // ファイル名作成
            $filename = $invoice->invoice_number . '-' . \Carbon\Carbon::parse($invoice->issue_date)->format('Ymd') . '.pdf';
            $savePath = 'invoices/' . $filename;

            // 保存
            Storage::disk('public')->put($savePath, $mpdf->Output('', 'S'));

            // 公開 URL
            $fullUrl = asset('storage/' . $savePath);

            // invoice テーブルに PDF パス保存
            $invoice->pdf_path = $fullUrl;
            $invoice->save();

            // ログに保存
            $invoice->logs()->create([
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
    public function toggleStatus(Invoice $invoice)
    {
        // 送信済みなら進めない
        if ($invoice->status === Invoice::STATUS_SENT) {
            return response()->json(['status' => $invoice->status]);
        }

        // 次のステータスに変更
        $newStatus = $invoice->nextStatus();

        // 変更して保存
        $invoice->status = $newStatus;
        $invoice->save();  // ← これが絶対必要！！

        // ログ（任意）
        InvoiceLog::create([
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id(),
            'action' => 'ステータス変更: ' . $newStatus,
        ]);

        return response()->json(['status' => $newStatus]);
    }

}