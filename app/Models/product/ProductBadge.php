<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductBadge extends Model
{
    protected $table = 'mst_product_badges';

    protected $fillable = [
        'label',
        'value',
        'color',
        'icon',
        'animation_type',
        'seq_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seq_order' => 'integer',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('seq_order');
    }
}