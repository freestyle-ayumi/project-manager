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
        'venue',
        'color',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'project_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
}


