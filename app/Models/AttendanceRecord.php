<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
        'type',
        'timestamp',
        'latitude',
        'longitude',
        'distance',
        'is_valid',
        'note',
        'is_business_trip',
        'work_minutes',
    ];

    protected function casts(): array
    {
        return [
            'timestamp'        => 'datetime',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
            'is_business_trip' => 'boolean',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->type) {
                'check_in'            => '出勤',
                'check_out'           => '退勤',
                'break_start'         => '中抜け',
                'break_end'           => '戻り',
                'business_trip_start' => '出張開始',
                'business_trip_end'   => '出張終了',
                default               => $this->type ?? '不明',
            }
        );
    }

    protected function displayLocation(): Attribute
    {
        return Attribute::make(
            get: function () {
                $type = $this->type;

                if ($type === 'business_trip_start') {
                    $note = $this->note ? trim($this->note) : '（メモなし）';
                    $lat  = $this->latitude ? round($this->latitude, 4) : null;
                    $lng  = $this->longitude ? round($this->longitude, 4) : null;
                    $coord = ($lat && $lng) ? " ({$lat}, {$lng})" : '（位置不明）';
                    return $note . $coord;
                }

                if ($type === 'business_trip_end') {
                    $lat  = $this->latitude ? round($this->latitude, 4) : null;
                    $lng  = $this->longitude ? round($this->longitude, 4) : null;
                    $coord = ($lat && $lng) ? " ({$lat}, {$lng})" : '';
                    return '出張終了' . $coord;
                }

                if ($this->is_business_trip) {
                    $lat = $this->latitude ? round($this->latitude, 4) : null;
                    $lng = $this->longitude ? round($this->longitude, 4) : null;
                    $coord = ($lat && $lng) ? " ({$lat}, {$lng})" : '（位置不明）';
                    return "出張中打刻{$coord}";
                }

                if ($this->location) {
                    return $this->location->name;
                }

                return '範囲外';
            }
        );
    }

    protected function displayDistance(): Attribute
    {
        return Attribute::make(
            get: function () {
                $type = $this->type;

                if (in_array($type, ['business_trip_start', 'business_trip_end']) || $this->is_business_trip) {
                    if ($this->latitude && $this->longitude) {
                        return round($this->latitude, 4) . ', ' . round($this->longitude, 4);
                    }
                    return '位置不明';
                }

                return $this->distance !== null
                    ? number_format($this->distance) . ' m'
                    : '---';
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1日の勤務時間を計算（出社から退社までの時間 - 中抜け時間）
    public static function calculateDailyWorkHours($date, $userId)
    {
        $records = self::where('user_id', $userId)
                ->whereDate('timestamp', $date)
                ->where('is_valid', true) // ★ ここを追加
                ->orderBy('timestamp')
                ->get();

        if ($records->isEmpty()) {
            return '0:00';
        }

        // 出勤と退勤を特定
        $startTypes = ['check_in', 'business_trip_start'];
        $endTypes   = ['check_out', 'business_trip_end'];

        $firstIn  = $records->first(fn($r) => in_array($r->type, $startTypes));
        $lastOut  = $records->last(fn($r) => in_array($r->type, $endTypes));

        if (!$firstIn || !$lastOut) {
            return '0:00';
        }

        // タイムスタンプをUnix秒に変換（Carbonの比較問題を回避）
        $startTs = strtotime($firstIn->timestamp);
        $endTs   = strtotime($lastOut->timestamp);

        // 開始 > 終了なら入れ替え
        if ($startTs > $endTs) {
            [$startTs, $endTs] = [$endTs, $startTs];
        }

        $totalSeconds = $endTs - $startTs;
        $totalMinutes = floor($totalSeconds / 60);

        if ($totalMinutes <= 0) {
            return '0:00';
        }

        // 休憩合計（複数対応）
        $breakMinutes = 0;
        $breakStartTs = null;

        foreach ($records as $record) {
            $ts = strtotime($record->timestamp);
            if ($record->type === 'break_start') {
                $breakStartTs = $ts;
            } elseif ($record->type === 'break_end' && $breakStartTs) {
                $breakMinutes += floor(($ts - $breakStartTs) / 60);
                $breakStartTs = null;
            }
        }

        $workMinutes = max(0, $totalMinutes - $breakMinutes);

        $hours   = floor($workMinutes / 60);
        $minutes = $workMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    /* ★指定期間の全日付リストを作成（Y-m-d形式） */
    public static function getDateRange($start, $end)
    {
        $dates = [];
        $current = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();

        while ($current->lte($endDate)) {
            $dates[$current->format('Y-m-d')] = [
                'check_in' => '---',
                'break_start' => '---',
                'break_end' => '---',
                'check_out' => '---',
                'work_hours' => '---',
            ];
            $current->addDay();
        }

        return $dates;
    }

    // タイムゾーンを東京に
    protected $dates = ['timestamp'];

    public function getTimestampAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Tokyo');
    }

    public function setTimestampAttribute($value)
    {
        $this->attributes['timestamp'] = Carbon::parse($value)->setTimezone('Asia/Tokyo');
    }

    public static function calculateExcelSplit($date, $userId)
    {
        $records = self::where('user_id', $userId)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp', 'asc')
            ->get();

        $inRecord = $records->whereIn('type', ['check_in', 'business_trip_start'])->first();
        $outRecord = $records->whereIn('type', ['check_out', 'business_trip_end'])->first();

        if (!$inRecord || !$outRecord) {
            return null;
        }

        // 30分単位の丸め処理（前回同様）
        $start = $inRecord->timestamp->copy();
        if ($start->minute > 0 && $start->minute <= 30) {
            $start->minute(30);
        } elseif ($start->minute > 30) {
            $start->addHour()->minute(0);
        }
        $start->second(0);

        $end = $outRecord->timestamp->copy();
        if ($end->minute > 0 && $end->minute < 30) {
            $end->minute(0);
        } elseif ($end->minute >= 30) {
            $end->minute(30);
        }
        $end->second(0);

        if ($start->gte($end)) {
            return null;
        }

        $results = ['early' => 0, 'basic' => 0, 'over' => 0, 'night' => 0, 'total' => 0];

        // 新しい時間区分ルール
        $rules = [
            'night_prev' => ['00:00', '07:00', 'night'], // 早朝（深夜業）
            'early'      => ['07:00', '09:00', 'early'], // 早出
            'basic'      => ['09:00', '18:00', 'basic'], // 基本
            'over'       => ['18:00', '21:00', 'over'],  // 残業
            'night_next' => ['21:00', '24:00', 'night'], // 深夜
        ];

        foreach ($rules as $range) {
            $pStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $range[0] . ':00');
            $pEnd   = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $range[1] . ':00');
            
            if ($range[1] === '24:00') {
                $pEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00')->addDay();
            }

            $overlapStart = $start->gt($pStart) ? $start : $pStart;
            $overlapEnd   = $end->lt($pEnd) ? $end : $pEnd;

            if ($overlapStart->lt($overlapEnd)) {
                $results[$range[2]] += $overlapStart->diffInMinutes($overlapEnd);
            }
        }

        // 休憩60分（基本就業時間 09:00-18:00 の中からのみ引く）
        if ($results['basic'] > 0) {
            // 基本時間が60分以下の場合は0に、それ以上の場合は60分引く
            $results['basic'] = max(0, $results['basic'] - 60);
        }
        
        // 合計の再集計
        $results['total'] = $results['early'] + $results['basic'] + $results['over'] + $results['night'];

        return $results;
    }
}