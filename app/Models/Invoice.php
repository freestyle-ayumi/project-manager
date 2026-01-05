<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 
        'client_id', 
        'user_id',
        'invoice_number', 
        'issue_date', 
        'expiry_date',
        'delivery_date', 
        'delivery_location', 
        'payment_terms',
        'subject', 'notes', 
        'total_amount', 
        'status', 
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function logs()
    {
        return $this->hasMany(InvoiceLog::class);
    }

    // ステータス管理（見積書と同じ）
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
}