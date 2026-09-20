<?php

namespace App\Http\Requests\ProductBadge;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'          => 'required|string|max:60|unique:mst_product_badges,label',
            'color'          => 'required|string|max:30',
            'icon'           => 'required|string|max:50',
            'animation_type' => 'required|string|max:50',
            'seq_order'      => 'nullable|integer|min:0',
        ];
    }
}