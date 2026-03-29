<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreRoomFeatureRequest;
use App\Http\Resources\V1\RoomFeatureResource;
use App\Models\RoomFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class RoomFeatureController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/room-features",
     *     operationId="listRoomFeatures",
     *     tags={"Room Features"},
     *     summary="List all room features/amenities",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="active_only", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(
     *         response=200,
     *         description="Room features list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string", example="Free WiFi"),
     *                 @OA\Property(property="code", type="string", example="WIFI"),
     *                 @OA\Property(property="icon", type="string", example="wifi")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = RoomFeature::query()->ordered();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->boolean('active_only', true)) {
            $query->active();
        }

        return $this->success(RoomFeatureResource::collection($query->get()));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/room-features",
     *     operationId="storeRoomFeature",
     *     tags={"Room Features"},
     *     summary="Create a new room feature",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","name","code"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Air Conditioning"),
     *             @OA\Property(property="code", type="string", example="AC"),
     *             @OA\Property(property="icon", type="string", example="snowflake"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Feature created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreRoomFeatureRequest $request): JsonResponse
    {
        $feature = RoomFeature::create($request->validated());
        return $this->created(new RoomFeatureResource($feature), 'Room feature created');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/room-features/{id}",
     *     operationId="showRoomFeature",
     *     tags={"Room Features"},
     *     summary="Get room feature details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Feature details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $feature = RoomFeature::find($id);
        if (!$feature) {
            return $this->notFound('Room feature not found');
        }
        return $this->success(new RoomFeatureResource($feature));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/room-features/{id}",
     *     operationId="updateRoomFeature",
     *     tags={"Room Features"},
     *     summary="Update a room feature",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="code", type="string"),
     *         @OA\Property(property="icon", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Feature updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $feature = RoomFeature::find($id);
        if (!$feature) {
            return $this->notFound('Room feature not found');
        }

        $feature->update($request->only(['name', 'code', 'icon', 'description', 'is_active', 'sort_order']));
        return $this->success(new RoomFeatureResource($feature), 'Room feature updated');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/room-features/{id}",
     *     operationId="deleteRoomFeature",
     *     tags={"Room Features"},
     *     summary="Delete a room feature (soft delete)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Feature deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $feature = RoomFeature::find($id);
        if (!$feature) {
            return $this->notFound('Room feature not found');
        }
        $feature->delete();
        return $this->success(null, 'Room feature deleted');
    }
}
