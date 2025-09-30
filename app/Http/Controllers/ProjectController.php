<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\ExpenseStatus;
use Illuminate\Http\Request;
use App\Models\Quote; // Quoteモデルをインポート
use App\Models\Invoice;
use App\Models\Expense;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * プロジェクト一覧を表示する
     */
    public function index()
    {
        $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();

        $projects = Project::with([
            'client',
            'user',
            'status',
            'tasks',
            'quotes',
            'invoices',
            'expenses' => function ($query) use ($approvedStatus) {
                if ($approvedStatus) {
                    $query->where('expense_status_id', $approvedStatus->id);
                }
            }
        ])
        ->withSum('quotes', 'total_amount')    // 見積額の合計
        ->withSum('invoices', 'total_amount')  // 請求額の合計
        ->withSum(['expenses as approved_expenses_sum' => function ($query) use ($approvedStatus) {
        if ($approvedStatus) {
            $query->where('expense_status_id', $approvedStatus->id);
        }
    }], 'amount')
    ->withSum(['expenses as unapproved_expenses_sum' => function ($query) use ($approvedStatus) {
        if ($approvedStatus) {
            $query->where('expense_status_id', '<>', $approvedStatus->id);
        }
    }], 'amount')
        ->get();

        // 各プロジェクトの最新見積書を配列で渡す
        $latestQuotes = [];
        foreach ($projects as $project) {
            $latestQuotes[$project->id] = $project->quotes->sortByDesc('issue_date')->first();
        }

        return view('projects.index', compact('projects', 'latestQuotes'));
    }

    /**
     * 新規プロジェクト作成フォームを表示する
     */
    public function create()
    {
        $clients = Client::all();
        $projectStatuses = ProjectStatus::all();

        return view('projects.create', compact('clients', 'projectStatuses'));
    }

    /**
     * 新規プロジェクトをデータベースに保存する
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_status_id' => 'required|exists:project_statuses,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $userId = Auth::id();

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_status_id' => $request->project_status_id,
            'client_id' => $request->client_id,
            'user_id' => $userId,
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'プロジェクトが正常に作成されました。');
    }

    /**
     * プロジェクト編集フォームを表示する
     */
    public function edit(Project $project)
    {
        $clients = Client::all();
        $projectStatuses = ProjectStatus::all();
        return view('projects.edit', compact('project', 'clients', 'projectStatuses'));
    }

    /**
     * プロジェクトをデータベースで更新する
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_status_id' => 'required|exists:project_statuses,id',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_status_id' => $request->project_status_id,
            'client_id' => $request->client_id,
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'プロジェクトが正常に更新されました。');
    }

    /**
     * プロジェクトをデータベースから削除する
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'プロジェクトが正常に削除されました。');
    }

    /**
     * プロジェクト詳細を表示する
     */
    public function show(Project $project)
    {
        $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();
        $pendingStatus = ExpenseStatus::where('name', '未承認')->first();

        $project = Project::with([
            'client',
            'user',
            'status',
            'tasks',
            // 'quotes.items', // 個別の見積書詳細に飛ばす場合、これらのリレーションは直接は必要ないかもしれません
            // 'invoices.items',
            // 'expenses.items'
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
