<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingChargeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'description' => $this->description,
            'amount' => $this->amount_cents / 100,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
