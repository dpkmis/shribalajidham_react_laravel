<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'room_number' => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'floor' => $this->floor,
            'block' => $this->block,
            'wing' => $this->wing,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'housekeeping_status' => $this->housekeeping_status,
            'current_rate' => $this->current_rate,
            'price_override' => $this->price_override,
            'is_smoking' => $this->is_smoking,
            'is_accessible' => $this->is_accessible,
            'is_connecting' => $this->is_connecting,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
