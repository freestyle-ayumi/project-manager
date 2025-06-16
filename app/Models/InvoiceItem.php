<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_name',
        'price',
        'quantity',
        'unit',
        'tax_rate',
        'tax',
        'subtotal',
        'memo',
    ];

    // この明細が関連する請求書を取得
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}