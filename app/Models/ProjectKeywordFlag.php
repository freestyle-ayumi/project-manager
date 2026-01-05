<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectKeywordFlag extends Model
{
    use HasFactory;

    // ここでfillableを1回だけ定義
    protected $fillable = ['keyword'];

    /**
     * Project とのリレーション
     * 1つのプロジェクトに紐付く場合
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * ChecklistItemTemplate へのリレーション
     */
    public function templates()
    {
        return $this->hasMany(ChecklistItemTemplate::class, 'project_keyword_flag_id');
    }
}
