<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Expense;
use App\Models\Task;
use Carbon\Carbon;
use App\Models\AttendanceRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. 自分の打刻履歴（最新20件、降順）
        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->with('location')  // 勤務地名を表示するため
            ->orderBy('timestamp', 'desc')
            ->take(20)
            ->get();

        // 2. 今後のプロジェクト（開催前・開催中：今日を含む）
        $upcomingProjects = Project::where(function ($query) use ($today) {
            $query->where('start_date', '>', $today)
                ->orWhere(function ($q) use ($today) {
                    $q->where('start_date', '<=', $today)
                      ->where('end_date', '>=', $today);
                })
                ->orWhere(function ($q) use ($today) {
                    $q->whereNull('end_date')
                      ->where('start_date', '>=', $today);
                });
        })
        ->with('client')
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get();

        // 3. 未承認の経費（自分が申請した「申請中」または「差し戻し」）
        $pendingExpenses = Expense::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['申請中', '差し戻し']);
            })
            ->with(['status', 'project'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // 4. 割り当てられた未完了タスク
        $assignedTasks = Task::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', '!=', '完了')
        ->with('project')
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();

        return view('dashboard', compact(
            'attendanceRecords',
            'upcomingProjects',
            'pendingExpenses',
            'assignedTasks',
            'today'
        ));
    }
}