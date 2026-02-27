<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'allowed_radius',
    ];

    // 1つの勤務地に紐づくユーザー（多対多）
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_locations');
    }

    // 1つの勤務地で打刻された記録
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'allowed_radius' => 'float',
    ];
}