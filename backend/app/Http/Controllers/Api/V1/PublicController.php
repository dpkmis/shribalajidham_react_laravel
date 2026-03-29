<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\BookingResource;
use App\Models\SiteMetadata;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\FestivalOffer;
use App\Models\GalleryImage;
use App\Models\Guest;
use App\Models\NearbyAttraction;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Testimonial;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class PublicController extends ApiBaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/public/booking",
     *     operationId="publicCreateBooking",
     *     tags={"Public"},
     *     summary="Create booking from website (no auth required)",
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","phone","email","check_in","check_out","room_type_id","guests"},
     *             @OA\Property(property="name", type="string", example="Rajesh Kumar"),
     *             @OA\Property(property="phone", type="string", example="+919876543210"),
     *             @OA\Property(property="email", type="string", example="rajesh@example.com"),
     *             @OA\Property(property="check_in", type="string", format="date", example="2026-04-01"),
     *             @OA\Property(property="check_out", type="string", format="date", example="2026-04-03"),
     *             @OA\Property(property="room_type_id", type="integer", example=1),
     *             @OA\Property(property="guests", type="integer", example=2),
     *             @OA\Property(property="special_requests", type="string", example="Early check-in")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Booking created"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=409, description="No rooms available")
     * )
     */
    public function createBooking(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'room_type_id' => 'required|exists:room_types,id',
            'guests' => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request) {
            $property = Property::first();
            if (!$property) {
                return $this->error('No property configured', 500);
            }

            $roomType = RoomType::find($request->room_type_id);

            // Find or create guest by phone
            $nameParts = explode(' ', $request->name, 2);
            $guest = Guest::firstOrCreate(
                ['phone' => $request->phone, 'property_id' => $property->id],
                [
                    'property_id' => $property->id,
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $request->email,
                    'guest_type' => 'individual',
                ]
            );

            // Find an available room of requested type
            $room = Room::where('room_type_id', $request->room_type_id)
                ->available()
                ->first();

            $nights = \Carbon\Carbon::parse($request->check_in)
                ->diffInDays(\Carbon\Carbon::parse($request->check_out));

            $ratePerNight = $roomType->default_rate_cents;
            $totalAmount = $ratePerNight * $nights;

            // Create booking
            $booking = Booking::create([
                'property_id' => $property->id,
                'guest_id' => $guest->id,
                'status' => 'pending',
                'source' => 'website',
                'checkin_date' => $request->check_in,
                'checkout_date' => $request->check_out,
                'number_of_adults' => $request->guests,
                'number_of_children' => 0,
                'number_of_infants' => 0,
                'currency' => 'INR',
                'room_charges_cents' => $totalAmount,
                'total_amount_cents' => $totalAmount,
                'paid_amount_cents' => 0,
                'balance_amount_cents' => $totalAmount,
                'payment_status' => 'unpaid',
                'special_requests' => $request->special_requests,
            ]);

            // Assign room if available
            if ($room) {
                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $room->id,
                    'room_type_id' => $request->room_type_id,
                    'checkin_date' => $request->check_in,
                    'checkout_date' => $request->check_out,
                    'rate_per_night_cents' => $ratePerNight,
                    'final_rate_cents' => $totalAmount,
                    'adults' => $request->guests,
                    'status' => 'confirmed',
                ]);
                $room->update(['status' => 'reserved']);
                $booking->update(['status' => 'confirmed']);
            }

            $booking->logActivity('created', 'Booking created from website');
            $booking->load(['guest', 'bookingRooms.room']);

            return $this->created([
                'booking_reference' => $booking->booking_reference,
                'status' => $booking->status,
                'guest_name' => $guest->full_name,
                'room_type' => $roomType->name,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'nights' => $nights,
                'total_amount' => $totalAmount / 100,
                'currency' => 'INR',
                'message' => $room
                    ? "Room {$room->room_number} has been reserved for you."
                    : "Your booking request is received. We will confirm room availability shortly.",
            ], 'Booking created successfully');
        });
    }

    // ── Site Metadata ─────────────────────────────────────────────

    public function siteMetadata(): JsonResponse
    {
        $data = Cache::remember('public:metadata', 300, function () {
            $data = SiteMetadata::getAllGrouped();
            foreach ($data as $group => &$items) {
                foreach ($items as $key => &$val) {
                    if ($val && str_starts_with($val, '/storage/')) {
                        $val = url($val);
                    }
                }
            }
            return $data;
        });
        return $this->success($data);
    }

    // ── Master Data Public APIs ──────────────────────────────────

    public function tourPackages(): JsonResponse
    {
        $data = Cache::remember('public:tour-packages', 300, fn() => TourPackage::active()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'name' => $r->name, 'slug' => $r->slug,
            'description' => $r->description, 'duration' => $r->duration,
            'price' => $r->price, 'price_label' => $r->price_label,
            'group_size' => $r->group_size, 'places_covered' => $r->places_covered,
            'includes' => $r->includes, 'image' => $r->image ? url($r->image) : null,
            'is_popular' => $r->is_popular,
        ])->toArray());
        return $this->success($data);
    }

    public function festivalOffers(): JsonResponse
    {
        $data = Cache::remember('public:festival-offers', 300, fn() => FestivalOffer::active()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'name' => $r->name, 'slug' => $r->slug,
            'hindi_name' => $r->hindi_name, 'description' => $r->description,
            'festival_month' => $r->festival_month, 'price' => $r->price,
            'per_night' => $r->per_night, 'nights' => $r->nights,
            'highlight_badge' => $r->highlight_badge, 'includes' => $r->includes,
            'image' => $r->image ? url($r->image) : null,
            'gradient_from' => $r->gradient_from, 'gradient_to' => $r->gradient_to,
        ])->toArray());
        return $this->success($data);
    }

    public function testimonials(): JsonResponse
    {
        $data = Cache::remember('public:testimonials', 300, fn() => Testimonial::active()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'guest_name' => $r->guest_name,
            'guest_location' => $r->guest_location, 'rating' => $r->rating,
            'review_text' => $r->review_text, 'stay_date' => $r->stay_date?->toDateString(),
            'source' => $r->source, 'is_featured' => $r->is_featured,
        ])->toArray());
        return $this->success($data);
    }

    public function blogPosts(): JsonResponse
    {
        $data = Cache::remember('public:blog-posts', 300, fn() => BlogPost::published()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'title' => $r->title, 'slug' => $r->slug,
            'subtitle' => $r->subtitle, 'excerpt' => $r->excerpt,
            'content' => $r->content, 'image' => $r->image ? url($r->image) : null,
            'icon' => $r->icon, 'read_time_min' => $r->read_time_min,
            'author' => $r->author, 'published_at' => $r->published_at?->toDateString(),
        ])->toArray());
        return $this->success($data);
    }

    public function gallery(): JsonResponse
    {
        $data = Cache::remember('public:gallery', 300, fn() => GalleryImage::active()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'title' => $r->title, 'caption' => $r->caption,
            'category' => $r->category, 'image' => $r->image ? url($r->image) : null,
        ])->toArray());
        return $this->success($data);
    }

    public function nearbyAttractions(): JsonResponse
    {
        $data = Cache::remember('public:nearby-attractions', 300, fn() => NearbyAttraction::active()->ordered()->get()->map(fn($r) => [
            'id' => $r->id, 'name' => $r->name, 'description' => $r->description,
            'distance' => $r->distance, 'travel_time' => $r->travel_time,
            'image' => $r->image ? url($r->image) : null,
            'category' => $r->category, 'highlights' => $r->highlights,
        ])->toArray());
        return $this->success($data);
    }
}
