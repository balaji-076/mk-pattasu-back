<?php

namespace App\Http\Requests\ProductBadge;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'label'          => 'required|string|max:60|unique:mst_product_badges,label,' . $id,
            'color'          => 'required|string|max:30',
            'icon'           => 'required|string|max:50',
            'animation_type' => 'required|string|max:50',
            'seq_order'      => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ];
    }
}