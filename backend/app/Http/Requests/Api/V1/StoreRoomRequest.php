<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'room_number' => ['required', 'string', 'max:20'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'floor' => ['nullable', 'integer'],
            'block' => ['nullable', 'string', 'max:50'],
            'wing' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:available,occupied,reserved,maintenance,out-of-order,blocked'],
            'housekeeping_status' => ['nullable', 'in:clean,dirty,inspected,out-of-service,pickup'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'is_smoking' => ['nullable', 'boolean'],
            'is_accessible' => ['nullable', 'boolean'],
            'is_connecting' => ['nullable', 'boolean'],
            'connecting_room_id' => ['nullable', 'exists:rooms,id'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
