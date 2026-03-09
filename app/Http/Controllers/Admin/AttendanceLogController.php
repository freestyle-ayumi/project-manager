<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    // 勤怠ログ一覧(全体)
    public function logIndex()
    {
        // 開発者(1)または経理(11)以外はアクセス拒否
        if (auth()->user()->developer != 1 && auth()->user()->role_id != 11) {
            abort(403);
        }

        // AttendanceRecord モデルを使ってログを取得
        // with('user') を入れることで、誰のログか名前を表示できるようにします
        $logs = \App\Models\AttendanceRecord::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.attendance.log', compact('logs'));
    }
    // 勤怠ログ一覧（個人絞り込み）
    public function logShow(\App\Models\User $user)
    {
        if (auth()->user()->developer != 1 && auth()->user()->role_id != 11) {
            abort(403);
        }

        $logs = \App\Models\AttendanceRecord::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.attendance.log', compact('logs', 'user'));
    }

    public function index()
    {
        // 全ユーザーのログを降順（新しい順）で取得
        $logs = AttendanceRecord::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.attendance.log', compact('logs'));
    }

    public function show(User $user)
    {
        // 特定のユーザーのログを取得
        $logs = AttendanceRecord::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.attendance.log', compact('logs', 'user'));
    }
}