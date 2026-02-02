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

    // ログインユーザーのタスク（絞り込みなし、全件）
    $myTasks = Task::whereExists(function($query) {
        $query->select('*')
            ->from('task_user')
            ->whereColumn('task_user.task_id', 'tasks.id')
            ->where('task_user.user_id', auth()->id());
    })->with(['project', 'assignees'])->get();

    // 他のユーザー（tasksも絞り込みなし、全件）
    $userQuery = User::with(['tasks' => function($query) {
        $query->with('assignees');
    }])
    ->where('id', '!=', auth()->id());

    $users = $userQuery->get();

    // フィルターボタン用（そのまま）
    $allUsersForFilter = User::all();
    $allRolesForFilter = \App\Models\Role::all();

    return view('tasks.index', compact(
        'today',
        'endDate',
        'myTasks',
        'users',
        'allUsersForFilter',
        'allRolesForFilter',
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
        $projects = \App\Models\Project::orderBy('start_date', 'desc')->get();
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
            'start_time' => 'nullable|date_format:H:i',
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
            'start_time' => $request->start_time,
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
            $task->assignees()->attach($assigneeIds);
        }

        // URLの保存（複数対応）
        if ($request->has('urls')) {
            foreach ($request->input('urls') as $urlData) {
                if (!empty($urlData['url'])) {  // URLが空でなければ保存
                    $task->urls()->create([
                        'url'   => $urlData['url'],
                        'title' => $urlData['title'] ?? null,
                        'memo'  => $urlData['memo'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました');
    }

    /* ★タスク編集フォーム */
    public function edit(Task $task)
    {
        $task->load('urls');
        $projects = \App\Models\Project::orderBy('start_date', 'desc')->get();
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
            'project_id'   => 'required|exists:projects,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'start_date'   => 'required|date',
            'start_time'   => 'nullable|date_format:H:i',
            'plans_date'   => 'nullable|date',
            'due_date'     => 'nullable|date',
            'status'       => 'required|string',
            'priority'     => 'nullable|string',
            'assignees'    => 'nullable|array',
            'assignees.*'  => 'exists:users,id',
        ]);

        // 基本情報の更新
        $task->update([
            'project_id'   => $request->project_id,
            'name'         => $request->name,
            'description'  => $request->description,
            'start_date'   => $request->start_date,
            'start_time'   => $request->start_time,
            'plans_date'   => $request->plans_date,
            'due_date'     => $request->due_date,
            'status'       => $request->status,
            'priority'     => $request->priority,
        ]);

        // 担当者の同期（重複エラーを防ぐために sync() を使用）
        $assigneeIds = $request->assignees ?? [];

        // ロールからユーザーを追加する場合
        if ($request->has('roles')) {
            $roleUserIds = User::whereIn('role_id', $request->roles)->pluck('id')->toArray();
            $assigneeIds = array_merge($assigneeIds, $roleUserIds);
        }

        // 重複を排除
        $assigneeIds = array_unique($assigneeIds);

        // sync() で現在の担当者を完全に置き換え（追加・削除を自動処理）
        $task->assignees()->sync($assigneeIds);

        // URLの保存（複数対応） - 既存コードをそのまま
        if ($request->has('urls')) {
            $submittedIds = [];

            foreach ($request->input('urls') as $urlData) {
                if (!empty($urlData['url'])) {
                    $data = [
                        'task_id' => $task->id,
                        'url'     => $urlData['url'],
                        'title'   => $urlData['title'] ?? null,
                        'memo'    => $urlData['memo'] ?? null,
                    ];

                    if (empty($urlData['id'])) {
                        // 新規作成
                        $created = $task->urls()->create($data);
                        $submittedIds[] = $created->id;
                    } else {
                        // 更新
                        $task->urls()->where('id', $urlData['id'])->update($data);
                        $submittedIds[] = $urlData['id'];
                    }
                }
            }

            // 送信されなかったURLは削除
            $task->urls()->whereNotIn('id', $submittedIds)->delete();
        }

        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました。');
    }

    /* ★タスク削除 */
    public function destroy(Task $task)
    {
        $task->assignees()->detach();
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました。');
    }

    /* ★タスクステータス変更 */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $oldStatus = $task->status;
        $task->status = $request->status;
        $task->save();

        return response()->json([
            'success' => true,
            'oldStatus' => $oldStatus,
            'newStatus' => $task->status,
        ]);
    }

}