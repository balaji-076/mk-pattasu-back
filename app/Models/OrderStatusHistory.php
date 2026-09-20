<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = ['order_id', 'status', 'note', 'changed_by', 'changed_at'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}