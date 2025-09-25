<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    // 各経費項目として保存されるフィールド
    protected $fillable = [
        'expense_id',    // 親となるExpenseのID
        'category',      // 費目 (例: 交通費, 会議費)
        'amount',        // 金額
        'date',          // 日付
        'payee',         // 支払先
        'description',   // 摘要
        'project_id',    // 関連プロジェクト (任意)
        'file_path',     // 添付ファイルのパス
    ];

    /**
     * この経費項目が属する経費を取得
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * この経費項目が関連するプロジェクトを取得
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

