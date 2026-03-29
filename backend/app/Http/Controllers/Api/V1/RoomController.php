<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreRoomRequest;
use App\Http\Resources\V1\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class RoomController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/rooms",
     *     operationId="listRooms",
     *     tags={"Rooms"},
     *     summary="List rooms with filters",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="room_type_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"available","occupied","reserved","maintenance","out-of-order","blocked"})),
     *     @OA\Parameter(name="floor", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=25)),
     *     @OA\Response(
     *         response=200,
     *         description="Rooms list with pagination",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="room_number", type="string", example="101"),
     *                 @OA\Property(property="status", type="string", example="available"),
     *                 @OA\Property(property="current_rate", type="number", example=2376.00)
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="current_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::with('roomType')->ordered();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->filled('room_type_id')) {
            $query->byType($request->room_type_id);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('floor')) {
            $query->byFloor($request->floor);
        }

        $rooms = $query->paginate($request->integer('per_page', 25));
        return $this->paginated($rooms, 'Success', RoomResource::class);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms/available",
     *     operationId="listAvailableRooms",
     *     tags={"Rooms"},
     *     summary="List only available rooms (clean, active, not occupied)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="room_type_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="checkin_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="checkout_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Available rooms"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function available(Request $request): JsonResponse
    {
        $query = Room::with('roomType')->available()->ordered();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->filled('room_type_id')) {
            $query->byType($request->room_type_id);
        }

        // Exclude rooms that have confirmed/checked-in bookings overlapping with the date range
        if ($request->filled('checkin_date') && $request->filled('checkout_date')) {
            $checkin = $request->checkin_date;
            $checkout = $request->checkout_date;

            $query->whereDoesntHave('housekeepingTasks', function ($q) {
                // placeholder for future filtering
            });

            $bookedRoomIds = \App\Models\BookingRoom::whereHas('booking', function ($q) use ($checkin, $checkout) {
                $q->whereIn('status', ['confirmed', 'checked-in'])
                  ->where('checkin_date', '<', $checkout)
                  ->where('checkout_date', '>', $checkin);
            })->pluck('room_id')->unique();

            if ($bookedRoomIds->isNotEmpty()) {
                $query->whereNotIn('id', $bookedRoomIds);
            }
        }

        return $this->success(RoomResource::collection($query->get()));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms/{id}",
     *     operationId="showRoom",
     *     tags={"Rooms"},
     *     summary="Get room details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $room = Room::with('roomType.features')->find($id);
        if (!$room) {
            return $this->notFound('Room not found');
        }
        return $this->success(new RoomResource($room));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/rooms",
     *     operationId="storeRoom",
     *     tags={"Rooms"},
     *     summary="Create a new room",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","room_number","room_type_id"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="room_number", type="string", example="201"),
     *             @OA\Property(property="room_type_id", type="integer", example=1),
     *             @OA\Property(property="floor", type="integer", example=2),
     *             @OA\Property(property="status", type="string", example="available"),
     *             @OA\Property(property="price_override", type="number", example=2500.00),
     *             @OA\Property(property="is_smoking", type="boolean", example=false),
     *             @OA\Property(property="is_accessible", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Room created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['price_override'])) {
            $data['price_override_cents'] = (int) ($data['price_override'] * 100);
            unset($data['price_override']);
        }

        $room = Room::create($data);
        $room->load('roomType');
        return $this->created(new RoomResource($room), 'Room created');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/rooms/{id}",
     *     operationId="updateRoom",
     *     tags={"Rooms"},
     *     summary="Update a room",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="room_number", type="string"),
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="housekeeping_status", type="string"),
     *         @OA\Property(property="price_override", type="number"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Room updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $room = Room::find($id);
        if (!$room) {
            return $this->notFound('Room not found');
        }

        $data = $request->only([
            'room_number', 'room_type_id', 'floor', 'block', 'wing',
            'status', 'housekeeping_status', 'is_smoking', 'is_accessible',
            'is_connecting', 'connecting_room_id', 'is_active', 'notes',
        ]);

        if ($request->has('price_override')) {
            $data['price_override_cents'] = $request->price_override
                ? (int) ($request->price_override * 100)
                : null;
        }

        $room->update($data);
        $room->load('roomType');
        return $this->success(new RoomResource($room), 'Room updated');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/rooms/{id}",
     *     operationId="deleteRoom",
     *     tags={"Rooms"},
     *     summary="Delete a room (soft delete)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $room = Room::find($id);
        if (!$room) {
            return $this->notFound('Room not found');
        }

        if ($room->status === 'occupied') {
            return $this->error('Cannot delete an occupied room', 409);
        }

        $room->delete();
        return $this->success(null, 'Room deleted');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rooms/status-summary",
     *     operationId="roomStatusSummary",
     *     tags={"Rooms"},
     *     summary="Get room status summary counts",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Status counts")
     * )
     */
    public function statusSummary(Request $request): JsonResponse
    {
        $query = Room::query();
        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }

        $summary = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $housekeeping = Room::query();
        if ($request->filled('property_id')) {
            $housekeeping->forProperty($request->property_id);
        }
        $hkSummary = $housekeeping->selectRaw('housekeeping_status, COUNT(*) as count')
            ->groupBy('housekeeping_status')
            ->pluck('count', 'housekeeping_status');

        return $this->success([
            'by_status' => $summary,
            'by_housekeeping' => $hkSummary,
            'total' => $summary->sum(),
        ]);
    }
}
