<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // テーブル名が 'roles' なので明示的に指定する必要はありませんが、
    // 慣例として書いておくこともあります。
    protected $table = 'roles';

    protected $fillable = [
        'name', // 例: 'Admin', 'Editor', 'Viewer'
    ];

    // 必要に応じて、このロールに属するユーザーを取得するリレーションも定義できますが、
    // 今回は User -> Role の一方向で十分です。
    public function users()
    {
        return $this->hasMany(User::class);
    }
}