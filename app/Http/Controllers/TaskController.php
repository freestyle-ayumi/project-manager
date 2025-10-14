<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /* ★タスク一覧（週単位カレンダー形式）*/
    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $endDate = $today->copy()->addDays(6); // 1週間

        // 祝日CSV読み込み
        $holidays = []; // 必要に応じて読み込み処理

        // 表示期間に関係あるプロジェクトだけ取得
        $projects = Project::with(['tasks.assignees'])
            ->where(function($query) use ($today, $endDate) {
                $query->whereBetween('start_date', [$today, $endDate])
                    ->orWhereBetween('end_date', [$today, $endDate])
                    ->orWhere(function($q) use ($today, $endDate) {
                        $q->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $endDate);
                    });
            })->get();

        if (!$projects) {
            $projects = collect();
        }

        // ログインユーザーのタスク
        $myTasks = Task::whereHas('assignees', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->where(function($query) use ($today, $endDate) {
            $query->whereBetween('start_date', [$today, $endDate])
                ->orWhereBetween('due_date', [$today, $endDate])
                ->orWhere(function($q2) use ($today, $endDate) {
                    $q2->where('start_date', '<=', $today)
                        ->where('due_date', '>=', $endDate);
                });
        })
        ->with(['project', 'assignees'])
        ->get();


        // 全ユーザーのタスク
        $allTasks = Task::whereBetween('due_date', [$today, $endDate])
            ->with(['project', 'assignees'])
            ->get();

        // 全ユーザー一覧取得
        $users = User::all();

        return view('tasks.index', compact(
            'today', 'endDate', 'projects', 'myTasks', 'allTasks', 'users', 'holidays'
        ));
    }


    /* ★タスク詳細 */
    public function show(Task $task)
    {
        $task->load('assignees', 'project', 'creator');

        return view('tasks.show', compact('task'));
    }

    /* ★タスク作成フォーム */
    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        $roles = \App\Models\Role::all();

        return view('tasks.create', compact('projects', 'users', 'roles'));
    }

    /* ★タスク保存 */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
            'priority' => 'nullable|string',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // タスク作成
        $task = Task::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'priority' => $request->priority,
            'user_id' => auth()->id(), // 作成者
        ]);

        // 担当者（assignees）をtask_userに登録
        if ($request->assignees) {
            $task->assignees()->sync($request->assignees);
        }

        // 担当者（ユーザー単位）を登録
        $assigneeIds = $request->assignees ?? [];

        // ロール単位で担当者を追加
        if ($request->has('roles')) {
            $roleUserIds = User::whereIn('role_id', $request->roles)->pluck('id')->toArray();
            $assigneeIds = array_merge($assigneeIds, $roleUserIds);
        }

        // 重複を除いて中間テーブルに登録
        $assigneeIds = array_unique($assigneeIds);

        // 中間テーブルに登録
        if (!empty($assigneeIds)) {
            $task->assignees()->sync($assigneeIds);
        }

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました');
    }

    /* ★タスク編集フォーム */
    public function edit(Task $task)
    {
        $task->assignees()->sync($request->assignees ?? []);

        $projects = Project::all();
        $users = User::all();
        $roles = \App\Models\Role::all();

        return view('tasks.edit', compact('task', 'projects', 'users', 'roles'));
    }

    /* ★タスク更新 */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
            'priority' => 'nullable|string',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
        ]);

        $task->update([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        // 担当者更新
        $task->assignees()->sync($request->assignees ?? []);

        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました。');
    }

    /* ★タスク削除 */
    public function destroy(Task $task)
    {
        $task->assignees()->detach();
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました。');
    }
}
