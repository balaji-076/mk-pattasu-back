<?php

namespace App\Models;

use App\Models\product\ComboOffer;
use App\Models\product\ProductBadge;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'rate',
        'variety',
        'image_url',
        'image_public_id',
        'is_active',
        'applied_discount',
        'discount_rate',
        'Visibility',
        'badge_id',          
        'meta_description',
        'tags',
        'sort_order',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'is_featured'       => 'boolean',
        'is_trending'       => 'boolean',
        'is_new_arrival'    => 'boolean',
        'rate'              => 'decimal:2',
        'applied_discount'  => 'decimal:2',
        'discount_rate'     => 'decimal:2',
        'tags'              => 'array',
    ];

    public function badge()
    {
        return $this->belongsTo(ProductBadge::class, 'badge_id');
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug) && $product->name) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function comboOffers()
    {
        return $this->belongsToMany(ComboOffer::class, 'combo_offer_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

}