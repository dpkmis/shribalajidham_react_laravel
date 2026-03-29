<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\GuestController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\RoomFeatureController;
use App\Http\Controllers\Api\V1\RoomTypeController;
use App\Http\Controllers\Api\V1\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
| All routes are prefixed with /api/v1
| Public routes: no auth needed (for website frontend)
| Protected routes: Sanctum bearer token required (for admin panel)
*/

Route::prefix('v1')->group(function () {

    // ── Public Website Endpoints (No Auth) ──────────────────────────
    Route::prefix('public')->group(function () {
        Route::get('room-types', [RoomTypeController::class, 'index']);
        Route::get('room-types/{id}', [RoomTypeController::class, 'show']);
        Route::get('room-features', [RoomFeatureController::class, 'index']);
        Route::get('rooms/available', [RoomController::class, 'available']);
        Route::get('properties', [PropertyController::class, 'index']);
        Route::post('booking', [PublicController::class, 'createBooking']);

        // Site metadata
        Route::get('metadata', [PublicController::class, 'siteMetadata']);

        // Master Data for website frontend
        Route::get('tour-packages', [PublicController::class, 'tourPackages']);
        Route::get('festival-offers', [PublicController::class, 'festivalOffers']);
        Route::get('testimonials', [PublicController::class, 'testimonials']);
        Route::get('blog-posts', [PublicController::class, 'blogPosts']);
        Route::get('gallery', [PublicController::class, 'gallery']);
        Route::get('nearby-attractions', [PublicController::class, 'nearbyAttractions']);
    });

    // ── Public Auth ──────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');
        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
    });

    // ── Protected Routes (Bearer Token Required) ─────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::get('profile', [AuthController::class, 'profile'])->name('api.v1.auth.profile');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('api.v1.auth.refresh');
        });

        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('api.v1.dashboard.stats');

        // Properties
        Route::get('properties', [PropertyController::class, 'index'])->name('api.v1.properties.index');
        Route::get('properties/{id}', [PropertyController::class, 'show'])->name('api.v1.properties.show');

        // Room Types (Master Data)
        Route::apiResource('room-types', RoomTypeController::class)->names([
            'index' => 'api.v1.room-types.index',
            'store' => 'api.v1.room-types.store',
            'show' => 'api.v1.room-types.show',
            'update' => 'api.v1.room-types.update',
            'destroy' => 'api.v1.room-types.destroy',
        ]);

        // Room Features (Master Data)
        Route::apiResource('room-features', RoomFeatureController::class)->names([
            'index' => 'api.v1.room-features.index',
            'store' => 'api.v1.room-features.store',
            'show' => 'api.v1.room-features.show',
            'update' => 'api.v1.room-features.update',
            'destroy' => 'api.v1.room-features.destroy',
        ]);

        // Rooms
        Route::get('rooms/available', [RoomController::class, 'available'])->name('api.v1.rooms.available');
        Route::get('rooms/status-summary', [RoomController::class, 'statusSummary'])->name('api.v1.rooms.status-summary');
        Route::apiResource('rooms', RoomController::class)->names([
            'index' => 'api.v1.rooms.index',
            'store' => 'api.v1.rooms.store',
            'show' => 'api.v1.rooms.show',
            'update' => 'api.v1.rooms.update',
            'destroy' => 'api.v1.rooms.destroy',
        ]);

        // Guests
        Route::get('guests/search', [GuestController::class, 'search'])->name('api.v1.guests.search');
        Route::apiResource('guests', GuestController::class)->names([
            'index' => 'api.v1.guests.index',
            'store' => 'api.v1.guests.store',
            'show' => 'api.v1.guests.show',
            'update' => 'api.v1.guests.update',
            'destroy' => 'api.v1.guests.destroy',
        ]);

        // Bookings
        Route::get('bookings/today', [BookingController::class, 'today'])->name('api.v1.bookings.today');
        Route::post('bookings/{id}/checkin', [BookingController::class, 'checkin'])->name('api.v1.bookings.checkin');
        Route::post('bookings/{id}/checkout', [BookingController::class, 'checkout'])->name('api.v1.bookings.checkout');
        Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('api.v1.bookings.cancel');
        Route::apiResource('bookings', BookingController::class)->only(['index', 'show', 'store'])->names([
            'index' => 'api.v1.bookings.index',
            'store' => 'api.v1.bookings.store',
            'show' => 'api.v1.bookings.show',
        ]);
    });
});
