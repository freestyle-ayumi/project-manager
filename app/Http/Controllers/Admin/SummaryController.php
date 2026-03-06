<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SummaryController extends Controller
{
    // ページ表示制限
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user(); // ここでログインユーザーを取得

            // デバッグ用（もし動かない場合はここを確認）
            // \Log::info('Access Check:', ['id' => $user->id, 'role' => $user->role_id, 'dev' => $user->developer]);

            if ($user && ($user->developer == 1 || $user->role_id == 11)) {
                return $next($request);
            }

            abort(403, 'このページへのアクセス権限がありません。');
        });
    }

    public function index(Request $request)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $users = User::all();
        $summaryData = [];

        foreach ($users as $user) {
            // インデックス画面でもExcelロジックを反映させるための集計
            $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            
            $totalMins = 0;
            $daysWorked = 0;

            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $calc = AttendanceRecord::calculateExcelSplit($date->format('Y-m-d'), $user->id);
                if ($calc) {
                    $daysWorked++;
                    $totalMins += $calc['total'];
                }
            }

            $hours = floor($totalMins / 60);
            $minutes = $totalMins % 60;
            $totalDisplay = sprintf('%d:%02d', $hours, $minutes);

            $summaryData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'total_hours' => $totalDisplay,
                'days_worked' => $daysWorked,
            ];
        }

        return view('admin.summary.index', compact('summaryData', 'month'));
    }

    public function show(Request $request, User $user)
    {
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // --- 祝日CSVの読み込み ---
        $holidays = [];
        $csvPath = storage_path('app/syukujitsu.csv');
        if (file_exists($csvPath)) {
            $file = new \SplFileObject($csvPath);
            $file->setFlags(\SplFileObject::READ_CSV);
            foreach ($file as $row) {
                if (isset($row[0])) {
                    // CSVの1列目が日付（YYYY/MM/DD等）であることを想定
                    try {
                        $holidayDate = Carbon::parse($row[0])->format('Y-m-d');
                        $holidays[] = $holidayDate;
                    } catch (\Exception $e) {
                        continue; // ヘッダー行などはスキップ
                    }
                }
            }
        }

        $attendances = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->orderBy('timestamp', 'asc')
            ->get()
            ->groupBy(fn($record) => $record->timestamp->format('Y-m-d'));

        $dailyData = [];
        $daysWorked = 0;
        $absentDays = 0;
        $paidHolidays = 0;
        $subHolidays = 0;
        
        $totalAllMinutes = 0;
        $totalEOMinutes = 0;
        $totalNightMinutes = 0;

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $calc = AttendanceRecord::calculateExcelSplit($dateStr, $user->id);
            $dayRecords = $attendances->get($dateStr);
            $isHoliday = in_array($dateStr, $holidays); // 祝日判定

            $dayInfo = [
                'date' => $date->day,
                'day' => ['日','月','火','水','木','金','土'][$date->dayOfWeek],
                'in' => '', 'out' => '', 'basic' => '', 'early' => '', 'over' => '', 'night' => '', 'total' => '',
                'note' => $isHoliday ? '祝日' : '', // 祝日の場合は備考に表示
            ];

            if ($dayRecords) {
                // --- 打刻がある場合の処理 ---
                $inRec = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
                $outRec = $dayRecords->whereIn('type', ['check_out', 'business_trip_end'])->first();

                $hasPaidHoliday = $dayRecords->contains(fn($r) => str_contains($r->note ?? '', '有休'));
                $hasSubHoliday = $dayRecords->contains(fn($r) => str_contains($r->note ?? '', '代休'));

                if ($hasPaidHoliday) {
                    $paidHolidays++;
                    $dayInfo['note'] = '有給休暇';
                } elseif ($hasSubHoliday) {
                    $subHolidays++;
                    $dayInfo['note'] = '代休';
                } elseif ($inRec && $outRec && $calc) {
                    $daysWorked++;
                    $dayInfo['in'] = $inRec->timestamp->format('H:i');
                    $dayInfo['out'] = $outRec->timestamp->format('H:i');
                    
                    $format = fn($m) => $m > 0 ? sprintf('%d:%02d', floor($m/60), $m%60) : '';
                    $dayInfo['basic'] = $format($calc['basic']);
                    $dayInfo['early'] = $format($calc['early']);
                    $dayInfo['over']  = $format($calc['over']);
                    $dayInfo['night'] = $format($calc['night']);
                    $dayInfo['total'] = $format($calc['total']);

                    $totalAllMinutes   += $calc['total'];
                    $totalEOMinutes    += ($calc['early'] + $calc['over']);
                    $totalNightMinutes += $calc['night'];
                }
            } else {
                // --- 打刻がない場合の欠勤判定 ---
                // 判定条件：土日ではない ＆ 祝日ではない ＆ 今日より前（昨日の分まで）
                if (!$date->isWeekend() && !$isHoliday && $date->isPast() && !$date->isToday()) {
                    $absentDays++;
                    $dayInfo['note'] = '欠勤';
                }
            }
            $dailyData[] = $dayInfo;
        }

        // --- 以下、サマリー作成とView返却（前回と同じ） ---
        $formatTotal = fn($m) => sprintf('%02d:%02d:00', floor($m/60), $m%60);
        $monthlySummary = [
            'days_worked' => $daysWorked,
            'absent_days' => $absentDays,
            'paid_holidays' => $paidHolidays,
            'sub_holidays' => $subHolidays,
            'total_work_time' => $formatTotal($totalAllMinutes),
            'total_early_over' => $formatTotal($totalEOMinutes),
            'total_night' => $formatTotal($totalNightMinutes),
        ];

        return view('admin.summary.show', compact('user', 'selectedMonth', 'dailyData', 'monthlySummary'));
    }

    public function download(Request $request, User $user)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        
        // 1. 指定した月の開始日と終了日を取得
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // 2. その月の全レコードを取得
        $attendances = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->orderBy('timestamp', 'asc')
            ->get();

        // 3. 日付ごとにグループ化（history機能のロジックを流用）
        $grouped = $attendances->groupBy(function ($record) {
            return $record->timestamp->format('Y-m-d');
        });

        $fileName = "{$month}_{$user->name}_勤務表.csv";

        return new StreamedResponse(function () use ($user, $startOfMonth, $endOfMonth, $grouped) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM

            fputcsv($handle, ['日付', '曜日', '場所/備考', '出勤', '中抜け', '戻り', '退勤', '実労働時間']);

            // 月の初日から末日までループ
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dayRecords = $grouped->get($dateStr, collect());

                $checkIn = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
                $breakStart = $dayRecords->firstWhere('type', 'break_start');
                $breakEnd = $dayRecords->firstWhere('type', 'break_end');
                $checkOut = $dayRecords->whereIn('type', ['check_out', 'business_trip_end'])->first();

                // 労働時間の計算（モデルの静的メソッドを使用）
                $workHours = AttendanceRecord::calculateDailyWorkHours($dateStr, $user->id);

                fputcsv($handle, [
                    $date->format('Y/m/d'),
                    ['日','月','火','水','木','金','土'][$date->dayOfWeek],
                    $checkIn ? ($checkIn->is_business_trip ? "[出張] {$checkIn->note}" : ($checkIn->location->name ?? '---')) : '',
                    $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                    $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                    $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                    $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                    $workHours
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
    
}