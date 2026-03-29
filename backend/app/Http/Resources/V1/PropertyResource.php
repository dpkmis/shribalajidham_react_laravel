<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'time_zone' => $this->time_zone,
            'currency' => $this->currency,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'meta' => $this->meta,
            'rooms_count' => $this->whenCounted('rooms'),
            'room_types_count' => $this->whenCounted('roomTypes'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
