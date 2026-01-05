<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Expense;
use App\Models\Task;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. 今後のプロジェクト（開催前・開催中：今日を含む）
        $today = Carbon::today();

        $upcomingProjects = Project::where(function ($query) use ($today) {
            // 開催前：start_date > today（明日以降）
            $query->where('start_date', '>', $today);

            // または開催中（end_dateあり）：start_date <= today かつ end_date >= today
            $query->orWhere(function ($q) use ($today) {
                $q->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today);
            });

            // または開催中（end_date NULL）：start_date >= today（今日を含む）
            $query->orWhere(function ($q) use ($today) {
                $q->whereNull('end_date')
                ->where('start_date', '>=', $today);
            });
        })
        ->with('client')
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get();

        // 2. 未承認の経費（自分が申請したもの）
        $pendingExpenses = Expense::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('name', '未承認');
            })
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // 3. 割り当てられた未完了タスク
        $assignedTasks = Task::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', '!=', '完了') // 完了以外
        ->with('project')
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();

        return view('dashboard', compact('upcomingProjects', 'pendingExpenses', 'assignedTasks', 'today'));
    }
}