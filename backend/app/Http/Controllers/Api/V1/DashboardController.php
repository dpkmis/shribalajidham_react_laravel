<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DashboardController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/dashboard/stats",
     *     operationId="dashboardStats",
     *     tags={"Dashboard"},
     *     summary="Get dashboard statistics",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="property_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard stats",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="rooms", type="object",
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="available", type="integer"),
     *                     @OA\Property(property="occupied", type="integer"),
     *                     @OA\Property(property="occupancy_rate", type="number")
     *                 ),
     *                 @OA\Property(property="bookings", type="object",
     *                     @OA\Property(property="today_checkins", type="integer"),
     *                     @OA\Property(property="today_checkouts", type="integer"),
     *                     @OA\Property(property="active_bookings", type="integer")
     *                 ),
     *                 @OA\Property(property="revenue", type="object",
     *                     @OA\Property(property="today", type="number"),
     *                     @OA\Property(property="this_month", type="number")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function stats(Request $request): JsonResponse
    {
        $propertyId = $request->input('property_id');
        $today = now()->toDateString();

        // Room stats
        $roomsQuery = Room::query();
        if ($propertyId) $roomsQuery->forProperty($propertyId);
        $totalRooms = $roomsQuery->count();
        $availableRooms = (clone $roomsQuery)->available()->count();
        $occupiedRooms = (clone $roomsQuery)->occupied()->count();

        // Booking stats
        $bookingsBase = Booking::query();
        if ($propertyId) $bookingsBase->forProperty($propertyId);

        $todayCheckins = (clone $bookingsBase)->where('checkin_date', $today)
            ->whereIn('status', ['confirmed', 'checked-in'])->count();
        $todayCheckouts = (clone $bookingsBase)->where('checkout_date', $today)
            ->where('status', 'checked-in')->count();
        $activeBookings = (clone $bookingsBase)->active()->count();
        $currentlyStaying = (clone $bookingsBase)->checkedIn()->count();

        // Revenue
        $todayRevenue = (clone $bookingsBase)
            ->where('created_at', '>=', now()->startOfDay())
            ->sum('paid_amount_cents') / 100;
        $monthRevenue = (clone $bookingsBase)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('paid_amount_cents') / 100;

        // Guests
        $guestsBase = Guest::query();
        if ($propertyId) $guestsBase->forProperty($propertyId);
        $totalGuests = $guestsBase->count();
        $newGuestsThisMonth = (clone $guestsBase)
            ->where('created_at', '>=', now()->startOfMonth())->count();

        return $this->success([
            'rooms' => [
                'total' => $totalRooms,
                'available' => $availableRooms,
                'occupied' => $occupiedRooms,
                'maintenance' => (clone $roomsQuery)->where('status', 'maintenance')->count(),
                'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0,
            ],
            'bookings' => [
                'today_checkins' => $todayCheckins,
                'today_checkouts' => $todayCheckouts,
                'active' => $activeBookings,
                'currently_staying' => $currentlyStaying,
            ],
            'revenue' => [
                'today' => $todayRevenue,
                'this_month' => $monthRevenue,
                'currency' => 'INR',
            ],
            'guests' => [
                'total' => $totalGuests,
                'new_this_month' => $newGuestsThisMonth,
            ],
        ]);
    }
}
