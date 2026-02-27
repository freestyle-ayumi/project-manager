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

        // 1. 自分の打刻履歴（最新20件、降順） - 既存のまま
        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->with('location')
            ->orderBy('timestamp', 'desc')
            ->take(20)
            ->get();

        // 2. 今後のプロジェクト（開催前・開催中：今日を含む） - 既存のまま
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

        // 3. 未承認の経費 - 既存のまま
        $pendingExpenses = Expense::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['申請中', '差し戻し']);
            })
            ->with(['status', 'project'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // 4. 割り当てられた未完了タスク - 既存のまま
        $assignedTasks = Task::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', '!=', '完了')
        ->with('project')
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();

        // 現在の出張状態を取得（最新のフラグ）
        $isBusinessTrip = $user->attendanceRecords()
            ->where('user_id', $user->id)
            ->latest('timestamp')
            ->first()?->is_business_trip ?? false;

        // 今日の出勤済みフラグ
        $todayClockedIn = $user->attendanceRecords()
            ->whereDate('timestamp', now()->toDateString())
            ->whereIn('type', ['check_in', 'business_trip_end'])  // ← 出張終了も出勤扱い
            ->exists();

        // 退社済み判定
        $todayClockedOut = $user->attendanceRecords()
            ->whereDate('timestamp', now()->toDateString())
            ->where('type', 'check_out')
            ->exists();

        // 今週 + 前週の全日付を先に作成
        $dashboardDailyRecords = [];

        $currentWeekStart = $today->copy()->startOfWeek()->startOfDay();
        $previousWeekStart = $currentWeekStart->copy()->subWeek()->startOfDay();

        $weeks = [$currentWeekStart, $previousWeekStart];

        foreach ($weeks as $weekStart) {
            $weekEnd = $weekStart->copy()->endOfWeek()->endOfDay();

            // 全日付を先に作成（古い日付から新しい日付へ）
            $currentDate = $weekStart->copy();
            while ($currentDate->lte($weekEnd)) {
                $dateKey = $currentDate->toDateString();
                $dateFormatted = $currentDate->format('m/d (D)');

                $dashboardDailyRecords[$dateKey] = [
                    'date_formatted' => $dateFormatted,
                    'check_in' => '---',
                    'break_start' => '---',
                    'break_end' => '---',
                    'check_out' => '---',
                    'work_hours' => '---',
                    'location' => '---',
                    'is_business_trip' => false,
                ];

                $currentDate = $currentDate->addDay();
            }

            // 打刻データ取得（範囲を広めに）
            $weekRecords = AttendanceRecord::where('user_id', $user->id)
                ->where('timestamp', '>=', $weekStart->subDay()->startOfDay())
                ->where('timestamp', '<=', $weekEnd->addDay()->endOfDay())
                ->with('location')
                ->get();

            Log::info('取得した週レコード数: ' . $weekRecords->count());
            Log::info('weekStart: ' . $weekStart->toDateTimeString() . ' ~ ' . $weekEnd->toDateTimeString());

            // groupBy
            $grouped = $weekRecords->groupBy(function ($record) {
                return $record->timestamp->format('Y-m-d');
            });

            Log::info('groupBy キー: ' . implode(', ', $grouped->keys()->toArray()));

            foreach ($grouped as $dateString => $dayRecords) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
                    Log::warning('不正な日付キー: ' . $dateString);
                    continue;
                }

                $dateCarbon = Carbon::parse($dateString);

                // 出社欄：check_in または business_trip_start を優先
                $checkIn = $dayRecords->firstWhere('type', 'check_in') ?? $dayRecords->firstWhere('type', 'business_trip_start');

                // 退社欄：check_out または business_trip_end を優先
                $checkOut = $dayRecords->firstWhere('type', 'check_out') ?? $dayRecords->firstWhere('type', 'business_trip_end');

                $breakStart = $dayRecords->firstWhere('type', 'break_start');
                $breakEnd = $dayRecords->firstWhere('type', 'break_end');

                // 地点情報（出張時はメモ + 緯度経度を表示）
                $locationInfo = $dayRecords->firstWhere('type', 'business_trip_start') 
                    ?? $dayRecords->firstWhere('type', 'business_trip_end') 
                    ?? $dayRecords->firstWhere('type', 'check_in') 
                    ?? $dayRecords->first();

                if ($locationInfo && $locationInfo->is_business_trip) {
                    // address があれば優先、それ以外は note + 座標
                    if ($locationInfo->address) {
                        $location = $locationInfo->address;
                    } else {
                        $note = $locationInfo->note ? trim($locationInfo->note) : '出張';
                        $lat = $locationInfo->latitude ? round($locationInfo->latitude, 5) : null;
                        $lng = $locationInfo->longitude ? round($locationInfo->longitude, 5) : null;
                        $coord = ($lat && $lng) ? " ({$lat}, {$lng})" : " (位置不明)";
                        $location = $note . $coord;
                    }
                } else {
                    $location = $locationInfo && $locationInfo->location 
                        ? $locationInfo->location->name 
                        : '本社';
                }

                $hasEnd = $dayRecords->contains(fn($r) => in_array($r->type, ['check_out', 'business_trip_end']));

                $workHours = '---';

                if ($hasEnd) {
                    // 保存値があれば優先（最後の退勤レコードから取得）
                    $lastRecord = $dayRecords->last();
                    if ($lastRecord && $lastRecord->work_minutes !== null) {
                        $minutes = $lastRecord->work_minutes;
                        $h = floor($minutes / 60);
                        $m = $minutes % 60;
                        $workHours = sprintf('%d:%02d', $h, $m);
                    } else {
                        // 保存値がない場合は計算（古いデータ対応）
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

        // 1. すべての日付をキー（Y-m-d）でソート（古い→新しい）
        ksort($dashboardDailyRecords);

        // 2. 今週と前週を分離（古い週を上、新しい週を下にしたいので前週を先頭に）
        $previousWeekDates = [];
        $currentWeekDates = [];

        $currentWeekStartStr = $currentWeekStart->toDateString();
        $previousWeekStartStr = $previousWeekStart->toDateString();

        foreach ($dashboardDailyRecords as $dateKey => $data) {
            if ($dateKey < $currentWeekStartStr) {
                $previousWeekDates[$dateKey] = $data;  // 前週を先頭に
            } else {
                $currentWeekDates[$dateKey] = $data;   // 今週を後ろに
            }
        }

        // 前週を上、今週を下に結合（各週内は古い→新しい）
        $dashboardDailyRecords = $previousWeekDates + $currentWeekDates;

        // ビューに渡す
        return view('dashboard', compact(
            'attendanceRecords',
            'upcomingProjects',
            'pendingExpenses',
            'assignedTasks',
            'today',
            'dashboardDailyRecords',
            'isBusinessTrip',
            'todayClockedIn',
            'todayClockedOut'
        ));
    }
}