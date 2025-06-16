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
        'assigned_user_id', // タスクが割り当てられたユーザー
        'created_user_id',  // タスクを作成したユーザー
        'due_date',
        'status',           // タスクのステータス (例: '未着手', '進行中', '完了')
        'priority',         // 優先度 (例: '低', '中', '高')
    ];

    // このタスクが関連するプロジェクトを取得
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // このタスクが割り当てられたユーザーを取得
    public function assignedUser()
    {
        // 'assigned_user_id' が外部キー
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // このタスクを作成したユーザーを取得
    public function createdUser()
    {
        // 'created_user_id' が外部キー
        return $this->belongsTo(User::class, 'created_user_id');
    }
}