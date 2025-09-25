<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'path', // ファイルの保存パス
    ];

    /**
     * 親の経費申請とのリレーション
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
