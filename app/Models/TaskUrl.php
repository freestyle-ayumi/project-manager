<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUrl extends Model
{
    use HasFactory;

    protected $table = 'tasks_urls'; // ← これを追加！（マイグレーションのテーブル名に合わせる）

    protected $fillable = [
        'task_id',
        'url',
        'title',
        'memo',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}