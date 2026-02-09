<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'is_valid'  => 'boolean',
    ];

    // 打刻したユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 打刻された勤務地
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}