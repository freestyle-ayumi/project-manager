<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Project; // Projectモデルをインポート
use App\Models\ExpenseStatus; // ExpenseStatusモデルをインポート
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Storageファサードをインポート
use Carbon\Carbon; // Carbonをインポート

class ExpenseController extends Controller
{
    /**
     * 経費一覧を表示する
     */
    public function index()
    {
        // データベースから経費データを取得します
        // 関連するプロジェクト、ユーザー、経費ステータス、および経費項目も一緒に取得
        $expenses = Expense::with(['user', 'status', 'items.project'])->orderBy('application_date', 'desc')->get();

        return view('expenses.index', compact('expenses'));
    }

    /**
     * 新規経費申請フォームを表示する
     */
    public function create()
    {
        // 経費の費目カテゴリのリスト
        $expenseCategories = [
            '交通費', '宿泊費', '会議費', '消耗品費', '通信費',
            '交際費', '福利厚生費', '旅費交通費', '図書費', '研修費',
            'その他'
        ];

        // 関連付け可能なプロジェクトのリスト
        // 終了していないプロジェクトのみを取得
        // プロジェクトのステータスが '完了' または '終了' でないものを取得すると仮定します。
        // プロジェクトモデルに 'status' リレーションまたは 'status_name' カラムが存在することを前提としています。
        $projects = Project::whereDoesntHave('status', function ($query) {
            $query->whereIn('name', ['完了', '終了']); // '完了' または '終了' ステータスのプロジェクトを除外
        })->orderBy('name')->get();

        // もしProjectモデルにstatusリレーションがない場合、以下のように直接statusカラムでフィルタリングすることも可能です。
        // $projects = Project::whereNotIn('status_column_name', ['完了', '終了'])->orderBy('name')->get();
        // または、is_completedのようなbooleanカラムがある場合
        // $projects = Project::where('is_completed', false)->orderBy('name')->get();


        // デフォルトの申請日 (今日の日付)
        $defaultApplicationDate = Carbon::now()->format('Y-m-d');

        return view('expenses.create', compact('expenseCategories', 'projects', 'defaultApplicationDate'));
    }

    /**
     * 新規経費申請を保存する
     */
    public function store(Request $request)
    {
        // バリデーションルール
        $request->validate([
            'applicant_id' => 'required|exists:users,id',
            'application_date' => 'required|date',
            'expense_status_id' => 'required|exists:expense_statuses,id',
            'project_id' => 'nullable|exists:projects,id', // 全体プロジェクトのバリデーションを追加
            'overall_reason' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.date' => 'required|date',
            'items.*.payee' => 'required|string|max:255',
            'items.*.project_id' => 'nullable|exists:projects,id', // 項目別プロジェクトのバリデーション
            'items.*.description' => 'nullable|string|max:1000',
            'items.*.file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048', // 2MBまで
        ]);

        DB::transaction(function () use ($request) {
            // Expense レコードを作成
            $expense = Expense::create([
                'user_id' => $request->input('applicant_id'),
                'application_date' => $request->input('application_date'),
                'expense_status_id' => $request->input('expense_status_id'),
                'project_id' => $request->input('project_id'), // 全体プロジェクトを保存
                'overall_reason' => $request->input('overall_reason'),
                'total_amount' => 0, // 後で計算して更新
            ]);

            $totalAmount = 0;

            // 各経費項目を保存
            foreach ($request->input('items') as $key => $itemData) {
                $filePath = null;
                // ファイルがアップロードされた場合
                if ($request->hasFile("items.{$key}.file")) {
                    $file = $request->file("items.{$key}.file");
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    // publicディスクにファイルを保存
                    $filePath = $file->storeAs('expense_files', $fileName, 'public');
                }

                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'category' => $itemData['category'],
                    'amount' => $itemData['amount'],
                    'date' => $itemData['date'],
                    'payee' => $itemData['payee'],
                    'description' => $itemData['description'],
                    'project_id' => $itemData['project_id'] ?? null,
                    'file_path' => $filePath,
                ]);

                $totalAmount += $itemData['amount'];
            }

            // Expenseの合計金額を更新
            $expense->update(['total_amount' => $totalAmount]);

            // ログに保存 (必要であれば)
            // QuoteLog::create([
            //     'expense_id' => $expense->id,
            //     'user_id' => Auth::id(),
            //     'action' => '新規経費申請が作成されました。',
            // ]);
        });

        return redirect()->route('expenses.index')->with('success', '経費申請が正常に作成されました。');
    }

    // 他のアクション (show, edit, update, destroy) は必要に応じて...
}
