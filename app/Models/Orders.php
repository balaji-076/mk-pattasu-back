<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'shippingCost',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'wa_status',
        'wa_sent_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }

    public function latestStatus()
    {
        return $this->hasOne(OrderStatusHistory::class, 'order_id')->latestOfMany();
    }
}