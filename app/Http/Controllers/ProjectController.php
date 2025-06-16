<?php

namespace App\Http\Controllers; // ここは App\Http\Controllers であるべきです

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\ExpenseStatus;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Invoice;
use App\Models\Expense; // この行は必要です。Expenseモデルを使用することを宣言しています。

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * プロジェクト一覧を表示する
     */
    public function index()
    {
        $projects = Project::with([
            'client',
            'user',
            'status', // Projectモデルにstatusリレーションがない場合は、Project.phpのリレーション名に合わせてください
            'tasks',
            'quotes',
            'invoices',
            'expenses' => function ($query) {
                $approvedStatus = ExpenseStatus::where('name', '承認済み')->first();
                if ($approvedStatus) {
                    $query->where('expense_status_id', $approvedStatus->id); // ★ここを修正しました
                }
            }
        ])->get();

        return view('projects.index', compact('projects'));
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

        // 現在ログインしているユーザーのIDを取得
        $userId = Auth::id();

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_status_id' => $request->project_status_id,
            'client_id' => $request->client_id,
            'user_id' => $userId, // ユーザーIDを保存
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
        $project->load(['client', 'user', 'status', 'tasks', 'quotes.items', 'invoices.items', 'expenses.items']);
        // 'status'リレーションがProjectモデルにない場合、
        // ProjectStatusモデルとのリレーション名をProject.phpで確認し、
        // 例えば 'projectStatus' になっている場合は $project->load(['projectStatus', ...]) のように修正が必要です。

        return view('projects.show', compact('project'));
    }
}