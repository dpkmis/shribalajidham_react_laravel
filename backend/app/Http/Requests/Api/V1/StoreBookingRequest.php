<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'guest_id' => ['required', 'exists:guests,id'],
            'checkin_date' => ['required', 'date', 'after_or_equal:today'],
            'checkout_date' => ['required', 'date', 'after:checkin_date'],
            'number_of_adults' => ['required', 'integer', 'min:1'],
            'number_of_children' => ['nullable', 'integer', 'min:0'],
            'number_of_infants' => ['nullable', 'integer', 'min:0'],
            'source' => ['nullable', 'string', 'in:direct,website,phone,walk-in,ota,agent'],
            'special_requests' => ['nullable', 'string'],
            'arrival_time' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.room_id' => ['required', 'exists:rooms,id'],
            'rooms.*.rate_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
