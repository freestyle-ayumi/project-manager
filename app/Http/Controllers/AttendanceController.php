<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AttendanceRecord;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:in,out,break_start,break_end,business_trip_start,business_trip_end',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        if ($request->type === 'business_trip_start') {
            $rules['note'] = 'required|string|max:500';
        }

        $request->validate($rules);

        $user = Auth::user();
        $type = $request->type;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $note = $request->note ?? null;

        $isValid = true;
        $matchedLocation = null;
        $distance = null;
        $message = '';

        $today = now()->format('Y-m-d');

        $isBusinessTripDay = false;

        if ($type === 'business_trip_start') {
            if (empty($note)) {
                return response()->json(['success' => false, 'message' => '出張時はメモを入力してください'], 422);
            }

            $message = '出張を開始しました';
            $dbType = 'business_trip_start';
            $isBusinessTripDay = true;
        } elseif ($type === 'business_trip_end') {
            $message = '出張を終了しました';
            $dbType = 'business_trip_end';
        } else {
            // 通常打刻 or 出張中打刻

            // ★最優先補完1★ 1つ前のレコードが終了打刻なら強制false
            $previousRecord = $user->attendanceRecords()
                ->latest('timestamp')
                ->first();

            if ($previousRecord && $previousRecord->type === 'business_trip_end') {
                $isBusinessTripDay = false;
            }

            // ★最優先補完2★ 今日の終了打刻があれば強制false
            $endedToday = $user->attendanceRecords()
                ->whereDate('timestamp', $today)
                ->where('type', 'business_trip_end')
                ->exists();

            if ($endedToday) {
                $isBusinessTripDay = false;
            }

            // ★最優先補完3★ 最新レコードのフラグを直接使う
            $latestRecord = $user->attendanceRecords()
                ->latest('timestamp')
                ->first();

            if ($latestRecord) {
                $isBusinessTripDay = $latestRecord->is_business_trip;
            }

            if ($isBusinessTripDay) {
                // 出張中 → 位置チェックなし
                $matchedLocation = null;
                $distance = null;
                $message = '打刻成功！（出張中）';
                $dbType = match($type) {
                    'in' => 'check_in',
                    'out' => 'check_out',
                    'break_start' => 'break_start',
                    'break_end' => 'break_end',
                };
            } else {
                // 通常 → 位置チェック必須
                if (is_null($latitude) || is_null($longitude)) {
                    return response()->json(['success' => false, 'message' => '位置情報が取得できませんでした'], 422);
                }

                $lat = (float) $latitude;
                $lng = (float) $longitude;

                $locations = Location::all();

                $matchedLocation = null;
                $distance = null;

                foreach ($locations as $location) {
                    $locLat = (float) $location->latitude;
                    $locLng = (float) $location->longitude;
                    $allowed = (float) $location->allowed_radius;

                    $dist = $this->haversineDistance($lat, $lng, $locLat, $locLng);
                    Log::debug("距離計算: 勤務地={$location->name}, 計算距離={$dist}m, 許容={$allowed}m");

                    if ($dist <= $allowed + 100) {  // テスト用に緩和
                        $matchedLocation = $location;
                        $distance = round($dist);
                        break;
                    }
                }

                $isValid = $matchedLocation !== null;
                $dbType = match($type) {
                    'in' => 'check_in',
                    'out' => 'check_out',
                    'break_start' => 'break_start',
                    'break_end' => 'break_end',
                };
                $message = $isValid ? '打刻成功！' : '勤務地範囲外です。打刻できません。';
            }
        }

        AttendanceRecord::create([
            'user_id' => $user->id,
            'location_id' => $matchedLocation ? $matchedLocation->id : null,
            'type' => $dbType,
            'timestamp' => now(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance' => $distance,
            'is_valid' => $isValid,
            'note' => $note,
            'is_business_trip' => $isBusinessTripDay,
        ]);

        // 勤務時間の保存
        if (in_array($dbType, ['check_out', 'business_trip_end'])) {
            Log::info("退勤系打刻検知: type={$dbType}, user_id={$user->id}");

            $today = now()->format('Y-m-d');
            Log::info("今日の日付: {$today}");

            $workHours = AttendanceRecord::calculateDailyWorkHours($today, $user->id);
            Log::info("計算結果 workHours: {$workHours}");

            if ($workHours !== '0:00' && $workHours !== '---') {
                Log::info("計算値が有効: {$workHours} → 分に変換開始");

                [$h, $m] = explode(':', $workHours);
                $workMinutes = ((int)$h * 60) + (int)$m;
                Log::info("変換後 workMinutes: {$workMinutes}");

                // 最新レコードをfreshで取得（キャッシュ回避）
                $latestRecord = AttendanceRecord::where('user_id', $user->id)
                    ->whereDate('timestamp', $today)
                    ->latest('timestamp')
                    ->first();

                if ($latestRecord) {
                    Log::info("保存対象レコード発見: id={$latestRecord->id}, type={$latestRecord->type}");

                    // Model update が効かない場合の回避策: DBファサードで直接更新
                    \DB::table('attendance_records')
                        ->where('id', $latestRecord->id)
                        ->update(['work_minutes' => $workMinutes]);

                    Log::info("work_minutes 保存成功: {$workMinutes}分 (id={$latestRecord->id})");

                    // 念のためモデルをリフレッシュして確認
                    $latestRecord->refresh();
                    Log::info("保存後確認: work_minutes = " . ($latestRecord->work_minutes ?? 'null'));
                } else {
                    Log::warning("今日のレコードが見つかりません: {$today}");
                }
            } else {
                Log::info("計算値が無効のため保存スキップ: {$workHours}");
            }
        } else {
            Log::info("退勤系打刻ではないため保存スキップ: type={$dbType}");
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // メートル

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function recent()
    {
        $user = Auth::user();
        $records = $user->attendanceRecords()
            ->with(['location', 'user'])
            ->latest('timestamp')
            ->take(20)
            ->get();

        return response()->json($records->map(function ($record) {
            $typeLabel = match($record->type) {
                'check_in'            => '出勤',
                'check_out'           => '退勤',
                'break_start'         => '中抜け',
                'break_end'           => '戻り',
                'business_trip_start' => '出張開始',
                'business_trip_end'   => '出張終了',
                default               => $record->type,
            };

            $location = $record->display_location ?? '---';
            $distance = $record->display_distance ?? '---';

            return [
                'timestamp'  => $record->timestamp->format('y/m/d H:i'),
                'type'       => $record->type,
                'type_label' => $typeLabel,
                'location'   => $location,
                'distance'   => $distance,
                'is_valid'   => $record->is_valid ? 1 : 0,
            ];
        }));
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        $selectedMonth = $request->input('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $attendances = $user->attendanceRecords()
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->with('location')
            ->orderBy('timestamp', 'desc')
            ->get();

        $dailyRecords = $attendances->groupBy(function ($record) {
            return $record->timestamp->format('Y-m-d');
        })->map(function ($dayRecords, $date) use ($user) {
            $checkIn = $dayRecords->firstWhere('type', 'check_in');
            $breakStart = $dayRecords->firstWhere('type', 'break_start');
            $breakEnd = $dayRecords->firstWhere('type', 'break_end');
            $checkOut = $dayRecords->firstWhere('type', 'check_out');

            return [
                'check_in' => $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                'break_start' => $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                'break_end' => $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                'check_out' => $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                'work_hours' => AttendanceRecord::calculateDailyWorkHours($date, $user->id),
            ];
        });

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i);
            $months[$month->format('Y-m')] = $month->format('Y年n月');
        }

        return view('attendance.history', compact(
            'dailyRecords',
            'selectedMonth',
            'months',
            'user'
        ));
    }
}