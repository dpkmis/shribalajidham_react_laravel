<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\FestivalOffer;
use App\Models\GalleryImage;
use App\Models\NearbyAttraction;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\TourPackage;
use Illuminate\Database\Seeder;

class WebsiteMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $prop = Property::first();
        $pid = $prop?->id;

        // ── Tour Packages ────────────────────────────────────
        $this->command->info('Seeding Tour Packages...');
        TourPackage::truncate();

        TourPackage::create(['property_id' => $pid, 'name' => 'Mathura Darshan', 'duration' => '1 Day', 'price_cents' => 149900, 'price_label' => 'per person', 'group_size' => '2-10 people', 'places_covered' => 'Mathura', 'is_popular' => false, 'sort_order' => 1, 'description' => 'Explore the birthplace of Lord Krishna with our guided Mathura Darshan covering all major temples and ghats in one day.', 'includes' => ['Sri Krishna Janmabhoomi Temple', 'Dwarkadhish Temple Darshan', 'Vishram Ghat Visit & Aarti', 'Kusum Sarovar', 'Mathura Museum Tour', 'AC Transport & Guide', 'Vegetarian Lunch']]);
        TourPackage::create(['property_id' => $pid, 'name' => 'Vrindavan Temple Tour', 'duration' => '1 Day', 'price_cents' => 179900, 'price_label' => 'per person', 'group_size' => '2-15 people', 'places_covered' => 'Vrindavan', 'is_popular' => true, 'sort_order' => 2, 'description' => 'Visit the most sacred temples of Vrindavan including Banke Bihari, ISKCON, and the spectacular Prem Mandir light show.', 'includes' => ['Banke Bihari Temple Darshan', 'ISKCON Temple & Prasad', 'Prem Mandir (Light Show)', 'Nidhivan Sacred Grove', 'Radha Raman Temple', 'Seva Kunj & Kesi Ghat', 'AC Transport & All Meals']]);
        TourPackage::create(['property_id' => $pid, 'name' => 'Braj 84 Kos Yatra', 'duration' => '3 Days / 2 Nights', 'price_cents' => 599900, 'price_label' => 'per person', 'group_size' => '4-20 people', 'places_covered' => 'Mathura, Vrindavan, Govardhan, Barsana', 'is_popular' => false, 'sort_order' => 3, 'description' => 'The complete Braj pilgrimage covering all sacred sites across Mathura, Vrindavan, Govardhan, and Barsana over 3 days.', 'includes' => ['All Major Temples & Ghats', 'Govardhan Parikrama', 'Barsana & Nandgaon Visit', 'Radha Kund Darshan', 'Hotel Stay at Shri Balaji Dham (2N)', 'All Meals Included', 'AC Transport & Expert Guide']]);
        TourPackage::create(['property_id' => $pid, 'name' => 'Agra-Mathura Heritage', 'duration' => '2 Days / 1 Night', 'price_cents' => 399900, 'price_label' => 'per person', 'group_size' => '2-10 people', 'places_covered' => 'Mathura & Agra', 'is_popular' => false, 'sort_order' => 4, 'description' => 'Combine the spiritual experience of Mathura with the architectural wonder of the Taj Mahal in this 2-day heritage tour.', 'includes' => ['Taj Mahal Sunrise Visit', 'Agra Fort Heritage Tour', 'Krishna Janmabhoomi Darshan', 'Vrindavan Temple Circuit', 'Hotel Stay at Shri Balaji Dham (1N)', 'All Meals & AC Transport', 'Professional Heritage Guide']]);

        // ── Festival Offers ──────────────────────────────────
        $this->command->info('Seeding Festival Offers...');
        FestivalOffer::truncate();

        FestivalOffer::create(['property_id' => $pid, 'name' => 'Janmashtami 2026', 'hindi_name' => 'जन्माष्टमी', 'festival_month' => 'August 2026', 'price_cents' => 699900, 'per_night_cents' => 349900, 'nights' => '2N', 'highlight_badge' => 'Biggest Festival', 'sort_order' => 1, 'gradient_from' => '#1a237e', 'gradient_to' => '#4a148c', 'description' => 'Experience the grand Janmashtami celebrations at Krishna Janmabhoomi with midnight darshan and special puja arrangements.', 'includes' => ['2 Nights Stay at Shri BalaJi Dham', 'Midnight Darshan at Krishna Janmabhoomi', 'Vrindavan Temple Tour', 'All Meals (Pure Veg Satvik)', 'AC Transport & Guide', 'Special Festival Puja Arrangement']]);
        FestivalOffer::create(['property_id' => $pid, 'name' => 'Holi in Mathura 2027', 'hindi_name' => 'होली', 'festival_month' => 'March 2027', 'price_cents' => 899900, 'per_night_cents' => 299900, 'nights' => '3N', 'highlight_badge' => 'Most Colorful', 'sort_order' => 2, 'gradient_from' => '#e65100', 'gradient_to' => '#f57f17', 'description' => 'Witness the world-famous Lathmar Holi of Barsana and Phoolon ki Holi at Banke Bihari Temple.', 'includes' => ['3 Nights Stay at Shri BalaJi Dham', 'Lathmar Holi at Barsana & Nandgaon', 'Phoolon ki Holi at Banke Bihari Temple', 'Dwarkadhish Temple Holi Celebration', 'All Meals & Refreshments', 'AC Transport for All Events']]);
        FestivalOffer::create(['property_id' => $pid, 'name' => 'Diwali in Mathura 2026', 'hindi_name' => 'दीपावली', 'festival_month' => 'October 2026', 'price_cents' => 599900, 'per_night_cents' => 299900, 'nights' => '2N', 'highlight_badge' => 'Festival of Lights', 'sort_order' => 3, 'gradient_from' => '#b71c1c', 'gradient_to' => '#e65100', 'description' => 'Celebrate the festival of lights in the holy city of Mathura with Diwali Aarti at Vishram Ghat.', 'includes' => ['2 Nights Stay at Shri BalaJi Dham', 'Diwali Aarti at Vishram Ghat', 'Krishna Janmabhoomi Darshan', 'Vrindavan Temple Light Tour', 'Special Diwali Dinner', 'AC Transport & Guide']]);

        // ── Testimonials ─────────────────────────────────────
        $this->command->info('Seeding Testimonials...');
        Testimonial::truncate();

        Testimonial::create(['property_id' => $pid, 'guest_name' => 'Rajesh Kumar', 'guest_location' => 'New Delhi', 'rating' => 5, 'review_text' => 'Excellent stay! The rooms were spotlessly clean and the staff was extremely helpful with temple darshan arrangements. The complimentary breakfast was delicious. Highly recommend for anyone visiting Mathura for pilgrimage.', 'stay_date' => '2026-01-15', 'source' => 'Google', 'is_featured' => true, 'sort_order' => 1]);
        Testimonial::create(['property_id' => $pid, 'guest_name' => 'Priya Sharma', 'guest_location' => 'Mumbai', 'rating' => 5, 'review_text' => 'We stayed here during Janmashtami and it was amazing! The hotel arranged everything for the midnight darshan. Rooms are clean, food is pure veg and tasty. Perfect location near the railway station. Will definitely come back!', 'stay_date' => '2025-08-22', 'source' => 'Google', 'is_featured' => true, 'sort_order' => 2]);
        Testimonial::create(['property_id' => $pid, 'guest_name' => 'Amit Gupta', 'guest_location' => 'Jaipur', 'rating' => 5, 'review_text' => 'Best budget hotel in Mathura! The location is perfect — just 10 minutes walk from the railway station. Staff helped us plan our entire Braj yatra. The temple tour guidance was exceptional. Clean rooms and great hospitality!', 'stay_date' => '2026-02-10', 'source' => 'Google', 'is_featured' => true, 'sort_order' => 3]);
        Testimonial::create(['property_id' => $pid, 'guest_name' => 'Sunita Devi', 'guest_location' => 'Lucknow', 'rating' => 4, 'review_text' => 'Good hotel for pilgrims. Rooms are neat and tidy. The free pick-up from the station was very helpful. Breakfast included with the room was a nice touch. Only suggestion — the WiFi could be a bit faster.', 'stay_date' => '2026-03-05', 'source' => 'TripAdvisor', 'is_featured' => false, 'sort_order' => 4]);
        Testimonial::create(['property_id' => $pid, 'guest_name' => 'Vikram Singh', 'guest_location' => 'Agra', 'rating' => 5, 'review_text' => 'Stayed with my family for 3 nights during our temple tour. The family room was spacious and well-maintained. Kids loved the food. The Braj 84 Kos Yatra package they arranged was perfectly organized. Will recommend to all friends!', 'stay_date' => '2026-03-12', 'source' => 'Google', 'is_featured' => true, 'sort_order' => 5]);

        // ── Blog Posts ───────────────────────────────────────
        $this->command->info('Seeding Blog Posts...');
        BlogPost::truncate();

        BlogPost::create(['property_id' => $pid, 'title' => 'Top 10 Temples to Visit in Mathura-Vrindavan', 'subtitle' => 'A Complete Guide for Pilgrims', 'icon' => 'FaPray', 'read_time_min' => 5, 'author' => 'Shri BalaJi Dham Team', 'sort_order' => 1, 'is_published' => true, 'published_at' => now()->subDays(30), 'excerpt' => 'Discover the most sacred temples of Mathura and Vrindavan with our detailed guide covering timings, significance, and travel tips.', 'content' => '<h3>1. Sri Krishna Janmabhoomi</h3><p>The birthplace of Lord Krishna is the most sacred site in Mathura. Located just 3.5 km from our hotel.</p><h3>2. Dwarkadhish Temple</h3><p>Built in 1814, this temple is dedicated to Lord Krishna as the King of Dwarka.</p><h3>3. Banke Bihari Temple</h3><p>One of the most visited temples in Vrindavan, known for its unique darshan style.</p><h3>4. ISKCON Temple</h3><p>The magnificent ISKCON temple in Vrindavan attracts devotees from around the world.</p><h3>5. Prem Mandir</h3><p>The stunning white marble temple with an incredible light show every evening at 7:30 PM.</p>']);
        BlogPost::create(['property_id' => $pid, 'title' => 'Mathura Vrindavan 2-Day Itinerary for Families', 'subtitle' => 'Plan Your Perfect Pilgrimage', 'icon' => 'FaMapMarkedAlt', 'read_time_min' => 4, 'author' => 'Shri BalaJi Dham Team', 'sort_order' => 2, 'is_published' => true, 'published_at' => now()->subDays(15), 'excerpt' => 'A detailed day-by-day itinerary for families visiting Mathura and Vrindavan, including budget tips and must-see spots.', 'content' => '<h3>Day 1: Mathura Darshan</h3><p><strong>Morning:</strong> Krishna Janmabhoomi Temple (allow 2 hours)</p><p><strong>Afternoon:</strong> Dwarkadhish Temple and lunch</p><p><strong>Evening:</strong> Vishram Ghat Aarti (starts at sunset)</p><h3>Day 2: Vrindavan Tour</h3><p><strong>Morning:</strong> Banke Bihari Temple (go early to avoid crowds)</p><p><strong>Afternoon:</strong> ISKCON Temple and Radha Raman Temple</p><p><strong>Evening:</strong> Prem Mandir light show at 7:30 PM</p><h3>Budget for Family of 4</h3><p>Hotel: ₹4,400/night (Family Room)<br>Meals: ₹1,500/day<br>Transport: ₹800/day<br>Total: ~₹12,000 for 2 days</p>']);

        // ── Gallery ──────────────────────────────────────────
        $this->command->info('Seeding Gallery Images...');
        GalleryImage::truncate();

        GalleryImage::create(['property_id' => $pid, 'title' => 'Hotel Entrance', 'caption' => 'Welcome to Shri BalaJi Dham — neon signage & aquarium lobby', 'category' => 'hotel', 'image' => '/images/hotel-entrance.png', 'sort_order' => 1]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Family Room', 'caption' => 'Spacious twin bed with AC — perfect for families', 'category' => 'rooms', 'image' => '/images/room-family.jpeg', 'sort_order' => 2]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Deluxe Room', 'caption' => 'AC, wardrobe & TV panel with elegant interiors', 'category' => 'rooms', 'image' => 'https://images.pexels.com/photos/16197244/pexels-photo-16197244.jpeg?auto=compress&cs=tinysrgb&w=600', 'sort_order' => 3]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Hotel Corridor', 'caption' => 'Spotless marble flooring and well-lit hallways', 'category' => 'hotel', 'image' => '/images/hotel-corridor.jpeg', 'sort_order' => 4]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Krishna Janmabhoomi Temple', 'caption' => 'The sacred birthplace of Lord Krishna — 3.5 km from hotel', 'category' => 'mathura', 'image' => 'https://images.pexels.com/photos/17468302/pexels-photo-17468302.jpeg?auto=compress&cs=tinysrgb&w=600', 'sort_order' => 5]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Evening Aarti at Ghats', 'caption' => 'Vishram Ghat evening aarti — a divine experience', 'category' => 'mathura', 'image' => 'https://images.pexels.com/photos/5458388/pexels-photo-5458388.jpeg?auto=compress&cs=tinysrgb&w=600', 'sort_order' => 6]);
        GalleryImage::create(['property_id' => $pid, 'title' => 'Taj Mahal', 'caption' => 'Agra Day Trip — just 58 km from Mathura', 'category' => 'tours', 'image' => 'https://images.pexels.com/photos/1583339/pexels-photo-1583339.jpeg?auto=compress&cs=tinysrgb&w=600', 'sort_order' => 7]);

        // ── Nearby Attractions ───────────────────────────────
        $this->command->info('Seeding Nearby Attractions...');
        NearbyAttraction::truncate();

        NearbyAttraction::create(['property_id' => $pid, 'name' => 'Sri Krishna Janmabhoomi', 'distance' => '3.5 km', 'travel_time' => '10 min drive', 'category' => 'temple', 'sort_order' => 1, 'description' => 'The sacred birthplace of Lord Krishna, one of the holiest sites in Hinduism. Features the prison cell where Lord Krishna was born.', 'highlights' => ['Open 5 AM - 9 PM', 'Free entry', 'Midnight Darshan on Janmashtami', 'Photography restricted inside']]);
        NearbyAttraction::create(['property_id' => $pid, 'name' => 'Dwarkadhish Temple', 'distance' => '3.8 km', 'travel_time' => '12 min drive', 'category' => 'temple', 'sort_order' => 2, 'description' => 'Built in 1814 by Seth Gokul Das Parikh, this stunning temple is dedicated to Lord Krishna as the King of Dwarka.', 'highlights' => ['Morning Aarti at 6:30 AM', 'Evening Aarti at 7 PM', 'Rajasthani architectural style']]);
        NearbyAttraction::create(['property_id' => $pid, 'name' => 'Vishram Ghat', 'distance' => '3.5 km', 'travel_time' => '10 min drive', 'category' => 'ghat', 'sort_order' => 3, 'description' => 'The most sacred ghat in Mathura where Lord Krishna rested after slaying the demon Kansa. Evening Aarti is a must-see.', 'highlights' => ['Evening Aarti at sunset', 'Boat rides available', 'Holy dip in Yamuna']]);
        NearbyAttraction::create(['property_id' => $pid, 'name' => 'ISKCON Temple Vrindavan', 'distance' => '12 km', 'travel_time' => '25 min drive', 'category' => 'temple', 'sort_order' => 4, 'description' => 'The magnificent ISKCON temple in Vrindavan with stunning architecture and spiritual atmosphere.', 'highlights' => ['Open 4:30 AM - 8:30 PM', 'Free Prasad available', 'Evening kirtan at 6 PM']]);
        NearbyAttraction::create(['property_id' => $pid, 'name' => 'Banke Bihari Temple', 'distance' => '11 km', 'travel_time' => '22 min drive', 'category' => 'temple', 'sort_order' => 5, 'description' => 'One of the most visited temples in Vrindavan, famous for its unique curtain darshan where the deity is never left open.', 'highlights' => ['Best time: early morning', 'Very crowded during festivals', 'No photography allowed']]);
        NearbyAttraction::create(['property_id' => $pid, 'name' => 'Prem Mandir', 'distance' => '13 km', 'travel_time' => '28 min drive', 'category' => 'temple', 'sort_order' => 6, 'description' => 'A stunning white marble temple with incredible architectural detail and a spectacular musical fountain light show every evening.', 'highlights' => ['Light show at 7:30 PM daily', 'Free entry', 'Best visited in evening']]);

        $this->command->info('');
        $this->command->info('=== Website Master Data Seeded ===');
        $this->command->info('Tour Packages: 4 | Festival Offers: 3 | Testimonials: 5');
        $this->command->info('Blog Posts: 2 | Gallery: 7 | Nearby Attractions: 6');
    }
}
