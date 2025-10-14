<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'start_date',
        'due_date',
        'status',      // ステータス
        'priority',    // 優先度
        'user_id',     // 作成者 or 登録者
    ];

    // 関連プロジェクト
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // このタスクに割り当てられているユーザー（担当者たち）
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }

    // このタスクを作成したユーザー
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
