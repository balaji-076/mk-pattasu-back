<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class CrackerCategory extends Model
{
    protected $fillable = [
        'label', 'value', 'seq_order', 'active_status', 'image_url', 'image_public_id',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('seq_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'label');
    }
    
}