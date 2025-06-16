<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'user_id',
        'quote_number',
        'issue_date',
        'expiry_date',
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

    // 追加: この見積書に紐づく明細を取得
    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
}