<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'item_name',
        'price',
        'quantity',
        'unit',
        'tax_rate',
        'tax',
        'subtotal',
        'memo',
    ];

    // この明細が関連する見積書を取得
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}