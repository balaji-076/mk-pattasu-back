<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class HeroSlider extends Model
{
    use SoftDeletes;
 
    protected $table = 'hero_sliders';
 
    protected $fillable = [
        'cta_link',
        'image_url',
        'image_public_id',
        'is_active',
        'seq_order',
        'valid_from',
        'valid_to',
    ];
 
    protected $casts = [
        'is_active'  => 'boolean',
        'seq_order'  => 'integer',
        'valid_from' => 'date',
        'valid_to'   => 'date',
    ];
 
 
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', today());
            })
            ->where(function (Builder $q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', today());
            });
    }
 
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('seq_order', 'asc')->orderBy('id', 'asc');
    }
}
 