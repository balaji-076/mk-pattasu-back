<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(
            Orders::class,
            'order_id',  // FK
            'id'         // owner key
        );
    }
}
