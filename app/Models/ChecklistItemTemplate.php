<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItemTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_keyword_flag_id',
        'name',
    ];

    /**
     * どのキーワードに属するテンプレートか
     */
    public function keywordFlag()
    {
        return $this->belongsTo(ProjectKeywordFlag::class, 'project_keyword_flag_id');
    }
}
