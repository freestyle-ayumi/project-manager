<?php

// app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'schedule_date',
        'title',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
