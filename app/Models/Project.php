<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    public function checklists()
    {
        return $this->hasMany(ProjectChecklist::class);
    }
    public function keywordFlags()
    {
        return $this->hasMany(ProjectKeywordFlag::class);
    }
    // ■ステータスを返すアクセサ
    //  '開催前', '開催中', '終了' のいずれかを返す
    public function getStatusAttribute()
    {
        $today = \Carbon\Carbon::today();
        $start = $this->start_date ? \Carbon\Carbon::parse($this->start_date)->startOfDay() : null;
        $end   = $this->end_date ? \Carbon\Carbon::parse($this->end_date)->endOfDay() : null;

        if (!$start) return 'unknown';

        if ($end) {
            if ($today->lt($start)) return 'before';
            if ($today->betweenIncluded($start, $end)) return 'progress';
            return 'end';
        }

        if ($today->lt($start)) return 'before';
        if ($today->eq($start)) return 'progress';
        return 'end';
    }
    public function deliveries()
    {
        return $this->hasMany(\App\Models\Delivery::class);
    }

    public function getDeliveriesSumTotalAmountAttribute()
    {
        return $this->deliveries()->sum('total_amount');
    }

}