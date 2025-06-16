<?php

namespace App\Models;

// Carbonをuseしない場合は、日付のフォーマットにCarbon::parseが使えないので注意
// use Carbon\Carbon; // 必要であれば追加

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
        'role_id', // 追加: usersテーブルにrole_idがある場合
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

    // ★★★ ここから追加 ★★★
    // このユーザーが属するロールを取得
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // ★★★ ここまで追加 ★★★
}