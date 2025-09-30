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
    public function index(Request $request)
    {
        $query = Expense::with(['user', 'project', 'status']);

        // 検索
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('project', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('status', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // ステータスフィルター
        if ($status = $request->input('status_filter')) {
            if ($status !== 'all') {
                $query->whereHas('status', fn($q) => $q->where('name', $status));
            }
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(20);

        // Blade に渡す変数
        $statuses = \App\Models\ExpenseStatus::all();
        $canEdit = in_array(auth()->user()->role->name, ['master', 'developer', 'accounting']);

        return view('expenses.index', compact('expenses', 'statuses', 'canEdit'));
    }

    /**
     * 新規経費申請フォームを表示する
     */
    public function create()
    {
        $projects = Project::whereDoesntHave('status', function ($query) {
            $query->whereIn('name', ['完了', '終了']); // '完了' または '終了' ステータスのプロジェクトを除外
        })->orderBy('name')->get();

        // デフォルトの申請日 (今日の日付)
        $defaultApplicationDate = Carbon::now()->format('Y-m-d');

        return view('expenses.create', compact('projects', 'defaultApplicationDate'));
    }

    /**
     * 新規経費申請を保存する
     */
    public function store(Request $request)
{
        $request->validate([
        'project_id' => 'required|exists:projects,id',
        'application_date' => 'required|date',
        'items.*.item_name' => 'required|string',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.tax_rate' => 'required|numeric|min:0',
    ]);

    DB::transaction(function() use ($request) {
        $expense = Expense::create([
            'user_id' => $request->input('applicant_id'),
            'date' => $request->input('application_date'),
            'expense_status_id' => $request->input('expense_status_id'),
            'project_id' => $request->input('project_id'),
            'amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->input('items') as $itemData) {
            $subtotal = $itemData['price'] * $itemData['quantity'] * (1 + $itemData['tax_rate']/100);
            ExpenseItem::create([
                'expense_id' => $expense->id,
                'item_name' => $itemData['item_name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'],
                'unit' => $itemData['unit'],
                'tax_rate' => $itemData['tax_rate'],
                'subtotal' => $subtotal,
            ]);
            $totalAmount += $subtotal;
        }

        $expense->update(['amount' => $totalAmount]);
    });

    return redirect()->route('expenses.index')->with('success','経費申請が正常に作成されました。');
    }
    
    public function show(Expense $expense) {
    $expense->load('items','user','status','project');
    return view('expenses.show', compact('expense'));
    }

public function edit(Expense $expense) {
    // 編集用に必要なデータを渡す
    $projects = Project::orderBy('name')->get();
    return view('expenses.edit', compact('expense','projects'));
}

public function destroy(Expense $expense) {
    $allowedRoles = ['master', 'developer', 'accounting'];
    if (!in_array(auth()->user()->role->name, $allowedRoles)) {
        abort(403, 'この操作を行う権限がありません。');
    }

    $expense->delete();
    return redirect()->route('expenses.index')->with('success','削除しました。');
}
public function updateStatus(Request $request, Expense $expense)
{
    $allowedRoles = ['master', 'developer', 'accounting'];
    if (!in_array(auth()->user()->role->name, $allowedRoles)) {
        abort(403, 'この操作を行う権限がありません。');
    }

    $request->validate([
        'expense_status_id' => 'required|exists:expense_statuses,id'
    ]);

    $expense->update([
        'expense_status_id' => $request->input('expense_status_id')
    ]);

    return redirect()->route('expenses.index')->with('success','ステータスを更新しました。');
}


}
