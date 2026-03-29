<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Resources\V1\BookingResource;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class BookingController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/bookings",
     *     operationId="listBookings",
     *     tags={"Bookings"},
     *     summary="List bookings with filters and pagination",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"pending","confirmed","checked-in","checked-out","cancelled","no-show"})),
     *     @OA\Parameter(name="payment_status", in="query", @OA\Schema(type="string", enum={"unpaid","partially-paid","paid"})),
     *     @OA\Parameter(name="from_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="guest_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search by booking reference or guest name", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=25)),
     *     @OA\Response(response=200, description="Bookings list with pagination"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['guest', 'bookingRooms.room'])->latest();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->byDateRange($request->from_date, $request->to_date);
        }
        if ($request->filled('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($gq) use ($search) {
                      $gq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate($request->integer('per_page', 25));
        return $this->paginated($bookings, 'Success', BookingResource::class);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bookings/{id}",
     *     operationId="showBooking",
     *     tags={"Bookings"},
     *     summary="Get booking details with rooms, charges, payments",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Booking details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $booking = Booking::with(['guest', 'bookingRooms.room.roomType', 'charges', 'payments'])->find($id);
        if (!$booking) {
            return $this->notFound('Booking not found');
        }
        return $this->success(new BookingResource($booking));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings",
     *     operationId="storeBooking",
     *     tags={"Bookings"},
     *     summary="Create a new booking",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","guest_id","checkin_date","checkout_date","number_of_adults","rooms"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="guest_id", type="integer", example=1),
     *             @OA\Property(property="checkin_date", type="string", format="date", example="2026-04-01"),
     *             @OA\Property(property="checkout_date", type="string", format="date", example="2026-04-03"),
     *             @OA\Property(property="number_of_adults", type="integer", example=2),
     *             @OA\Property(property="number_of_children", type="integer", example=1),
     *             @OA\Property(property="source", type="string", example="website"),
     *             @OA\Property(property="special_requests", type="string", example="Early check-in if possible"),
     *             @OA\Property(property="rooms", type="array", @OA\Items(
     *                 @OA\Property(property="room_id", type="integer", example=1),
     *                 @OA\Property(property="rate_cents", type="integer", example=237600)
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Booking created"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=409, description="Room not available")
     * )
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            // Verify rooms are available
            $roomIds = collect($data['rooms'])->pluck('room_id');
            $rooms = Room::whereIn('id', $roomIds)->get();

            foreach ($rooms as $room) {
                if (!$room->isAvailable()) {
                    return $this->error("Room {$room->room_number} is not available", 409);
                }
            }

            // Calculate total room charges
            $totalRoomCharges = collect($data['rooms'])->sum('rate_cents');
            $nights = \Carbon\Carbon::parse($data['checkin_date'])->diffInDays(\Carbon\Carbon::parse($data['checkout_date']));
            $totalAmount = $totalRoomCharges * $nights;

            $booking = Booking::create([
                'property_id' => $data['property_id'],
                'guest_id' => $data['guest_id'],
                'status' => 'confirmed',
                'source' => $data['source'] ?? 'direct',
                'checkin_date' => $data['checkin_date'],
                'checkout_date' => $data['checkout_date'],
                'number_of_adults' => $data['number_of_adults'],
                'number_of_children' => $data['number_of_children'] ?? 0,
                'number_of_infants' => $data['number_of_infants'] ?? 0,
                'currency' => 'INR',
                'room_charges_cents' => $totalAmount,
                'total_amount_cents' => $totalAmount,
                'paid_amount_cents' => 0,
                'balance_amount_cents' => $totalAmount,
                'payment_status' => 'unpaid',
                'special_requests' => $data['special_requests'] ?? null,
                'arrival_time' => $data['arrival_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $request->user()->id,
            ]);

            // Assign rooms
            foreach ($data['rooms'] as $roomData) {
                $room = $rooms->firstWhere('id', $roomData['room_id']);
                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $roomData['room_id'],
                    'room_type_id' => $room->room_type_id,
                    'rate_cents' => $roomData['rate_cents'],
                    'final_rate_cents' => $roomData['rate_cents'] * $nights,
                    'status' => 'confirmed',
                ]);

                $room->update(['status' => 'reserved']);
            }

            $booking->logActivity('created', 'Booking created via API');
            $booking->load(['guest', 'bookingRooms.room']);

            return $this->created(new BookingResource($booking), 'Booking created');
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings/{id}/checkin",
     *     operationId="checkinBooking",
     *     tags={"Bookings"},
     *     summary="Check-in a guest",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Checked in successfully"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=409, description="Invalid status for check-in")
     * )
     */
    public function checkin(int $id): JsonResponse
    {
        $booking = Booking::with(['bookingRooms.room'])->find($id);
        if (!$booking) {
            return $this->notFound('Booking not found');
        }

        if (!in_array($booking->status, ['confirmed', 'pending'])) {
            return $this->error("Cannot check-in booking with status '{$booking->status}'", 409);
        }

        $booking->checkIn();
        $booking->refresh()->load(['guest', 'bookingRooms.room']);
        return $this->success(new BookingResource($booking), 'Guest checked in successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings/{id}/checkout",
     *     operationId="checkoutBooking",
     *     tags={"Bookings"},
     *     summary="Check-out a guest",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Checked out successfully"),
     *     @OA\Response(response=409, description="Invalid status for check-out")
     * )
     */
    public function checkout(int $id): JsonResponse
    {
        $booking = Booking::with(['bookingRooms.room'])->find($id);
        if (!$booking) {
            return $this->notFound('Booking not found');
        }

        if ($booking->status !== 'checked-in') {
            return $this->error("Cannot check-out booking with status '{$booking->status}'", 409);
        }

        $booking->checkOut();
        $booking->refresh()->load(['guest', 'bookingRooms.room']);
        return $this->success(new BookingResource($booking), 'Guest checked out successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/bookings/{id}/cancel",
     *     operationId="cancelBooking",
     *     tags={"Bookings"},
     *     summary="Cancel a booking",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="reason", type="string", example="Guest requested cancellation")
     *     )),
     *     @OA\Response(response=200, description="Booking cancelled"),
     *     @OA\Response(response=409, description="Cannot cancel")
     * )
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = Booking::with(['bookingRooms.room'])->find($id);
        if (!$booking) {
            return $this->notFound('Booking not found');
        }

        if (in_array($booking->status, ['checked-out', 'cancelled'])) {
            return $this->error("Cannot cancel booking with status '{$booking->status}'", 409);
        }

        // Free rooms if they were reserved
        foreach ($booking->bookingRooms as $br) {
            if ($br->room && $br->room->status === 'reserved') {
                $br->room->markAsAvailable();
            }
        }

        $booking->cancel($request->input('reason'));
        $booking->refresh()->load(['guest', 'bookingRooms.room']);
        return $this->success(new BookingResource($booking), 'Booking cancelled');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bookings/today",
     *     operationId="todayBookings",
     *     tags={"Bookings"},
     *     summary="Get today's check-ins and check-outs",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Today's bookings summary")
     * )
     */
    public function today(Request $request): JsonResponse
    {
        $today = now()->toDateString();

        $checkinsQuery = Booking::with('guest')
            ->where('checkin_date', $today)
            ->whereIn('status', ['confirmed', 'checked-in']);

        $checkoutsQuery = Booking::with('guest')
            ->where('checkout_date', $today)
            ->where('status', 'checked-in');

        if ($request->filled('property_id')) {
            $checkinsQuery->forProperty($request->property_id);
            $checkoutsQuery->forProperty($request->property_id);
        }

        return $this->success([
            'date' => $today,
            'expected_checkins' => BookingResource::collection($checkinsQuery->get()),
            'expected_checkouts' => BookingResource::collection($checkoutsQuery->get()),
            'checkins_count' => $checkinsQuery->count(),
            'checkouts_count' => $checkoutsQuery->count(),
        ]);
    }
}
