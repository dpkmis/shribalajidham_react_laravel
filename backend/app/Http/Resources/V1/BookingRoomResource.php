<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'room_type_id' => $this->room_type_id,
            'rate_cents' => $this->rate_cents,
            'discount_cents' => $this->discount_cents,
            'final_rate_cents' => $this->final_rate_cents,
            'status' => $this->status,
            'room' => new RoomResource($this->whenLoaded('room')),
        ];
    }
}
