<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',   // 親のExpense ID
        'item_name',    // 品名
        'price',        // 単価
        'quantity',     // 数量
        'unit',         // 単位
        'tax_rate',     // 税率
        'subtotal',     // 小計
        'tax',          // 税
    ];

    /**
     * 親となるExpense（経費申請）とのリレーション
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
