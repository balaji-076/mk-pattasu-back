<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Model;

class PosBill extends Model
{
    protected $table = 'pos_bills';

    protected $fillable = [
        'bill_no',
        'customer_name',
        'customer_phone',
        'customer_city',
        'items',
        'subtotal',
        'discount',
        'net_total',
        'payment_method'
    ];

    protected $casts = [
        'items'     => 'array',
        'subtotal'  => 'decimal:2',
        'discount'  => 'decimal:2',
        'net_total' => 'decimal:2',
    ];
}