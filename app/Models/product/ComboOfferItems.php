<?php

namespace App\Models\product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ComboOfferItems extends Model
{
    protected $table = 'combo_offer_items';

    protected $fillable = [
        'combo_offer_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function comboOffer()
    {
        return $this->belongsTo(ComboOffer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}