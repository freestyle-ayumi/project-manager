<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseStatus extends Model
{
    use HasFactory;

    // テーブル名が 'expense_statuses' なので明示的に指定
    protected $table = 'expense_statuses';

    protected $fillable = [
        'name', // 例: '申請中', '承認済み', '却下'
    ];

    // 必要に応じて、このステータスに属する経費を取得するリレーションも定義できますが、
    // 今回はシンプルにリスト表示のみなので、不要です。
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}