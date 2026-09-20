<?php

namespace App\Http\Resource;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
class HeroSliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'image_url'  => $this->image_url,
            'cta_link'   => $this->cta_link,
            'is_active'  => $this->is_active,
            'seq_order'  => $this->seq_order,
            'valid_from' => $this->valid_from?->format('Y-m-d'),
            'valid_to'   => $this->valid_to?->format('Y-m-d'),
            'created_at' => $this->created_at,
        ];
    }
}
 