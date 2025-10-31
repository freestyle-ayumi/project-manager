<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Color;
use App\Models\Expense;
use App\Models\ExpenseStatus;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /* プロジェクト一覧を表示する */
    public function index(Request $request)
    {
        $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();

        // カレンダー用（前後1年分）
        $startRange = now()->subYear()->format('Y-m-d');
        $endRange = now()->addYear()->format('Y-m-d');

        $allProjects = Project::with(['client', 'user', 'color']) 
            ->where(function ($q) use ($startRange, $endRange) {
                $q->whereBetween('start_date', [$startRange, $endRange])
                  ->orWhereBetween('end_date', [$startRange, $endRange]);
            })
            ->orderBy('start_date', 'asc')
            ->get();

        // リスト用（既存の検索・絞り込み付き）
        $query = Project::with([
            'client',
            'user',
            'users',
            'color', 
            'tasks',
            'quotes',
            'invoices',
            'expenses' => function ($q) use ($approvedStatus) {
                if ($approvedStatus) {
                    $q->where('expense_status_id', $approvedStatus->id);
                }
            }
        ])
        ->withSum('quotes', 'total_amount')
        ->withSum('invoices', 'total_amount')
        ->withSum(['expenses as approved_expenses_sum' => function ($q) use ($approvedStatus) {
            if ($approvedStatus) {
                $q->where('expense_status_id', $approvedStatus->id);
            }
        }], 'amount')
        ->withSum(['expenses as unapproved_expenses_sum' => function ($q) use ($approvedStatus) {
            if ($approvedStatus) {
                $q->where('expense_status_id', '<>', $approvedStatus->id);
            }
        }], 'amount');

        // デフォルト絞り込み
        if (!$request->has('search')) {
            $query->where(function ($q) {
                $today = \Carbon\Carbon::today();
                $q->where(function($q2) use ($today) {
                    $q2->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
                })
                ->orWhere('start_date', '>', $today);
            });
        }

        // キーワード検索
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhereHas('client', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('tasks', function($q3) use ($search) {
                    $q3->where('name', 'like', "%{$search}%");
                });
            });
        }

        // ステータスフィルター
        $today = \Carbon\Carbon::today();
        if ($status = $request->input('status')) {
            if ($status === 'upcoming') {
                $query->where('start_date', '>', $today);
            } elseif ($status === 'ongoing') {
                $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
            } elseif ($status === 'finished') {
                $query->where('end_date', '<', $today);
            }
        }

        $projects = $query->with(['client', 'users', 'tasks'])->get();

        // 検索キーワード
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('tasks', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $projects = $query->orderBy('start_date', 'asc')->get();

        // 最新見積書
        $latestQuotes = [];
        foreach ($projects as $project) {
            $latestQuotes[$project->id] = $project->quotes->sortByDesc('issue_date')->first();
        }

        // 色一覧
        $colors = Color::all();

        // Bladeに2種類渡す（リスト用・全件カレンダー用）
        return view('projects.index', compact('projects', 'latestQuotes',  'allProjects', 'colors'));
    }

    /* 新規プロジェクト作成フォームを表示する */
    public function create()
    {
        $clients = Client::all();
        $colors = Color::all(); 
        $users = User::all();
        $projectUsers = [];

        return view('projects.create', compact('clients', 'colors', 'users', 'projectUsers'));
    }

    /* 新規プロジェクトをデータベースに保存する */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|integer|exists:colors,id',
            'client_id' => 'required|integer|exists:clients,id',
            'venue' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $userId = Auth::id();

        // プロジェクト作成
        $project = Project::create([
            'name' => $request->name,
            'color' => $request->color,
            'client_id' => $request->client_id,
            'venue' => $request->venue,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'user_id' => $userId,
        ]);

        // 担当者保存（多対多）
        if ($request->has('users')) {
            $project->users()->sync($request->users);
        }

        return redirect()->route('projects.show', $project)
                        ->with('success', 'プロジェクトを作成しました。');
    }


    /* プロジェクト編集フォームを表示する */
    public function edit(Project $project)
    {
        $clients = Client::all();
        $colors = Color::all();
        $users = User::all(); // ← 追加

        $projectUsers = $project->users()->pluck('id')->toArray();

        return view('projects.edit', compact('project', 'clients', 'users', 'colors', 'projectUsers'));
    }

    /* プロジェクトをデータベースで更新する */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|integer|exists:colors,id',
            'client_id' => 'required|integer|exists:clients,id',
            'venue' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $project->update([
            'name' => $request->name,
            'color' => $request->color,
            'client_id' => $request->client_id,
            'venue' => $request->venue,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);

        $project->users()->sync($request->users ?? []);

        return redirect()->route('projects.show', $project)->with('success', 'プロジェクトを更新しました。');
    }

    /* プロジェクトをデータベースから削除する */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'プロジェクトが正常に削除されました。');
    }

    /* プロジェクト詳細を表示する */
    public function show(Project $project)
    {
        $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();
        $pendingStatus = ExpenseStatus::where('name', '未承認')->first();

        $project = Project::with([
            'client',
            'user',
            'color',
            'tasks',
            'tasks.users',
        ])
        ->withSum('quotes', 'total_amount')
        ->withSum('invoices', 'total_amount')
        ->withSum(['expenses as total_approved_expenses_sum' => function($query) use ($approvedStatus) {
            if ($approvedStatus) $query->where('expense_status_id', $approvedStatus->id);
        }], 'amount')
        ->withSum(['expenses as total_pending_expenses_sum' => function($query) use ($pendingStatus) {
            if ($pendingStatus) $query->where('expense_status_id', $pendingStatus->id);
        }], 'amount')
        ->findOrFail($project->id);

        // 既存の処理（例: タスクや見積、請求などの取得）
        $latestQuote = $project->quotes()->latest('created_at')->first();
        $latestInvoice = $project->invoices()->latest('created_at')->first();

        // 最新ログに紐づくPDF付きの見積書
        $latestLogWithPdf = \App\Models\QuoteLog::whereHas('quote', function($q) use ($project) {
            $q->where('project_id', $project->id);
        })
        ->whereHas('quote', fn($q) => $q->whereNotNull('pdf_path')) // PDFがある見積書
        ->latest('created_at')
        ->first();

        $latestPdfQuoteId = $latestLogWithPdf ? $latestLogWithPdf->quote_id : null;

        return view('projects.show', compact(
            'project', 
            'latestQuote', 
            'latestInvoice', 
            'latestPdfQuoteId'
        ));
    }
}