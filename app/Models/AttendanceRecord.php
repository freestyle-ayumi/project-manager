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
                'break_30'            => '休憩30分',
                'break_60'            => '休憩1時間',
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
    public static function calculateDailyWorkHours($date, $userId) {
        $record = self::where('user_id', $userId)
            ->whereDate('timestamp', $date)
            ->whereIn('type', ['check_in', 'business_trip_start'])
            ->first();

        if (!$record || is_null($record->work_minutes)) return '---';

        $mins = $record->work_minutes;
        return floor($mins / 60) . ':' . str_pad($mins % 60, 2, '0', STR_PAD_LEFT);
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
    /**
     * 社員向け：統一勤務計算ロジック
     * 通常分 + 深夜分 = 実働時間 とし、8時間を超えた分を残業とする
     */
    public static function getUnifiedCalculation($date, $userId)
    {
        $records = self::where('user_id', $userId)
            ->where('is_valid', true)
            ->whereDate('timestamp', $date)
            ->orderBy('timestamp', 'asc')
            ->get();

        if ($records->isEmpty()) return null;

        $firstIn = $records->whereIn('type', ['check_in', 'business_trip_start'])->first();
        $lastOut = $records->whereIn('type', ['check_out', 'business_trip_end'])->last();

        if (!$firstIn || !$lastOut) return null;

        $start = Carbon::parse($firstIn->timestamp);
        $end = Carbon::parse($lastOut->timestamp);

        // --- 30分単位の丸め処理 (計算用) ---
        // 出勤時刻：30分単位で切り上げ
        $startMinute = $start->minute;
        $startRounded = ceil($startMinute / 30) * 30;

        if ($startRounded === 60) {
            $start->addHour()->minute(0)->second(0);
        } else {
            $start->minute($startRounded)->second(0);
        }

        // 退勤時刻：30分単位で切り捨て
        $endMinute = $end->minute;
        $endRounded = floor($endMinute / 30) * 30;

        $end->minute($endRounded)->second(0);

        // 1. 総拘束時間（分） ※丸めた後の時間で計算
        $totalMinutes = (int)$start->diffInMinutes($end);

        // 万が一、丸めた結果 出勤 > 退勤 になった場合は0分とする
        if ($start->gt($end)) {
            $totalMinutes = 0;
        }

        // 2. 中抜け時間の合計
        $breakMinutes = 0;
        $bStart = null;
        foreach ($records as $r) {
            if ($r->type === 'break_start') {
                $bStart = Carbon::parse($r->timestamp)->second(0);
            } elseif ($r->type === 'break_end' && $bStart) {
                $breakMinutes += (int)$bStart->diffInMinutes(Carbon::parse($r->timestamp)->second(0));
                $bStart = null;
            }
        }
        
        // ★固定休憩ボタン（30分/60分）の合計を計算
        $fixedBreakMinutes = $records->whereIn('type', ['break_30', 'break_60'])
            ->sum(function($r) {
                return ($r->type === 'break_30') ? 30 : 60;
            });

        // 3. 実働時間（拘束 - 中抜け合計 - 固定休憩合計）
        $actualWorkMinutes = $totalMinutes - $breakMinutes - $fixedBreakMinutes;
        if ($actualWorkMinutes < 0) $actualWorkMinutes = 0;

        // 4. 残業時間の計算（8時間 = 480分超え）
        $overtimeMinutes = ($actualWorkMinutes > 480) ? ($actualWorkMinutes - 480) : 0;
        
        // 5. 基本時間の計算（実働 - 残業）
        $basicWorkMinutes = $actualWorkMinutes - $overtimeMinutes;

        // 6. 深夜時間の計算 (22時〜翌5時)
        $midnightMinutes = self::calculateNightMinutes($start, $end);

        return [
            'check_in'         => $start->format('H:i'),
            'check_out'        => $end->format('H:i'),
            'actual_minutes'   => (int)$actualWorkMinutes,
            'actual_hours'     => sprintf('%d:%02d', floor($actualWorkMinutes / 60), $actualWorkMinutes % 60),
            'midnight_minutes' => (int)$midnightMinutes,
            'overtime_minutes' => (int)$overtimeMinutes,
            'basic_minutes'    => (int)$basicWorkMinutes,
        ];
    }

    private static function calculateNightMinutes(Carbon $start, Carbon $end): int
    {
        $nightMinutes = 0;

        $current = $start->copy()->startOfDay();
        $lastDay = $end->copy()->startOfDay();

        while ($current <= $lastDay) {

            $nightStart = $current->copy()->setTime(22, 0);
            $nightEnd = $current->copy()->addDay()->setTime(5, 0);

            $overlapStart = $start->greaterThan($nightStart) ? $start : $nightStart;
            $overlapEnd = $end->lessThan($nightEnd) ? $end : $nightEnd;

            if ($overlapStart < $overlapEnd) {
                $nightMinutes += $overlapStart->diffInMinutes($overlapEnd);
            }

            $current->addDay();
        }

        return $nightMinutes;
    }
}