<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /* ★タスク一覧 */
    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $endDate = $today->copy()->addDays(6);
        $holidays = [];

        // ログインユーザーのタスク
        $myTasks = Task::whereExists(function($query) {
            $query->select('*')
                ->from('task_user')
                ->whereColumn('task_user.task_id', 'tasks.id')
                ->where('task_user.user_id', auth()->id());
        })->get();

        // フィルター値取得
        $userIds = $request->input('user_ids', []);
        $roleIds = $request->input('role_ids', []);

        // ===== タスクフィルター =====
        $allTasksQuery = Task::whereBetween('due_date', [$today, $endDate])
            ->with(['project', 'assignees']);

        // 担当者フィルター
        if (!empty($userIds) && !in_array('all', $userIds)) {
            $allTasksQuery->whereHas('assignees', function ($q) use ($userIds) {
                $q->whereIn('users.id', $userIds);
            });
        }

        // ロールフィルター
        if (!empty($roleIds) && !in_array('all', $roleIds)) {
            $allTasksQuery->whereHas('assignees', function ($q) use ($roleIds) {
                $q->whereIn('users.role_id', $roleIds);
            });
        }

        $allTasks = $allTasksQuery->get();

        // ===== ユーザー取得 =====
        $userQuery = User::with(['tasks' => function($query) use ($today, $endDate) {
            $query->whereBetween('due_date', [$today, $endDate]);
        }]);

        // 担当者 or ロールでユーザーを絞り込む
        $numericUserIds = array_filter($userIds, fn($id) => is_numeric($id));
        $numericRoleIds = array_filter($roleIds, fn($id) => is_numeric($id));

        if (!in_array('all', $userIds) || !in_array('all', $roleIds)) {
            if (!empty($numericUserIds)) {
                $userQuery->whereIn('id', $numericUserIds);
            }
            if (!empty($numericRoleIds)) {
                $userQuery->orWhereIn('role_id', $numericRoleIds);
            }
        }

        $users = $userQuery->get();

        // フィルターボタン用
        $allUsersForFilter = User::all();
        $allRolesForFilter = \App\Models\Role::all();

        return view('tasks.index', compact(
            'today',
            'endDate',
            'myTasks',
            'allTasks',
            'users',
            'allUsersForFilter',
            'allRolesForFilter',
            'userIds',
            'roleIds',
            'holidays'
        ));
    }

    /* ★タスク詳細 */
    public function show(Task $task)
    {
        $task->load('assignees', 'project', 'creator');
        return view('tasks.show', compact('task'));
    }

    /* ★タスク作成フォーム */
    public function create(Request $request)
    {
        $projects = \App\Models\Project::all();
        $users = User::all();
        $roles = \App\Models\Role::all();

        $selectedUserId = $request->query('user_id');
        $defaultDate    = $request->query('date');

        return view('tasks.create', compact(
            'projects', 'users', 'roles', 'selectedUserId', 'defaultDate'
        ));
    }

    /* ★タスク保存 */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'plans_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
            'priority' => 'nullable|string',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $task = Task::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'plans_date' => $request->plans_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'priority' => $request->priority,
            'user_id' => auth()->id(),
        ]);

        // 担当者登録
        $assigneeIds = $request->assignees ?? [];
        if ($request->has('roles')) {
            $roleUserIds = User::whereIn('role_id', $request->roles)->pluck('id')->toArray();
            $assigneeIds = array_merge($assigneeIds, $roleUserIds);
        }
        $assigneeIds = array_unique($assigneeIds);
        if (!empty($assigneeIds)) {
            $task->assignees()->sync($assigneeIds);
        }

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました');
    }

    /* ★タスク編集フォーム */
    public function edit(Task $task)
    {
        $projects = \App\Models\Project::all();
        $users = User::all();
        $roles = \App\Models\Role::all();

        // 選択済み担当者ID
        $selectedAssignees = $task->assignees->pluck('id')->toArray();

        return view('tasks.edit', compact('task', 'projects', 'users', 'roles', 'selectedAssignees'));
    }

    /* ★タスク更新 */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'plans_date' => 'nullable|date',
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
            'start_date' => $request->start_date,
            'plans_date' => $request->plans_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

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