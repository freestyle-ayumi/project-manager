<?php

namespace App\Http\Controllers;

use App\Models\Task; // 追加: Task モデルを使用するために追記
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * タスク一覧を表示する
     */
    public function index()
    {
        // データベースからタスクデータを取得します
        // 関連するプロジェクト、担当ユーザー、作成ユーザーの情報も一緒に取得
        $tasks = Task::with('project', 'assignedUser', 'createdUser')->get();

        // 取得したデータをビューに渡して表示
        return view('tasks.index', compact('tasks'));
    }
}