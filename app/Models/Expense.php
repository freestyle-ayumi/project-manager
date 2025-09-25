<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    // 経費のメイン情報として保存されるフィールド
    protected $fillable = [
        'user_id',
        'expense_status_id', // 承認ステータスなど
        'application_date',  // 申請日
        'department',        // 部門
        'overall_reason',    // 申請理由全体
        'total_amount',      // 合計金額
    ];

    /**
     * この経費を申請したユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この経費のステータスを取得
     */
    public function status()
    {
        return $this->belongsTo(ExpenseStatus::class, 'expense_status_id');
    }

    /**
     * この経費に関連する複数の経費項目を取得
     */
    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
}

