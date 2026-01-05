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
        'delivery_date',
        'delivery_location',
        'payment_terms',
        'subject',
        'notes',
        'total_amount',
        'status',
        'action',
        'pdf_path',
    ];
    // ステータス定数
    const STATUS_DRAFT = '作成済み';
    const STATUS_ISSUED = '発行済み';
    const STATUS_SENT = '送信済み';

    // ステータス配列（順番が重要）
    public static $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_ISSUED,
        self::STATUS_SENT,
    ];

    // 次のステータスを取得するメソッド
    public function nextStatus()
    {
        $currentIndex = array_search($this->status, self::$statuses);
        // 送信済みの次は進めない（循環させない）
        if ($currentIndex === false || $currentIndex === count(self::$statuses) - 1) {
            return $this->status; // 変更なし
        }
        $nextIndex = $currentIndex + 1;
        return self::$statuses[$nextIndex];
    }

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
