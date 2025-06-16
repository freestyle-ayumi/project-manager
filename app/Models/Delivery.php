<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'user_id',
        'delivery_date',
        'total_amount',
        'status',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 追加: この納品書に紐づく明細を取得
    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }
}