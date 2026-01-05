<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItemTemplate extends Model
{
    protected $fillable = ['name', 'project_keyword_flag_id'];

    public function projectKeywordFlag()
    {
        return $this->belongsTo(ProjectKeywordFlag::class);
    }
}
