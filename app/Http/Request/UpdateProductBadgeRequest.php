<?php

namespace App\Http\Requests\ProductBadge;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string'],
            'variety'          => ['required', 'string'],
            'rate'             => ['required', 'numeric', 'min:0'],
            'applied_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_description' => ['nullable', 'string'],
            'badge_id'         => ['nullable'], 
            'is_active'        => ['nullable'],
            'image'            => ['nullable', 'image', 'max:2048'],
        ];
    }
}   