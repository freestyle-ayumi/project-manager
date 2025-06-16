<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id', // 経費を登録したユーザー
        'expense_status_id', // ★ここが重要：DBのカラム名と一致させる
        'date',
        'category',
        'description',
        'amount', // ★ここが重要：DBのカラム名と一致させる (total_amount ではない)
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(ExpenseStatus::class, 'expense_status_id'); // ★ここが重要：リレーションの外部キー名をDBと一致させる
    }

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
}