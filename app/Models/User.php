<?php

namespace App\Models;

// Carbonをuseしない場合は、日付のフォーマットにCarbon::parseが使えないので注意
// use Carbon\Carbon; // 必要であれば追加

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // このユーザーが属するロールを取得
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user', 'user_id', 'task_id');
    }
    // ユーザーが登録できる勤務地（多対多）
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'user_locations');
    }

    // ユーザーの打刻記録
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

}

