<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'category',
        'description',
        'amount',
        'notes', // 明細ごとの備考
    ];

    // この明細が関連する経費申請を取得
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}