<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ulid' => $this->ulid,
            'booking_reference' => $this->booking_reference,
            'confirmation_number' => $this->confirmation_number,
            'property_id' => $this->property_id,
            'guest_id' => $this->guest_id,
            'status' => $this->status,
            'source' => $this->source,
            'checkin_date' => $this->checkin_date?->toDateString(),
            'checkout_date' => $this->checkout_date?->toDateString(),
            'nights' => $this->nights,
            'actual_checkin_at' => $this->actual_checkin_at?->toISOString(),
            'actual_checkout_at' => $this->actual_checkout_at?->toISOString(),
            'number_of_adults' => $this->number_of_adults,
            'number_of_children' => $this->number_of_children,
            'number_of_infants' => $this->number_of_infants,
            'currency' => $this->currency,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'balance_amount' => $this->balance_amount,
            'payment_status' => $this->payment_status,
            'payment_progress' => $this->payment_progress,
            'special_requests' => $this->special_requests,
            'arrival_time' => $this->arrival_time,
            'notes' => $this->notes,
            'is_checked_in' => $this->is_checked_in,
            'is_checked_out' => $this->is_checked_out,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'guest' => new GuestResource($this->whenLoaded('guest')),
            'rooms' => BookingRoomResource::collection($this->whenLoaded('bookingRooms')),
            'charges' => BookingChargeResource::collection($this->whenLoaded('charges')),
            'payments' => BookingPaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
