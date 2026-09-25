<?php

namespace App\Models\product;

use App\Models\ComboPackItems;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComboOffer extends Model
{
    use HasFactory;

    protected $table = 'combo_offers';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'image_public_id',
        'original_total',
        'combo_price',
        'discount_percent',
        'starts_at',
        'ends_at',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'original_total'   => 'decimal:2',
        'combo_price'      => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'starts_at'        => 'date',
        'ends_at'          => 'date',
        'is_active'        => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'combo_offer_items')
                     ->withPivot('quantity')
                     ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(ComboOfferItems::class);
    }

    public function itemsList()
    {
        return $this->hasMany(ComboPackItems::class, 'combo_pack_id');
    }

    protected static function booted(): void
    {
        static::saving(function (ComboOffer $combo) {
            if ((empty($combo->slug) || $combo->isDirty('name')) && !empty($combo->name)) {
                $baseSlug = Str::slug($combo->name);
                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)
                    ->when($combo->exists, fn($query) => $query->where('id', '!=', $combo->id))
                    ->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $combo->slug = $slug;
            }
        });
    }
}