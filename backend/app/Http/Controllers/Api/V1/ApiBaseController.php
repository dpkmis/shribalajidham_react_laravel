<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Shri BalaJi Dham HMS API",
 *     description="Hotel Management System REST API for Shri BalaJi Dham Hotel, Mathura. Provides endpoints for room management, bookings, guest management, financials, and more.",
 *     @OA\Contact(
 *         email="sribalajidhamhotel@gmail.com",
 *         name="Shri BalaJi Dham Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     description="Paste token from /api/v1/auth/login (without Bearer prefix)"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Properties", description="Property management")
 * @OA\Tag(name="Room Types", description="Room type master data")
 * @OA\Tag(name="Room Features", description="Room features/amenities master data")
 * @OA\Tag(name="Rooms", description="Room inventory management")
 * @OA\Tag(name="Guests", description="Guest profile management")
 * @OA\Tag(name="Bookings", description="Booking lifecycle management")
 * @OA\Tag(name="Dashboard", description="Dashboard statistics")
 */
class ApiBaseController extends Controller
{
    use ApiResponse;
}
