<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'default_rate' => ['required', 'numeric', 'min:0'],
            'max_occupancy' => ['required', 'integer', 'min:1'],
            'max_adults' => ['required', 'integer', 'min:1'],
            'max_children' => ['nullable', 'integer', 'min:0'],
            'beds' => ['nullable', 'integer', 'min:1'],
            'bed_type' => ['nullable', 'string', 'max:50'],
            'room_size_sqm' => ['nullable', 'numeric', 'min:0'],
            'amenities' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['exists:room_features,id'],
        ];
    }
}
