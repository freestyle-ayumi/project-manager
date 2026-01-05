<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'item_name',
        'price',
        'quantity',
        'unit',
        'tax_rate',
        'tax',
        'subtotal'
    ];

    public function delivery() { return $this->belongsTo(Delivery::class); }
}
