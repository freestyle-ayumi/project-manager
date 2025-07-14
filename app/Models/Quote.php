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
        'delivery_date', // 追加
        'delivery_location', // 追加
        'payment_terms', // 追加
        'subject',
        'notes',
        'total_amount',
        'status',
        'action',
        'pdf_path',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
    public function logs()
    {
        return $this->hasMany(QuoteLog::class);
    }

}
