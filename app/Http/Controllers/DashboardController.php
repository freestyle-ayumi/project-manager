<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Expense;
use App\Models\Task;
use Carbon\Carbon;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. 自分の打刻履歴（最新20件、降順）
        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->with('location')
            ->orderBy('timestamp', 'desc')
            ->take(20)
            ->get();

        // 2. 今後のプロジェクト
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

        // 3. 未承認の経費
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

        // --- ここからボタン判定ロジック（is_validを考慮） ---

        // 現在の出張状態を取得（有効な最新フラグ）
        $isBusinessTrip = $user->attendanceRecords()
            ->where('is_valid', true) // ← 有効な打刻のみ
            ->latest('timestamp')
            ->first()?->is_business_trip ?? false;

        // 今日の出勤済みフラグ
        $todayClockedIn = $user->attendanceRecords()
            ->where('is_valid', true) // ← 有効な打刻のみ
            ->whereDate('timestamp', now()->toDateString())
            ->whereIn('type', ['check_in', 'business_trip_end'])
            ->exists();

        // 退社済み判定
        $todayClockedOut = $user->attendanceRecords()
            ->where('is_valid', true) // ← 有効な打刻のみ
            ->whereDate('timestamp', now()->toDateString())
            ->where('type', 'check_out')
            ->exists();

        // 最新の有効なレコードを取得（退勤忘れ判定用）
        $latestRecord = $user->attendanceRecords()
            ->where('is_valid', true) // ← 有効な打刻のみ
            ->latest('timestamp')
            ->first();

        // 前日の退勤忘れがあるか判定
        $needsFix = false;
        $unclosedRecord = null;

        if ($latestRecord && !in_array($latestRecord->type, ['check_out', 'business_trip_end'])) {
            if ($latestRecord->timestamp->format('Y-m-d') < now()->toDateString()) {
                $needsFix = true;
                $unclosedRecord = $latestRecord;
            }
        }

        // Bladeで使うボタン状態変数の生成
        $status = 'can_check_in';
        if ($needsFix) {
            $status = 'needs_fix';
        } elseif ($todayClockedOut) {
            $status = 'already_checked_out';
        } elseif ($todayClockedIn || $isBusinessTrip) {
            $status = 'already_checked_in';
        }
        
        // --- ここまで判定ロジック ---

        // 今週 + 前週の履歴表示用データ作成
        $dashboardDailyRecords = [];
        $currentWeekStart = $today->copy()->startOfWeek()->startOfDay();
        $previousWeekStart = $currentWeekStart->copy()->subWeek()->startOfDay();
        $weeks = [$currentWeekStart, $previousWeekStart];

        foreach ($weeks as $weekStart) {
            $weekEnd = $weekStart->copy()->endOfWeek()->endOfDay();

            $currentDate = $weekStart->copy();
            while ($currentDate->lte($weekEnd)) {
                $dateKey = $currentDate->toDateString();
                $dashboardDailyRecords[$dateKey] = [
                    'date_formatted' => $currentDate->format('m/d (D)'),
                    'check_in' => '---', 'break_start' => '---', 'break_end' => '---',
                    'check_out' => '---', 'work_hours' => '---', 'location' => '---',
                    'is_business_trip' => false,
                ];
                $currentDate = $currentDate->addDay();
            }

            $weekRecords = AttendanceRecord::where('user_id', $user->id)
                ->where('is_valid', true) // ← ここを追加！無効なデータは履歴に出さない
                ->where('timestamp', '>=', $weekStart->copy()->subDay()->startOfDay())
                ->where('timestamp', '<=', $weekEnd->copy()->addDay()->endOfDay())
                ->with('location')
                ->get();

            $grouped = $weekRecords->groupBy(fn($record) => $record->timestamp->format('Y-m-d'));

            foreach ($grouped as $dateString => $dayRecords) {
                if (!isset($dashboardDailyRecords[$dateString])) continue;

                $dateCarbon = Carbon::parse($dateString);
                $checkIn = $dayRecords->firstWhere('type', 'check_in') ?? $dayRecords->firstWhere('type', 'business_trip_start');
                $checkOut = $dayRecords->firstWhere('type', 'check_out') ?? $dayRecords->firstWhere('type', 'business_trip_end');
                $breakStart = $dayRecords->firstWhere('type', 'break_start');
                $breakEnd = $dayRecords->firstWhere('type', 'break_end');

                $locationInfo = $dayRecords->whereIn('type', ['business_trip_start', 'business_trip_end', 'check_in'])->first() ?? $dayRecords->first();

                if ($locationInfo && $locationInfo->is_business_trip) {
                    $location = $locationInfo->address ?: ($locationInfo->note ? trim($locationInfo->note) : '出張');
                } else {
                    $location = $locationInfo?->location?->name ?? '本社';
                }

                $hasEnd = $dayRecords->contains(fn($r) => in_array($r->type, ['check_out', 'business_trip_end']));
                $workHours = '---';

                if ($hasEnd) {
                    $lastRec = $dayRecords->last();
                    if ($lastRec && $lastRec->work_minutes !== null) {
                        $h = floor($lastRec->work_minutes / 60);
                        $m = $lastRec->work_minutes % 60;
                        $workHours = sprintf('%d:%02d', $h, $m);
                    } else {
                        $workHours = AttendanceRecord::calculateDailyWorkHours($dateString, $user->id);
                    }
                }

                $dashboardDailyRecords[$dateString] = [
                    'date_formatted' => $dateCarbon->format('m/d (D)'),
                    'check_in' => $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                    'break_start' => $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                    'break_end' => $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                    'check_out' => $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                    'work_hours' => $workHours,
                    'location' => $location,
                    'is_business_trip' => $locationInfo ? $locationInfo->is_business_trip : false,
                ];
            }
        }

        ksort($dashboardDailyRecords);
        $previousWeekDates = array_filter($dashboardDailyRecords, fn($k) => $k < $currentWeekStart->toDateString(), ARRAY_FILTER_USE_KEY);
        $currentWeekDates = array_filter($dashboardDailyRecords, fn($k) => $k >= $currentWeekStart->toDateString(), ARRAY_FILTER_USE_KEY);
        $dashboardDailyRecords = $previousWeekDates + $currentWeekDates;

        return view('dashboard', compact(
            'attendanceRecords', 'upcomingProjects', 'pendingExpenses', 'assignedTasks', 'today',
            'dashboardDailyRecords', 'isBusinessTrip', 'todayClockedIn', 'todayClockedOut',
            'needsFix', 'unclosedRecord', 'status'
        ));
    }
}