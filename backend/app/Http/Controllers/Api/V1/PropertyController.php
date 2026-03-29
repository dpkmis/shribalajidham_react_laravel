<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\PropertyResource;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PropertyController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/properties",
     *     operationId="listProperties",
     *     tags={"Properties"},
     *     summary="List all properties",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Properties list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="code", type="string", example="SBD-MTR"),
     *                 @OA\Property(property="name", type="string", example="Shri BalaJi Dham Hotel"),
     *                 @OA\Property(property="city", type="string", example="Mathura"),
     *                 @OA\Property(property="country", type="string", example="India")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $properties = Property::withCount(['rooms', 'roomTypes'])->get();
        return $this->success(PropertyResource::collection($properties));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/properties/{id}",
     *     operationId="showProperty",
     *     tags={"Properties"},
     *     summary="Get property details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Property details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $property = Property::withCount(['rooms', 'roomTypes'])->find($id);
        if (!$property) {
            return $this->notFound('Property not found');
        }
        return $this->success(new PropertyResource($property));
    }
}
