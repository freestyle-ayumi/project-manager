<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'description',
        'start_date',
        'end_date',
        'budget',
        'total_expenses',
        'net_profit',
        'user_id',
        'project_status_id', 
    ];

    // このプロジェクトが属するクライアントを取得
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // このプロジェクトの担当者（ユーザー）を取得
    public function user()
    {
        return $this->belongsTo(User::class); // デフォルトでは user_id カラムを使用
    }

    // このプロジェクトのステータスを取得
    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id'); 
    }
    // このプロジェクトに関連するタスクを取得
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // このプロジェクトに関連する見積書を取得
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    // このプロジェクトに関連する請求書を取得
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // このプロジェクトに関連する経費を取得
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}