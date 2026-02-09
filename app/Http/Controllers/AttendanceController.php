<?php

namespace App\Http\Controllers;

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

        // 出張開始時だけnoteを必須にする
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

        $today = now()->format('Y-m-d');
        // 今日が出張期間内か判定（activeな出張レコードがあるか）
        $activeTrip = $user->businessTrips()->where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->where('end_date', '>=', $today)->orWhereNull('end_date');
            })->first();

        $isBusinessTripDay = !is_null($activeTrip);

        if ($type === 'business_trip_start') {
            // 出張開始：レコード作成
            if (empty($note)) {
                return response()->json(['success' => false, 'message' => '出張時はメモを入力してください'], 422);
            }
            $user->businessTrips()->create([
                'start_date' => $today,
                'note' => $note,
            ]);
            $message = '出張を開始しました';
            $dbType = 'business_trip_start';
        } elseif ($type === 'business_trip_end') {
            // 出張終了：activeなレコードのend_dateをセット
            if ($activeTrip) {
                $activeTrip->update(['end_date' => $today]);
                $message = '出張を終了しました';
                $dbType = 'business_trip_end';
            } else {
                return response()->json(['success' => false, 'message' => '出張中でありません'], 422);
            }
        } elseif ($isBusinessTripDay) {
            // 出張中の打刻（out, break_start, break_end, in）：範囲チェックスキップ
            $isValid = true;
            $matchedLocation = null;
            $distance = null;
            $dbType = match($type) {
                'in' => 'check_in',
                'out' => 'check_out',
                'break_start' => 'break_start',
                'break_end' => 'break_end',
                'business_trip_start' => 'business_trip_start',
                'business_trip_end' => 'business_trip_end',
            };
            $message = $isValid ? '打刻成功！（出張中）' : '打刻失敗';
        } else {
            // 通常打刻
            if (is_null($latitude) || is_null($longitude)) {
                return response()->json(['success' => false, 'message' => '位置情報が取得できませんでした'], 422);
            }

            $lat = $latitude;
            $lng = $longitude;

            $locations = Location::all();

            foreach ($locations as $location) {
                $dist = $this->haversineDistance($lat, $lng, $location->latitude, $location->longitude);
                Log::debug("距離計算: 勤務地={$location->name}, 計算距離={$dist}m, 許容={$location->allowed_radius}m");

                if ($dist <= $location->allowed_radius) {
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
        ]);

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
            ->with('location')
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

            $locationDisplay = $record->location ? $record->location->name : '範囲外';

            $distanceDisplay = $record->distance !== null ? $record->distance . ' m' : '-';

            // 出張開始/終了時の特別処理
            if (in_array($record->type, ['business_trip_start', 'business_trip_end'])) {
                $locationDisplay = $record->note ? $record->note : 'メモなし';

                // 距離欄を緯度経度に置き換え
                $distanceDisplay = $record->latitude && $record->longitude 
                    ? round($record->latitude, 4) . ', ' . round($record->longitude, 4)
                    : '位置不明';
            }

            return [
                'timestamp'  => $record->timestamp->format('y/m/d H:i'),
                'type'       => $record->type,
                'type_label' => $typeLabel,
                'location'   => $locationDisplay,
                'distance'   => $distanceDisplay,
                'is_valid'   => $record->is_valid,
            ];
        }));
    }
}