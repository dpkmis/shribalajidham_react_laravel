<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreGuestRequest;
use App\Http\Resources\V1\GuestResource;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class GuestController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/guests",
     *     operationId="listGuests",
     *     tags={"Guests"},
     *     summary="List guests with search and filters",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search by name, email, or phone", @OA\Schema(type="string")),
     *     @OA\Parameter(name="guest_type", in="query", @OA\Schema(type="string", enum={"regular","corporate","travel-agent","walk-in"})),
     *     @OA\Parameter(name="is_vip", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=25)),
     *     @OA\Response(response=200, description="Guests list with pagination"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Guest::query()->withCount('bookings')->latest();

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('guest_type')) {
            $query->where('guest_type', $request->guest_type);
        }
        if ($request->has('is_vip')) {
            $request->boolean('is_vip') ? $query->vip() : $query;
        }
        if ($request->boolean('blacklisted')) {
            $query->blacklisted();
        } else {
            $query->active();
        }

        $guests = $query->paginate($request->integer('per_page', 25));
        return $this->paginated($guests, 'Success', GuestResource::class);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/guests/{id}",
     *     operationId="showGuest",
     *     tags={"Guests"},
     *     summary="Get guest profile details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Guest details"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $guest = Guest::withCount('bookings')->find($id);
        if (!$guest) {
            return $this->notFound('Guest not found');
        }
        return $this->success(new GuestResource($guest));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/guests",
     *     operationId="storeGuest",
     *     tags={"Guests"},
     *     summary="Create a new guest profile",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id","first_name","last_name","phone"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="Rajesh"),
     *             @OA\Property(property="last_name", type="string", example="Kumar"),
     *             @OA\Property(property="title", type="string", example="Mr"),
     *             @OA\Property(property="phone", type="string", example="+919876543210"),
     *             @OA\Property(property="email", type="string", example="rajesh@example.com"),
     *             @OA\Property(property="city", type="string", example="New Delhi"),
     *             @OA\Property(property="id_type", type="string", example="Aadhaar"),
     *             @OA\Property(property="id_number", type="string", example="1234-5678-9012"),
     *             @OA\Property(property="meal_preference", type="string", example="veg")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Guest created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreGuestRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by_user_id'] = $request->user()->id;

        $guest = Guest::create($data);
        return $this->created(new GuestResource($guest), 'Guest created');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/guests/{id}",
     *     operationId="updateGuest",
     *     tags={"Guests"},
     *     summary="Update a guest profile",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="first_name", type="string"),
     *         @OA\Property(property="last_name", type="string"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="is_vip", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Guest updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $guest = Guest::find($id);
        if (!$guest) {
            return $this->notFound('Guest not found');
        }

        $guest->update(array_merge(
            $request->only([
                'first_name', 'last_name', 'middle_name', 'title', 'gender', 'dob',
                'nationality', 'email', 'phone', 'whatsapp', 'alternate_phone',
                'address_line1', 'address_line2', 'city', 'state', 'country', 'postal_code',
                'company_name', 'company_designation', 'gstin',
                'id_type', 'id_number', 'id_expiry_date',
                'preferred_language', 'meal_preference', 'special_requests', 'allergies',
                'guest_type', 'is_vip',
            ]),
            ['updated_by_user_id' => $request->user()->id]
        ));

        return $this->success(new GuestResource($guest), 'Guest updated');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/guests/{id}",
     *     operationId="deleteGuest",
     *     tags={"Guests"},
     *     summary="Delete a guest (soft delete)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Guest deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $guest = Guest::find($id);
        if (!$guest) {
            return $this->notFound('Guest not found');
        }
        $guest->delete();
        return $this->success(null, 'Guest deleted');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/guests/search",
     *     operationId="searchGuests",
     *     tags={"Guests"},
     *     summary="Quick search guests for autocomplete",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string", minLength=2)),
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Search results (max 10)")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $query = Guest::search($request->q)->active()->limit(10);

        if ($request->filled('property_id')) {
            $query->forProperty($request->property_id);
        }

        return $this->success(GuestResource::collection($query->get()));
    }
}
