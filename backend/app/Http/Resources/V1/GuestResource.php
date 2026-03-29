<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ulid' => $this->ulid,
            'property_id' => $this->property_id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'dob' => $this->dob?->toDateString(),
            'age' => $this->age,
            'nationality' => $this->nationality,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->complete_address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'company_name' => $this->company_name,
            'id_type' => $this->id_type,
            'id_number' => $this->id_number,
            'guest_type' => $this->guest_type,
            'is_vip' => $this->is_vip,
            'is_blacklisted' => $this->is_blacklisted,
            'loyalty_points' => $this->loyalty_points,
            'meal_preference' => $this->meal_preference,
            'special_requests' => $this->special_requests,
            'total_bookings' => $this->when(isset($this->bookings_count), $this->bookings_count),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
