<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'default_rate' => $this->default_rate,
            'max_occupancy' => $this->max_occupancy,
            'max_adults' => $this->max_adults,
            'max_children' => $this->max_children,
            'beds' => $this->beds,
            'bed_type' => $this->bed_type,
            'room_size_sqm' => $this->room_size_sqm,
            'amenities' => $this->amenities,
            'images' => $this->images ? collect($this->images)->map(function ($img) {
                if (str_starts_with($img, 'http')) return $img;
                return url($img);
            })->toArray() : null,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'features' => RoomFeatureResource::collection($this->whenLoaded('features')),
            'available_rooms_count' => $this->when(isset($this->available_rooms_count), $this->available_rooms_count),
            'total_rooms_count' => $this->when(isset($this->total_rooms_count), $this->total_rooms_count),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
