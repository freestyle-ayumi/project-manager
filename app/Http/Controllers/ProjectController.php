<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectKeywordFlag;
use App\Models\ProjectChecklist;
use App\Models\Client;
use App\Models\Color;
use App\Models\ExpenseStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // ■ プロジェクト一覧
    public function index(Request $request)
    {
        $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();
        $startRange = now()->subYear()->format('Y-m-d');
        $endRange = now()->addYear()->format('Y-m-d');

        // カレンダー用全件取得
        $allProjects = Project::with(['client', 'user', 'color'])
            ->whereBetween('start_date', [$startRange, $endRange])
            ->orWhereBetween('end_date', [$startRange, $endRange])
            ->orderBy('start_date', 'asc')
            ->get();

        // リスト用クエリ
        $query = Project::with([
            'client', 'user', 'users', 'color', 'tasks', 'quotes', 'deliveries', 'invoices',
            'expenses' => function($q) use ($approvedStatus) {
                if ($approvedStatus) $q->where('expense_status_id', $approvedStatus->id);
            }
        ])
        ->withSum('quotes', 'total_amount')
        ->withSum('deliveries', 'total_amount')
        ->withSum('invoices', 'total_amount')
        ->withSum(['expenses as approved_expenses_sum' => function($q) use ($approvedStatus) {
            if ($approvedStatus) $q->where('expense_status_id', $approvedStatus->id);
        }], 'amount')
        ->withSum(['expenses as unapproved_expenses_sum' => function($q) use ($approvedStatus) {
            if ($approvedStatus) $q->where('expense_status_id', '<>', $approvedStatus->id);
        }], 'amount');

        // キーワード検索
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('tasks', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
            });
        }

        // ステータス絞り込み
        $today = now()->today();
        if ($status = $request->input('status')) {
            if ($status === 'upcoming') {
                $query->where(function($q) use ($today) {
                    $q->where('start_date', '>', $today)
                    ->orWhere(function($q2) use ($today) {
                        $q2->whereNull('end_date')
                            ->where('start_date', '>', $today);
                    });
                });
            }

            if ($status === 'ongoing') {
                $query->where(function($q) use ($today) {
                    $q->where(function($q2) use ($today) {
                        $q2->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today);
                    })
                    ->orWhere(function($q3) use ($today) {
                        // end_date が NULL → start_date を end とみなす
                        $q3->whereNull('end_date')
                        ->where('start_date', '<=', $today)
                        ->where('start_date', '>=', $today);
                    });
                });
            }

            if ($status === 'finished') {
                $query->where(function($q) use ($today) {
                    $q->where('end_date', '<', $today)
                    ->orWhere(function($q2) use ($today) {
                        $q2->whereNull('end_date')
                            ->where('start_date', '<', $today);
                    });
                });
            }
        }


        $projects = $query->orderBy('start_date', 'desc')->get();

        // 最新見積書とステータスを取得
        $latestQuotes = [];
        $latestDeliveries = [];
        foreach ($projects as $project) {
            $latestQuotes[$project->id] = $project->quotes()->latest('created_at')->first();
            $latestDeliveries[$project->id] = $project->deliveries()->latest('created_at')->first();
        }

        // 最新納品書
        $latestDeliveries = [];
        foreach ($projects as $project) {
            $latestDeliveries[$project->id] = $project->deliveries->sortByDesc('delivery_date')->first();
        }
        // 最新請求書を取得
        $latestInvoices = [];
        foreach ($projects as $project) {
            $latestInvoices[$project->id] = $project->invoices()->latest('created_at')->first();
        }

        $colors = Color::all();

        return view('projects.index', compact('projects', 'latestQuotes', 'latestDeliveries','latestInvoices', 'allProjects', 'colors'));
    }

    // ■ 作成フォーム
    public function create()
    {
        $clients = Client::all();
        $colors = Color::all();
        $users = User::all();
        $projectUsers = [];
        $keywordFlags = ProjectKeywordFlag::with('templates')->get();

        return view('projects.create', 
            compact(
            'clients','colors','users','projectUsers','keywordFlags'
        ));
    }

    // ■ 保存
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'color'=>'required|integer|exists:colors,id',
            'client_id'=>'required|integer|exists:clients,id',
            'venue'=>'required|string|max:255',
            'start_date'=>'required|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'description'=>'nullable|string',
            'users'=>'nullable|array',
            'users.*'=>'integer|exists:users,id',
            'checklists'=>'nullable|array',
            'checklists.*.name'=>'required|string|max:255',
            'checklists.*.status'=>'nullable|string|max:50',
            'checklists.*.link'=>'nullable|string|max:255',
        ]);

        $userId = Auth::id();

        // プロジェクト作成
        $project = Project::create([
            'name'=>$request->name,
            'color'=>$request->color,
            'client_id'=>$request->client_id,
            'venue'=>$request->venue,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'description'=>$request->description,
            'user_id'=>$userId,
        ]);

        // 担当者保存
        if ($request->has('users')) {
            $project->users()->sync($request->users);
        }

        // チェックリスト作成
        $checklists = [];
        $submittedItems = $request->input('checklists', []);
        foreach ($submittedItems as $item) {
            $name = trim((string)($item['name'] ?? ''));
            if ($name === '') continue;

            $checklists[] = [
                'project_id' => $project->id,
                'name' => $name,
                'status' => $item['status'] ?? '未',
                'link' => $item['link'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($checklists)) {
            ProjectChecklist::insert($checklists);
        }

        return redirect()->route('projects.show', $project)
                        ->with('success', 'イベントを作成しました');
    }


    // ■ 編集フォーム
    public function edit(Project $project)
    {
        $clients = Client::all();
        $colors = Color::all();
        $users = User::all();

        // プロジェクトに紐づく担当者IDを取得
        $projectUsers = $project->users()->pluck('id')->toArray();

        // プロジェクトに紐づくチェックリストを取得
        $checklists = $project->checklists()->get();

        return view('projects.edit', compact(
            'project',
            'clients',
            'users',
            'colors',
            'projectUsers',
            'checklists'
        ));
    }

    // ■ 更新
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'color'=>'required|integer|exists:colors,id',
            'client_id'=>'required|integer|exists:clients,id',
            'venue'=>'required|string|max:255',
            'start_date'=>'required|date',
            'end_date'=>'nullable|date|after_or_equal:start_date',
            'description'=>'nullable|string',
            'users'=>'nullable|array',
            'users.*'=>'integer|exists:users,id',
            'checklists'=>'nullable|array',
            'checklists.*.id'=>'nullable|integer|exists:project_checklists,id',
            'checklists.*.name'=>'required|string|max:255',
            'checklists.*.status'=>'nullable|string|max:50',
            'checklists.*.link'=>'nullable|string|max:255',
            'removed_checklists'=>'nullable|array',
            'removed_checklists.*'=>'integer|exists:project_checklists,id',
        ]);

        // プロジェクト更新
        $project->update([
            'name'=>$request->name,
            'color'=>$request->color,
            'client_id'=>$request->client_id,
            'venue'=>$request->venue,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'description'=>$request->description,
        ]);

        // 担当者更新
        $project->users()->sync($request->users ?? []);

        // 削除対象のチェックリスト
        $removedIds = $request->input('removed_checklists', []);
        if (!empty($removedIds)) {
            $project->checklists()->whereIn('id', $removedIds)->delete();
        }

        // チェックリスト更新・追加
        $submittedItems = $request->input('checklists', []);
        foreach ($submittedItems as $item) {
            $name = trim($item['name'] ?? '');
            if ($name === '') continue;

            if (!empty($item['id'])) {
                // 既存のチェックリストを更新
                $checklist = $project->checklists()->find($item['id']);
                if ($checklist) {
                    $checklist->update([
                        'name' => $name,
                        'status' => $item['status'] ?? '未',
                        'link' => $item['link'] ?? null,
                    ]);
                }
            } else {
                // 新規チェックリストを作成
                $project->checklists()->create([
                    'name' => $name,
                    'status' => $item['status'] ?? '未',
                    'link' => $item['link'] ?? null,
                ]);
            }
        }

        return redirect()->route('projects.show', $project)
                        ->with('success', 'イベントを更新しました');
    }

    // ■ 削除
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')
                         ->with('success','イベントを削除しました');
    }

    // ■ 詳細表示
    public function show(Project $project)
    {
        $approvedStatus = ExpenseStatus::where('name','承認済み')->first();
        $pendingStatus = ExpenseStatus::where('name','未承認')->first();

        $project = Project::with(['client','user','color','tasks','tasks.users'])
            ->withSum('quotes','total_amount')
            ->withSum('invoices','total_amount')
            ->withSum(['expenses as total_approved_expenses_sum'=>fn($q)=> $approvedStatus?$q->where('expense_status_id',$approvedStatus->id):null],'amount')
            ->withSum(['expenses as total_pending_expenses_sum'=>fn($q)=> $pendingStatus?$q->where('expense_status_id',$pendingStatus->id):null],'amount')
            ->findOrFail($project->id);

        $latestQuote = $project->quotes()->latest('created_at')->first();
        $latestInvoice = $project->invoices()->latest('created_at')->first();

        // 見積書の最新PDF（既存のままOK）
        $latestLogWithPdf = \App\Models\QuoteLog::whereHas('quote',fn($q)=> $q->where('project_id',$project->id))
            ->whereHas('quote',fn($q)=> $q->whereNotNull('pdf_path'))
            ->latest('created_at')->first();

        $latestPdfQuoteId = $latestLogWithPdf?->quote_id;

        // --- ここから修正（納品書用） ---
        // 最新の納品書
        $latestDelivery = $project->deliveries()->latest('created_at')->first();

        // PDFが生成されている最新の納品書（pdf_path が NULL でないもの）
        $latestPdfDelivery = $project->deliveries()
            ->whereNotNull('pdf_path')
            ->latest('created_at')
            ->first();

        $latestPdfDeliveryId = $latestPdfDelivery?->id;
        // --- ここまで修正 ---

        return view('projects.show', compact(
            'project',
            'latestQuote',
            'latestInvoice',
            'latestPdfQuoteId',
            'latestDelivery',        // 追加
            'latestPdfDeliveryId'    // 追加
        ));
    }
    // ■ 詳細にチェック項目表示
    public function toggleChecklistStatus(Project $project, ProjectChecklist $checklist)
    {
        // 対象のチェックリストがこのプロジェクトに属しているか確認
        if ($checklist->project_id !== $project->id) {
            abort(404);
        }

        // ステータスの順序
        $statuses = ['未', '作', '済'];

        // 次のステータスを計算
        $currentIndex = array_search($checklist->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses); // 循環
        $checklist->status = $statuses[$nextIndex];
        $checklist->save();

        // JSONで返す
        return response()->json(['status' => $checklist->status]);
    }

    // ■ チェックリストにURL追加
    public function updateChecklistLink(Project $project, ProjectChecklist $checklist, Request $request)
    {
        if ($checklist->project_id !== $project->id) abort(404);

        $request->validate([
            'link' => 'required|url|max:255',
        ]);

        $checklist->link = $request->link;
        $checklist->save();

        return response()->json(['success'=>true, 'link'=>$checklist->link]);
    }


}
