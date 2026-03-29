<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingActivity;
use App\Models\BookingCharge;
use App\Models\BookingGuest;
use App\Models\BookingPayment;
use App\Models\BookingRoom;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomFeature;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreshWebsiteDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Clean old data (order matters for FK constraints) ─────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BookingActivity::truncate();
        BookingCharge::truncate();
        BookingPayment::truncate();
        BookingGuest::truncate();
        BookingRoom::truncate();
        Booking::truncate();
        Guest::truncate();
        Room::truncate();
        DB::table('room_type_feature')->truncate();
        RoomType::truncate();
        RoomFeature::truncate();
        Property::truncate();
        // Keep roles, permissions, pivot tables — only reset users
        DB::table('personal_access_tokens')->truncate();
        User::whereNotNull('id')->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Old data cleaned.');

        // ── 1. Property ──────────────────────────────────────────────
        $property = Property::create([
            'code' => 'SBD-MTR',
            'name' => 'Shri BalaJi Dham Hotel',
            'time_zone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'meta' => [
                'type' => 'Budget Hotel',
                'address' => '580 Shankar Gali, Natwar Nagar, Dhauli Pyau',
                'city' => 'Mathura',
                'state' => 'Uttar Pradesh',
                'country' => 'India',
                'pincode' => '281001',
                'phone' => '+919639066602',
                'email' => 'sribalajidhamhotel@gmail.com',
                'tagline' => 'Under Divine Observation',
                'google_rating' => 4.0,
                'near_railway_station' => '800m / 10 min walk',
                'rooms' => 10,
                'location' => 'Mathura',
            ],
        ]);
        $this->command->info('Property created: Shri BalaJi Dham Hotel');

        // ── 2. Admin User ────────────────────────────────────────────
        $admin = User::create([
            'property_id' => $property->id,
            'name' => 'Deepak Mishra',
            'email' => 'deepakmishra1166@gmail.com',
            'phone' => '9897443747',
            'password' => 'password',
            'is_active' => true,
            'email_verified' => true,
            'designation' => 'Owner',
            'department' => 'Management',
        ]);
        $admin->assignRole('super-admin');

        $receptionist = User::create([
            'property_id' => $property->id,
            'name' => 'Rahul Sharma',
            'email' => 'reception@shribalajidham.com',
            'phone' => '9639066602',
            'password' => 'password',
            'is_active' => true,
            'email_verified' => true,
            'designation' => 'Front Desk Manager',
            'department' => 'Reception',
        ]);
        $receptionist->assignRole('receptionist');

        $this->command->info('Users created: Admin + Receptionist');

        // ── 3. Room Features (16 Amenities matching website) ─────────
        $features = [];
        $featureData = [
            ['name' => 'Free WiFi', 'code' => 'WIFI', 'icon' => 'FaWifi', 'description' => 'Complimentary high-speed wireless internet across the entire property'],
            ['name' => 'Air Conditioning', 'code' => 'AC', 'icon' => 'FaSnowflake', 'description' => 'Climate-controlled comfort in every room for a restful stay year-round'],
            ['name' => 'LED TV', 'code' => 'LED-TV', 'icon' => 'FaTv', 'description' => 'Flat screen LED television with cable channels for in-room entertainment'],
            ['name' => 'Hot Water', 'code' => 'HOT-WATER', 'icon' => 'FaBath', 'description' => '24/7 hot water supply with premium geyser in every bathroom'],
            ['name' => 'Room Service', 'code' => 'ROOM-SVC', 'icon' => 'FaConciergeBell', 'description' => 'In-room meal delivery and beverages available round the clock'],
            ['name' => 'Free Parking', 'code' => 'PARKING', 'icon' => 'FaParking', 'description' => 'Complimentary on-site parking space for cars, bikes, and traveller buses'],
            ['name' => 'Daily Housekeeping', 'code' => 'HOUSEKEEP', 'icon' => 'FaBroom', 'description' => 'Daily housekeeping with sanitized rooms, fresh linen, and pristine bathrooms'],
            ['name' => 'Complimentary Breakfast', 'code' => 'BREAKFAST', 'icon' => 'FaUtensils', 'description' => 'Fresh Satvik meals, local Mathura delicacies, and complimentary breakfast daily'],
            ['name' => 'CCTV Security', 'code' => 'CCTV', 'icon' => 'FaShieldAlt', 'description' => '24/7 CCTV surveillance and trained security for your peace of mind'],
            ['name' => 'Temple Tour Guidance', 'code' => 'TEMPLE-TOUR', 'icon' => 'FaPray', 'description' => 'Expert local guides to plan your darshan at Mathura & Vrindavan temples'],
            ['name' => 'Pick-up & Drop', 'code' => 'PICKUP', 'icon' => 'FaCar', 'description' => 'Free railway station pick-up and drop service for hassle-free arrival'],
            ['name' => 'Safe Deposit Lockers', 'code' => 'LOCKER', 'icon' => 'FaLock', 'description' => 'Secure lockers and safe-deposit box at front desk for your valuables'],
            ['name' => 'Balcony', 'code' => 'BALCONY', 'icon' => 'FaCoffee', 'description' => 'Private balcony with a view for relaxation and fresh air'],
            ['name' => 'Smart TV', 'code' => 'SMART-TV', 'icon' => 'FaTv', 'description' => 'Smart TV with streaming capabilities and premium channels'],
            ['name' => 'Mini Fridge', 'code' => 'FRIDGE', 'icon' => 'FaSnowflake', 'description' => 'In-room mini fridge for storing beverages and snacks'],
            ['name' => 'Premium Bedding', 'code' => 'BEDDING', 'icon' => 'FaBed', 'description' => 'Comfortable mattresses with quality pillows for a sound night\'s sleep'],
        ];

        foreach ($featureData as $i => $fd) {
            $features[$fd['code']] = RoomFeature::create([
                'property_id' => $property->id,
                'name' => $fd['name'],
                'code' => $fd['code'],
                'icon' => $fd['icon'],
                'description' => $fd['description'],
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }
        $this->command->info('Room features created: ' . count($features));

        // ── 4. Room Types (4 types matching website exactly) ─────────
        $standardType = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Standard Room',
            'code' => 'STD',
            'description' => 'Our cozy Standard Room is ideal for solo travellers and couples visiting Mathura-Vrindavan for darshan. Enjoy a peaceful night\'s rest in a clean, well-maintained space with all essential amenities.',
            'default_rate_cents' => 237600,
            'max_occupancy' => 2,
            'max_adults' => 2,
            'max_children' => 0,
            'beds' => 1,
            'bed_type' => 'twin',
            'room_size_sqm' => 16.72, // 180 sq.ft
            'is_active' => true,
            'sort_order' => 1,
            'images' => [
                'https://images.pexels.com/photos/5461582/pexels-photo-5461582.jpeg?auto=compress&cs=tinysrgb&w=600',
            ],
            'amenities' => ['Free WiFi', 'Air Conditioning', 'LED TV', 'Hot Water', 'Room Service', 'Free Parking'],
        ]);
        $standardType->features()->sync([
            $features['WIFI']->id, $features['AC']->id, $features['LED-TV']->id,
            $features['HOT-WATER']->id, $features['ROOM-SVC']->id, $features['PARKING']->id,
        ]);

        $deluxeType = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Deluxe Room',
            'code' => 'DLX',
            'description' => 'Step into our premium Deluxe Room with elegant interiors, AC, wardrobe, and wall-mounted TV panel. Perfect for couples and small families seeking a comfortable stay near Mathura temples.',
            'default_rate_cents' => 352000,
            'max_occupancy' => 3,
            'max_adults' => 2,
            'max_children' => 1,
            'beds' => 1,
            'bed_type' => 'king',
            'room_size_sqm' => 26.01, // 280 sq.ft
            'is_active' => true,
            'sort_order' => 2,
            'images' => [
                'https://images.pexels.com/photos/16197244/pexels-photo-16197244.jpeg?auto=compress&cs=tinysrgb&w=600',
            ],
            'amenities' => ['High-Speed WiFi', 'Air Conditioning', 'Flat Screen TV', 'Balcony', 'Room Service', 'Free Parking'],
        ]);
        $deluxeType->features()->sync([
            $features['WIFI']->id, $features['AC']->id, $features['LED-TV']->id,
            $features['BALCONY']->id, $features['ROOM-SVC']->id, $features['PARKING']->id,
            $features['FRIDGE']->id,
        ]);

        $familyType = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Family Room',
            'code' => 'FAM',
            'description' => 'Spacious interconnected room designed for families on their Braj Bhoomi yatra. Features a king bed with extra bedding, ample luggage space, and child-friendly amenities for a worry-free pilgrimage.',
            'default_rate_cents' => 440000,
            'max_occupancy' => 4,
            'max_adults' => 2,
            'max_children' => 2,
            'beds' => 2,
            'bed_type' => 'king',
            'room_size_sqm' => 35.30, // 380 sq.ft
            'is_active' => true,
            'sort_order' => 3,
            'images' => [
                '/images/room-family.jpeg',
            ],
            'amenities' => ['High-Speed WiFi', 'Air Conditioning', 'Smart TV', 'Hot Water', '24/7 Service', 'Free Parking'],
        ]);
        $familyType->features()->sync([
            $features['WIFI']->id, $features['AC']->id, $features['SMART-TV']->id,
            $features['HOT-WATER']->id, $features['ROOM-SVC']->id, $features['PARKING']->id,
            $features['BEDDING']->id,
        ]);

        $superiorType = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Superior Double Room',
            'code' => 'SUP',
            'description' => 'Our finest room featuring a double bed and king bed combination, elegant furnishings, and superior amenities. Ideal for extended pilgrim stays and guests seeking the best comfort in Mathura.',
            'default_rate_cents' => 440000,
            'max_occupancy' => 3,
            'max_adults' => 3,
            'max_children' => 0,
            'beds' => 2,
            'bed_type' => 'double',
            'room_size_sqm' => 32.52, // 350 sq.ft
            'is_active' => true,
            'sort_order' => 4,
            'images' => [
                'https://images.pexels.com/photos/18703869/pexels-photo-18703869.jpeg?auto=compress&cs=tinysrgb&w=600',
            ],
            'amenities' => ['High-Speed WiFi', 'Air Conditioning', 'Smart TV', 'Premium Bath', '24/7 Service', 'Free Parking'],
        ]);
        $superiorType->features()->sync([
            $features['WIFI']->id, $features['AC']->id, $features['SMART-TV']->id,
            $features['HOT-WATER']->id, $features['ROOM-SVC']->id, $features['PARKING']->id,
            $features['LOCKER']->id, $features['BEDDING']->id,
        ]);

        $this->command->info('Room types created: Standard, Deluxe, Family, Superior Double');

        // ── 5. Rooms (10 rooms across all types) ─────────────────────
        $roomsData = [
            // Standard Rooms (Ground Floor)
            ['number' => '101', 'type' => $standardType, 'floor' => 1],
            ['number' => '102', 'type' => $standardType, 'floor' => 1],
            ['number' => '103', 'type' => $standardType, 'floor' => 1],
            // Deluxe Rooms (First Floor)
            ['number' => '201', 'type' => $deluxeType, 'floor' => 2],
            ['number' => '202', 'type' => $deluxeType, 'floor' => 2],
            ['number' => '203', 'type' => $deluxeType, 'floor' => 2],
            // Family Rooms (Second Floor)
            ['number' => '301', 'type' => $familyType, 'floor' => 3],
            ['number' => '302', 'type' => $familyType, 'floor' => 3],
            // Superior Rooms (Second Floor)
            ['number' => '303', 'type' => $superiorType, 'floor' => 3],
            ['number' => '304', 'type' => $superiorType, 'floor' => 3],
        ];

        foreach ($roomsData as $rd) {
            Room::create([
                'property_id' => $property->id,
                'room_number' => $rd['number'],
                'room_type_id' => $rd['type']->id,
                'floor' => $rd['floor'],
                'status' => 'available',
                'housekeeping_status' => 'clean',
                'is_smoking' => false,
                'is_accessible' => $rd['floor'] === 1,
                'is_connecting' => false,
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }
        $this->command->info('Rooms created: 10 rooms (3 STD + 3 DLX + 2 FAM + 2 SUP)');

        // ── 6. Sample Guests ─────────────────────────────────────────
        $guests = [];

        $guests[] = Guest::create([
            'property_id' => $property->id,
            'first_name' => 'Rajesh',
            'last_name' => 'Kumar',
            'title' => 'Mr',
            'gender' => 'male',
            'nationality' => 'Indian',
            'email' => 'rajesh.kumar@gmail.com',
            'phone' => '+919876543210',
            'whatsapp' => '+919876543210',
            'address_line1' => 'B-12, Rajouri Garden',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'country' => 'India',
            'postal_code' => '110027',
            'id_type' => 'Aadhaar',
            'id_number' => '9876-5432-1098',
            'guest_type' => 'individual',
            'meal_preference' => 'veg',
            'is_vip' => false,
            'special_requests' => 'Early check-in if possible',
            'created_by_user_id' => $admin->id,
        ]);

        $guests[] = Guest::create([
            'property_id' => $property->id,
            'first_name' => 'Priya',
            'last_name' => 'Sharma',
            'title' => 'Mrs',
            'gender' => 'female',
            'nationality' => 'Indian',
            'email' => 'priya.sharma@yahoo.com',
            'phone' => '+919988776655',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'country' => 'India',
            'id_type' => 'Aadhaar',
            'id_number' => '1234-5678-9012',
            'guest_type' => 'individual',
            'meal_preference' => 'veg',
            'is_vip' => false,
            'created_by_user_id' => $admin->id,
        ]);

        $guests[] = Guest::create([
            'property_id' => $property->id,
            'first_name' => 'Amit',
            'last_name' => 'Gupta',
            'title' => 'Mr',
            'gender' => 'male',
            'nationality' => 'Indian',
            'email' => 'amit.gupta@hotmail.com',
            'phone' => '+919112233445',
            'whatsapp' => '+919112233445',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'country' => 'India',
            'guest_type' => 'individual',
            'meal_preference' => 'veg',
            'is_vip' => true,
            'loyalty_points' => 150,
            'special_requests' => 'Temple tour guidance needed',
            'created_by_user_id' => $admin->id,
        ]);

        $guests[] = Guest::create([
            'property_id' => $property->id,
            'first_name' => 'Sunita',
            'last_name' => 'Devi',
            'title' => 'Mrs',
            'gender' => 'female',
            'nationality' => 'Indian',
            'email' => 'sunita.devi@gmail.com',
            'phone' => '+918877665544',
            'city' => 'Lucknow',
            'state' => 'Uttar Pradesh',
            'country' => 'India',
            'guest_type' => 'individual',
            'meal_preference' => 'veg',
            'is_vip' => false,
            'created_by_user_id' => $admin->id,
        ]);

        $guests[] = Guest::create([
            'property_id' => $property->id,
            'first_name' => 'Vikram',
            'last_name' => 'Singh',
            'title' => 'Mr',
            'gender' => 'male',
            'nationality' => 'Indian',
            'email' => 'vikram.singh@outlook.com',
            'phone' => '+917766554433',
            'city' => 'Agra',
            'state' => 'Uttar Pradesh',
            'country' => 'India',
            'guest_type' => 'individual',
            'meal_preference' => 'veg',
            'is_vip' => false,
            'created_by_user_id' => $admin->id,
        ]);

        $this->command->info('Guests created: 5 sample guests');

        // ── 7. Sample Bookings ───────────────────────────────────────
        $rooms = Room::all();

        // Booking 1: Rajesh Kumar — Currently checked in (Deluxe)
        $b1 = Booking::create([
            'property_id' => $property->id,
            'guest_id' => $guests[0]->id,
            'status' => 'checked-in',
            'source' => 'website',
            'checkin_date' => now()->subDay(),
            'checkout_date' => now()->addDays(2),
            'actual_checkin_at' => now()->subDay()->setTime(14, 15),
            'number_of_adults' => 2,
            'number_of_children' => 0,
            'currency' => 'INR',
            'room_charges_cents' => 1056000, // 3520 * 3 nights
            'total_amount_cents' => 1056000,
            'paid_amount_cents' => 1056000,
            'balance_amount_cents' => 0,
            'payment_status' => 'paid',
            'special_requests' => 'Ground floor room preferred',
            'arrival_time' => '14:00',
            'created_by_user_id' => $receptionist->id,
        ]);
        $room201 = $rooms->where('room_number', '201')->first();
        BookingRoom::create([
            'booking_id' => $b1->id, 'room_id' => $room201->id,
            'room_type_id' => $deluxeType->id,
            'checkin_date' => now()->subDay(), 'checkout_date' => now()->addDays(2),
            'rate_per_night_cents' => 352000, 'final_rate_cents' => 1056000,
            'adults' => 2, 'status' => 'confirmed',
        ]);
        $room201->update(['status' => 'occupied', 'housekeeping_status' => 'dirty']);

        // Booking 2: Priya Sharma — Upcoming confirmed (Family Room)
        $b2 = Booking::create([
            'property_id' => $property->id,
            'guest_id' => $guests[1]->id,
            'status' => 'confirmed',
            'source' => 'phone',
            'checkin_date' => now()->addDays(3),
            'checkout_date' => now()->addDays(5),
            'number_of_adults' => 2,
            'number_of_children' => 2,
            'currency' => 'INR',
            'room_charges_cents' => 880000, // 4400 * 2 nights
            'total_amount_cents' => 880000,
            'paid_amount_cents' => 440000,
            'balance_amount_cents' => 440000,
            'payment_status' => 'partially-paid',
            'special_requests' => 'Extra bed for children, temple tour guidance',
            'arrival_time' => '11:00',
            'created_by_user_id' => $receptionist->id,
        ]);
        $room301 = $rooms->where('room_number', '301')->first();
        BookingRoom::create([
            'booking_id' => $b2->id, 'room_id' => $room301->id,
            'room_type_id' => $familyType->id,
            'checkin_date' => now()->addDays(3), 'checkout_date' => now()->addDays(5),
            'rate_per_night_cents' => 440000, 'final_rate_cents' => 880000,
            'adults' => 2, 'children' => 2, 'status' => 'confirmed',
        ]);
        $room301->update(['status' => 'reserved']);

        // Booking 3: Amit Gupta — Checked out last week (Superior)
        $b3 = Booking::create([
            'property_id' => $property->id,
            'guest_id' => $guests[2]->id,
            'status' => 'checked-out',
            'source' => 'walk-in',
            'checkin_date' => now()->subDays(7),
            'checkout_date' => now()->subDays(4),
            'actual_checkin_at' => now()->subDays(7)->setTime(14, 30),
            'actual_checkout_at' => now()->subDays(4)->setTime(11, 45),
            'number_of_adults' => 2,
            'number_of_children' => 0,
            'currency' => 'INR',
            'room_charges_cents' => 1320000, // 4400 * 3 nights
            'total_amount_cents' => 1320000,
            'paid_amount_cents' => 1320000,
            'balance_amount_cents' => 0,
            'payment_status' => 'paid',
            'special_requests' => 'Braj 84 Kos Yatra tour package',
            'created_by_user_id' => $admin->id,
        ]);
        BookingRoom::create([
            'booking_id' => $b3->id, 'room_id' => $rooms->where('room_number', '303')->first()->id,
            'room_type_id' => $superiorType->id,
            'checkin_date' => now()->subDays(7), 'checkout_date' => now()->subDays(4),
            'rate_per_night_cents' => 440000, 'final_rate_cents' => 1320000,
            'adults' => 2, 'status' => 'checked-out',
        ]);

        // Booking 4: Sunita Devi — Today's check-in (Standard)
        $b4 = Booking::create([
            'property_id' => $property->id,
            'guest_id' => $guests[3]->id,
            'status' => 'confirmed',
            'source' => 'website',
            'checkin_date' => now(),
            'checkout_date' => now()->addDays(2),
            'number_of_adults' => 1,
            'number_of_children' => 0,
            'currency' => 'INR',
            'room_charges_cents' => 475200, // 2376 * 2 nights
            'total_amount_cents' => 475200,
            'paid_amount_cents' => 0,
            'balance_amount_cents' => 475200,
            'payment_status' => 'unpaid',
            'special_requests' => 'Vishram Ghat Aarti darshan assistance',
            'arrival_time' => '15:00',
            'created_by_user_id' => $receptionist->id,
        ]);
        $room101 = $rooms->where('room_number', '101')->first();
        BookingRoom::create([
            'booking_id' => $b4->id, 'room_id' => $room101->id,
            'room_type_id' => $standardType->id,
            'checkin_date' => now(), 'checkout_date' => now()->addDays(2),
            'rate_per_night_cents' => 237600, 'final_rate_cents' => 475200,
            'adults' => 1, 'status' => 'confirmed',
        ]);
        $room101->update(['status' => 'reserved']);

        // Booking 5: Vikram Singh — Upcoming next week (Standard)
        $b5 = Booking::create([
            'property_id' => $property->id,
            'guest_id' => $guests[4]->id,
            'status' => 'confirmed',
            'source' => 'phone',
            'checkin_date' => now()->addDays(7),
            'checkout_date' => now()->addDays(9),
            'number_of_adults' => 2,
            'number_of_children' => 0,
            'currency' => 'INR',
            'room_charges_cents' => 475200,
            'total_amount_cents' => 475200,
            'paid_amount_cents' => 237600,
            'balance_amount_cents' => 237600,
            'payment_status' => 'partially-paid',
            'special_requests' => 'Agra-Mathura Heritage tour',
            'arrival_time' => '12:00',
            'created_by_user_id' => $admin->id,
        ]);
        BookingRoom::create([
            'booking_id' => $b5->id, 'room_id' => $rooms->where('room_number', '102')->first()->id,
            'room_type_id' => $standardType->id,
            'checkin_date' => now()->addDays(7), 'checkout_date' => now()->addDays(9),
            'rate_per_night_cents' => 237600, 'final_rate_cents' => 475200,
            'adults' => 2, 'status' => 'confirmed',
        ]);

        $this->command->info('Bookings created: 5 sample bookings');
        $this->command->info('');
        $this->command->info('=== SEED COMPLETE ===');
        $this->command->info('Admin Login: deepakmishra1166@gmail.com / password');
        $this->command->info('Receptionist: reception@shribalajidham.com / password');
        $this->command->info('Rooms: 10 (3 STD + 3 DLX + 2 FAM + 2 SUP)');
        $this->command->info('Guests: 5 | Bookings: 5');
    }
}
