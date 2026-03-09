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
            // 出張中判定ロジック
            $latestRecord = $user->attendanceRecords()
                ->where('is_valid', true) // ★ここが重要
                ->latest('timestamp')
                ->first();
            if ($latestRecord) {
                $isBusinessTripDay = $latestRecord->is_business_trip;
            }
            // 今日の終了打刻があればfalse
            if ($user->attendanceRecords()->whereDate('timestamp', $today)->where('type', 'business_trip_end')->exists()) {
                $isBusinessTripDay = false;
            }

            if ($isBusinessTripDay) {
                $message = '打刻成功！（出張中）';
                $dbType = match($type) {
                    'in' => 'check_in',
                    'out' => 'check_out',
                    'break_start' => 'break_start',
                    'break_end' => 'break_end',
                };
            } else {
                if (is_null($latitude) || is_null($longitude)) {
                    return response()->json(['success' => false, 'message' => '位置情報が取得できませんでした'], 422);
                }

                $locations = Location::all();
                $minDistance = INF;

                foreach ($locations as $location) {
                    $dist = $this->haversineDistance((float)$latitude, (float)$longitude, (float)$location->latitude, (float)$location->longitude);
                    if ($dist < $minDistance) {
                        $minDistance = $dist;
                        $matchedLocation = $location;
                    }
                }

                $dbType = match($type) {
                    'in' => 'check_in',
                    'out' => 'check_out',
                    'break_start' => 'break_start',
                    'break_end' => 'break_end',
                };

                if ($matchedLocation) {
                    $distance = round($minDistance);
                    $allowed = (float)$matchedLocation->allowed_radius;
                    
                    // 距離判定（100mのバッファ）
                    $isInRange = ($distance <= ($allowed + 100));

                    if (!$isInRange) {
                        // 【重要】範囲外なら変数を null に上書きする
                        $matchedLocation = null; 
                        $isValid = false;
                        $message = '勤務地範囲外です。';
                    } else {
                        $isValid = true;
                        $message = '打刻成功！';
                    }


                    Log::info("打刻判定ログ: " . ($matchedLocation ? "拠点: {$matchedLocation->name}" : "【範囲外】") . " 距離: {$distance}m, 結果=" . ($isValid ? 'OK' : 'NG'));
                } else {
                    $isValid = false;
                    $message = '勤務地が見つかりません。';
                }
            }
        }

        // DB保存（ここで一度だけ実行）
        $createdRecord = AttendanceRecord::create([
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

        // 勤務時間計算
        if (in_array($dbType, ['check_out', 'business_trip_end'])) {
            $workHours = AttendanceRecord::calculateDailyWorkHours($today, $user->id);

            if ($workHours !== '0:00' && $workHours !== '---') {
                $parts = explode(':', $workHours);
                $totalMinutes = ((int)$parts[0] * 60) + (int)$parts[1];
                $createdRecord->work_minutes = $totalMinutes;
                $createdRecord->save();
                Log::info("勤務時間を保存しました: ID={$createdRecord->id}, 分={$totalMinutes}");
            }
        }

        return response()->json([
            'success' => $isValid,
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

        $monthDates = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $monthDates[$date->format('Y-m-d')] = [
                'check_in' => '---',
                'break_start' => '---',
                'break_end' => '---',
                'check_out' => '---',
                'work_hours' => '---',
                'location' => null,
                'is_business_trip' => false,
            ];
        }

        $attendances = $user->attendanceRecords()
            ->whereBetween('timestamp', [$startOfMonth, $endOfMonth])
            ->with('location')
            ->orderBy('timestamp', 'asc')
            ->get();

        $grouped = $attendances->groupBy(function ($record) {
            return $record->timestamp->format('Y-m-d');
        });

        foreach ($grouped as $date => $dayRecords) {
            // ★ 修正：is_valid が true のレコードの中から「出勤」系を探す
            $validDayRecords = $dayRecords->where('is_valid', true);

            $checkIn = $validDayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
            $breakStart = $validDayRecords->firstWhere('type', 'break_start');
            $breakEnd = $validDayRecords->firstWhere('type', 'break_end');
            $checkOut = $validDayRecords->whereIn('type', ['check_out', 'business_trip_end'])->first();
            
            // 表示の基準となるメインレコードも有効なものから選ぶ
            $mainRecord = $checkIn; 

            $monthDates[$date] = [
                'check_in' => $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                'break_start' => $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                'break_end' => $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                'check_out' => $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                'work_hours' => AttendanceRecord::calculateDailyWorkHours($date, $user->id),
                // 有効なレコードがない場合は location を null にする
                'location' => $mainRecord ? $mainRecord->display_location : null,
                'is_business_trip' => $mainRecord ? (bool)$mainRecord->is_business_trip : false,
                'note' => $mainRecord ? $mainRecord->note : null,
            ];
        }

        $dailyRecords = collect($monthDates);

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i);
            $months[$month->format('Y-m')] = $month->format('Y年n月');
        }

        return view('attendance.history', compact('dailyRecords', 'selectedMonth', 'months', 'user'));
    }

    public function fixMissing(Request $request)
    {
        $request->validate([
            'record_id' => 'required|exists:attendance_records,id',
            'time' => 'required',
        ]);

        $oldRecord = AttendanceRecord::findOrFail($request->record_id);
        
        // 前日の日付と入力された時間を結合
        $checkoutTimestamp = Carbon::parse($oldRecord->timestamp->format('Y-m-d') . ' ' . $request->time);

        // 新しい退勤レコードを作成
        AttendanceRecord::create([
            'user_id' => Auth::id(),
            // 出張中なら business_trip_end、通常なら check_out
            'type' => (strpos($oldRecord->type, 'business_trip') !== false) ? 'business_trip_end' : 'check_out',
            'timestamp' => $checkoutTimestamp,
            'is_business_trip' => $oldRecord->is_business_trip,
            'note' => '未打刻のため手入力', 
            'is_valid' => true,
        ]);

        // 勤務時間の再計算
        AttendanceRecord::calculateDailyWorkHours($oldRecord->timestamp->format('Y-m-d'), Auth::id());

        return response()->json(['success' => true]);
    }

    public static function getButtonStatus($user)
    {
        // 有効な打刻（失敗していない打刻）の中で最新のものを取得
        $latest = $user->attendanceRecords()
            ->where('is_valid', true)
            ->latest('timestamp')
            ->first();

        if (!$latest) {
            return 'can_check_in'; // 打刻が一つもない＝出社可能
        }

        // 最新の有効な打刻タイプによって、次に押せるボタンを判定
        return match ($latest->type) {
            'check_in', 'break_end', 'business_trip_start' => 'can_check_out', // 出勤中＝退勤・中抜けが可能
            'break_start' => 'can_break_end', // 中抜け中＝戻りが可能
            default => 'can_check_in', // 退勤済み＝出社が可能
        };
    }
}