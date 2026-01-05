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
        'delivery_number', 
        'subject', 
        'issue_date', 
        'expiry_date',
        'delivery_date', 
        'delivery_location', 
        'payment_terms',
        'total_amount', 
        'pdf_path', 
        'status', 
        'notes'
    ];
    const STATUS_DRAFT = '作成済み';
    const STATUS_ISSUED = '発行済み';
    const STATUS_SENT = '送信済み';

    public static $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_ISSUED,
        self::STATUS_SENT,
    ];

    public function nextStatus()
    {
        $currentIndex = array_search($this->status, self::$statuses);
        if ($currentIndex === false || $currentIndex === count(self::$statuses) - 1) {
            return $this->status;
        }
        return self::$statuses[$currentIndex + 1];
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function logs()
    {
        return $this->hasMany(DeliveryLog::class);
    }

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
}