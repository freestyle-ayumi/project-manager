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
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
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
            $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            
            $totalMins = 0;
            $daysWorked = 0;

            // 当月の有効な全レコードを取得
            $monthlyRecords = AttendanceRecord::where('user_id', $user->id)
                ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
                ->where('is_valid', true)
                ->get()
                ->groupBy(fn($r) => $r->timestamp->format('Y-m-d'));

            foreach ($monthlyRecords as $dateStr => $records) {
                // 退勤・出張終了レコードを探す
                $checkOut = $records->whereIn('type', ['check_out', 'business_trip_end'])->first();
                
                // 【指示反映】退勤済み(work_minutesあり)ならDB値を加算。なければリアルタイム計算。
                if ($checkOut && $checkOut->work_minutes !== null) {
                    $totalMins += (int)$checkOut->work_minutes;
                    $daysWorked++;
                } else {
                    $checkIn = $records->whereIn('type', ['check_in', 'business_trip_start'])->first();
                    if ($checkIn) {
                        $calc = AttendanceRecord::getUnifiedCalculation($dateStr, $user->id);
                        if ($calc && isset($calc['actual_minutes'])) {
                            $totalMins += (int)$calc['actual_minutes'];
                            $daysWorked++;
                        }
                    }
                }
            }

            // index.blade.php の @foreach($summaryData as $data) に対応
            $summaryData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'days_worked' => $daysWorked,
                'total_work_hours' => sprintf('%d:%02d', floor($totalMins / 60), $totalMins % 60),
                'total_basic_hours' => '---',
            ];
        }

        return view('admin.summary.index', compact('summaryData', 'month'));
    }

    public function show(Request $request, User $user)
    {
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // 祝日読み込み
        $holidays = [];
        $csvPath = storage_path('app/syukujitsu.csv');
        if (file_exists($csvPath)) {
            $file = new \SplFileObject($csvPath);
            $file->setFlags(\SplFileObject::READ_CSV);
            foreach ($file as $row) {
                if (isset($row[0])) {
                    try {
                        $holidays[] = Carbon::parse($row[0])->format('Y-m-d');
                    } catch (\Exception $e) {}
                }
            }
        }

        $attendances = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->where('is_valid', true)
            ->orderBy('timestamp', 'asc')
            ->get()
            ->groupBy(fn($r) => $r->timestamp->format('Y-m-d'));

        $dailyData = [];
        $daysWorked = 0; $absentDays = 0; $paidHolidays = 0; $subHolidays = 0;
        $totalAllMins = 0; $totalOverMins = 0; $totalNightMins = 0;

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayRecords = $attendances->get($dateStr);
            $isHoliday = in_array($dateStr, $holidays);

            $dayInfo = [
                'date' => $date->day,
                'day' => ['日','月','火','水','木','金','土'][$date->dayOfWeek],
                'in' => '', 'out' => '', 'basic' => '', 'early' => '', 'over' => '', 'night' => '', 'total' => '',
                'note' => $isHoliday ? '祝日' : '',
            ];

            if ($dayRecords) {
                $inRec = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
                $outRec = $dayRecords->whereIn('type', ['check_out', 'business_trip_end'])->last();

                // 休暇判定
                if ($dayRecords->contains(fn($r) => str_contains($r->note ?? '', '有休'))) {
                    $paidHolidays++; $dayInfo['note'] = '有給休暇';
                } elseif ($dayRecords->contains(fn($r) => str_contains($r->note ?? '', '代休'))) {
                    $subHolidays++; $dayInfo['note'] = '代休';
                } elseif ($inRec && $outRec) {
                    $daysWorked++;
                    $dayInfo['in'] = $inRec->timestamp->format('H:i');
                    $dayInfo['out'] = $outRec->timestamp->format('H:i');

                    // 保存された値があるか確認
                    if ($outRec->work_minutes !== null) {
                        $work = $outRec->work_minutes;
                        $night = $outRec->night_minutes ?? 0;
                        $over = $outRec->overtime_minutes ?? 0;
                        $basic = $work - $over;
                    } else {
                        // 未保存データは新ロジックで再計算
                        $calc = AttendanceRecord::getUnifiedCalculation($dateStr, $user->id);
                        $work = $calc['actual_minutes'] ?? 0;
                        $night = $calc['midnight_minutes'] ?? 0;
                        $over = $calc['overtime_minutes'] ?? 0;
                        $basic = $calc['basic_minutes'] ?? 0;
                    }

                    $format = fn($m) => $m > 0 ? sprintf('%d:%02d', floor($m/60), $m%60) : '';
                    $dayInfo['basic'] = $format($basic);
                    $dayInfo['over']  = $format($over);
                    $dayInfo['night'] = $format($night);
                    $dayInfo['total'] = $format($work);

                    $totalAllMins += $work;
                    $totalOverMins += $over;
                    $totalNightMins += $night;
                }
            } else {
                if (!$date->isWeekend() && !$isHoliday && $date->isPast() && !$date->isToday()) {
                    $absentDays++; $dayInfo['note'] = '欠勤';
                }
            }
            $dailyData[] = $dayInfo;
        }

        $formatTotal = fn($m) => sprintf('%02d:%02d:00', floor($m/60), $m%60);
        $monthlySummary = [
            'days_worked' => $daysWorked,
            'absent_days' => $absentDays,
            'paid_holidays' => $paidHolidays,
            'sub_holidays' => $subHolidays,
            'total_work_time' => $formatTotal($totalAllMins),
            'total_early_over' => $formatTotal($totalOverMins),
            'total_night' => $formatTotal($totalNightMins),
        ];

        return view('admin.summary.show', compact('user', 'selectedMonth', 'dailyData', 'monthlySummary'));
    }

    public function download(Request $request, User $user)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $grouped = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->where('is_valid', true)
            ->get()
            ->groupBy(fn($r) => $r->timestamp->format('Y-m-d'));

        $fileName = "{$month}_{$user->name}_勤務表.csv";

        return new StreamedResponse(function () use ($user, $startOfMonth, $endOfMonth, $grouped) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['日付', '曜日', '場所/備考', '出勤', '中抜け', '戻り', '退勤', '実労働時間', '深夜', '残業']);

            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dayRecords = $grouped->get($dateStr, collect());

                $checkIn = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
                $breakStart = $dayRecords->firstWhere('type', 'break_start');
                $breakEnd = $dayRecords->firstWhere('type', 'break_end');
                $checkOut = $dayRecords->whereIn('type', ['check_out', 'business_trip_end'])->last();

                // 保存値があればそれを利用、なければ再計算
                if ($checkOut && $checkOut->work_minutes !== null) {
                    $work = sprintf('%d:%02d', floor($checkOut->work_minutes/60), $checkOut->work_minutes%60);
                    $night = sprintf('%d:%02d', floor(($checkOut->night_minutes ?? 0)/60), ($checkOut->night_minutes ?? 0)%60);
                    $over = sprintf('%d:%02d', floor(($checkOut->overtime_minutes ?? 0)/60), ($checkOut->overtime_minutes ?? 0)%60);
                } else {
                    $calc = AttendanceRecord::getUnifiedCalculation($dateStr, $user->id);
                    $work = $calc ? $calc['actual_hours'] : '---';
                    $night = $calc ? sprintf('%d:%02d', floor($calc['midnight_minutes']/60), $calc['midnight_minutes']%60) : '---';
                    $over = $calc ? sprintf('%d:%02d', floor($calc['overtime_minutes']/60), $calc['overtime_minutes']%60) : '---';
                }

                fputcsv($handle, [
                    $date->format('Y/m/d'),
                    ['日','月','火','水','木','金','土'][$date->dayOfWeek],
                    $checkIn ? ($checkIn->is_business_trip ? "[出張] {$checkIn->note}" : ($checkIn->location->name ?? '---')) : '',
                    $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                    $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                    $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                    $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                    $work,
                    $night,
                    $over
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}