<?php

namespace App\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComboOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'image_url'         => $this->image_url,

            'original_total'    => (float) $this->original_total,
            'combo_price'       => (float) $this->combo_price,
            'discount_percent'  => (float) $this->discount_percent,

            'starts_at'         => $this->starts_at?->toDateString(),
            'ends_at'           => $this->ends_at?->toDateString(),

            'is_active'         => (bool) $this->is_active,
            'sort_order'        => $this->sort_order,

            'products'          => $this->whenLoaded('products', function () {
                return $this->products->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'rate'     => (float) $product->rate,
                        'image_url'=> $product->image_url,
                        'quantity' => (int) $product->pivot->quantity,
                    ];
                });
            }),

            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}