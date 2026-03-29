<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreRoomTypeRequest;
use App\Http\Requests\Api\V1\UpdateRoomTypeRequest;
use App\Http\Resources\V1\RoomTypeResource;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class RoomTypeController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/room-types",
     *     operationId="listRoomTypes",
     *     tags={"Room Types"},
     *     summary="List all room types",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="active_only", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="with_features", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(
     *         response=200,
     *         description="Room types list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string", example="Deluxe Room"),
     *                 @OA\Property(property="default_rate", type="number", example=3520.00),
     *                 @OA\Property(property="max_occupancy", type="integer", example=3),
     *                 @OA\Property(property="bed_type", type="string", example="King Size")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = RoomType::query()->ordered();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->boolean('active_only', true)) {
            $query->active();
        }
        if ($request->boolean('with_features')) {
            $query->with('features');
        }

        $roomTypes = $query->get()->each(function ($rt) {
            $rt->available_rooms_count = $rt->getAvailableRoomsCount();
            $rt->total_rooms_count = $rt->getTotalRoomsCount();
        });

        return $this->success(RoomTypeResource::collection($roomTypes));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/room-types/{id}",
     *     operationId="showRoomType",
     *     tags={"Room Types"},
     *     summary="Get room type details with features",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room type details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $roomType = RoomType::with('features')->find($id);
        if (!$roomType) {
            return $this->notFound('Room type not found');
        }
        $roomType->available_rooms_count = $roomType->getAvailableRoomsCount();
        $roomType->total_rooms_count = $roomType->getTotalRoomsCount();
        return $this->success(new RoomTypeResource($roomType));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/room-types",
     *     operationId="storeRoomType",
     *     tags={"Room Types"},
     *     summary="Create a new room type",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","name","code","default_rate","max_occupancy","max_adults"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Superior Double Room"),
     *             @OA\Property(property="code", type="string", example="SUP-DBL"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="default_rate", type="number", example=4400.00),
     *             @OA\Property(property="max_occupancy", type="integer", example=3),
     *             @OA\Property(property="max_adults", type="integer", example=3),
     *             @OA\Property(property="max_children", type="integer", example=1),
     *             @OA\Property(property="beds", type="integer", example=2),
     *             @OA\Property(property="bed_type", type="string", example="Double + King"),
     *             @OA\Property(property="room_size_sqm", type="number", example=32.5),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="feature_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Room type created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreRoomTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['default_rate_cents'] = (int) ($data['default_rate'] * 100);
        unset($data['default_rate'], $data['feature_ids']);

        $roomType = RoomType::create($data);

        if ($request->filled('feature_ids')) {
            $roomType->features()->sync($request->feature_ids);
        }

        $roomType->load('features');
        return $this->created(new RoomTypeResource($roomType), 'Room type created');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/room-types/{id}",
     *     operationId="updateRoomType",
     *     tags={"Room Types"},
     *     summary="Update a room type",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="default_rate", type="number"),
     *         @OA\Property(property="feature_ids", type="array", @OA\Items(type="integer"))
     *     )),
     *     @OA\Response(response=200, description="Room type updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(UpdateRoomTypeRequest $request, int $id): JsonResponse
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return $this->notFound('Room type not found');
        }

        $data = $request->validated();
        if (isset($data['default_rate'])) {
            $data['default_rate_cents'] = (int) ($data['default_rate'] * 100);
            unset($data['default_rate']);
        }
        unset($data['feature_ids']);

        $roomType->update($data);

        if ($request->filled('feature_ids')) {
            $roomType->features()->sync($request->feature_ids);
        }

        $roomType->load('features');
        return $this->success(new RoomTypeResource($roomType), 'Room type updated');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/room-types/{id}",
     *     operationId="deleteRoomType",
     *     tags={"Room Types"},
     *     summary="Delete a room type (soft delete)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Room type deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return $this->notFound('Room type not found');
        }

        if ($roomType->rooms()->exists()) {
            return $this->error('Cannot delete room type with existing rooms. Remove rooms first.', 409);
        }

        $roomType->delete();
        return $this->success(null, 'Room type deleted');
    }
}
