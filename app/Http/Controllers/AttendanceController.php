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
            $latestRecord = $user->attendanceRecords()->latest('timestamp')->first();
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
                    $allowed = (float)$matchedLocation->allowed_radius; // ここで確実に定義
                    
                    // 判定ロジック：500km離れていれば必ず false になるはずです
                    $isValid = ($distance <= ($allowed + 100));
                    
                    // 【重要】ログに出力して確認
                    Log::info("打刻判定ログ: 拠点={$matchedLocation->name}, 距離={$distance}m, 許容={$allowed}m, 結果=" . ($isValid ? 'OK' : 'NG'));
                } else {
                    $isValid = false;
                }
                
                if (!$isValid) {
                    $message = '勤務地範囲外です。';
                    if ($type === 'in') {
                        $message .= '「出社」は登録地点で行ってください。';
                    }
                } else {
                    $message = '打刻成功！';
                }
            }
        }

        // DB保存（isValidがfalseでも保存される）
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
            // その日の勤務時間を取得（"08:30" などの形式で返ってくる想定）
            $workHours = AttendanceRecord::calculateDailyWorkHours($today, $user->id);

            if ($workHours !== '0:00' && $workHours !== '---') {
                // "H:i" 形式を分解して「分」に変換
                $parts = explode(':', $workHours);
                $totalMinutes = ((int)$parts[0] * 60) + (int)$parts[1];

                // クエリビルダではなく、モデルインスタンスを直接更新して保存
                $createdRecord->work_minutes = $totalMinutes;
                $createdRecord->save();
                
                // ログに出力して保存されたか確認（デバッグ用）
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
            // ★修正：出勤レコード または 出張開始レコードを取得
            $checkIn = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();
            
            $breakStart = $dayRecords->firstWhere('type', 'break_start');
            $breakEnd = $dayRecords->firstWhere('type', 'break_end');
            
            // ★修正：退勤レコード または 出張終了レコードを取得
            $checkOut = $dayRecords->whereIn('type', ['check_out', 'business_trip_end'])->first();
            
            $mainRecord = $dayRecords->whereIn('type', ['check_in', 'business_trip_start'])->first();

            $monthDates[$date] = [
                'check_in' => $checkIn ? $checkIn->timestamp->format('H:i') : '---',
                'break_start' => $breakStart ? $breakStart->timestamp->format('H:i') : '---',
                'break_end' => $breakEnd ? $breakEnd->timestamp->format('H:i') : '---',
                'check_out' => $checkOut ? $checkOut->timestamp->format('H:i') : '---',
                'work_hours' => AttendanceRecord::calculateDailyWorkHours($date, $user->id),
                'location' => ($mainRecord && $mainRecord->location) ? $mainRecord->location->name : null,
                'is_business_trip' => $mainRecord ? (bool)$mainRecord->is_business_trip : false,
                // 出張中かどうかを判断しやすくするためにメモも渡すならここに追加（任意）
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
}