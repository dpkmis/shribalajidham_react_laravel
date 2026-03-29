<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\SiteMetadata;
use Illuminate\Database\Seeder;

class SiteMetadataSeeder extends Seeder
{
    public function run(): void
    {
        SiteMetadata::truncate();
        $pid = Property::first()?->id;
        $order = 0;

        $fields = [
            // ── General ──────────────────────────────────────
            ['group' => 'general', 'key' => 'site_name', 'label' => 'Hotel Name', 'value' => 'Shri BalaJi Dham Hotel', 'type' => 'text'],
            ['group' => 'general', 'key' => 'tagline', 'label' => 'Tagline', 'value' => 'Under Divine Observation', 'type' => 'text'],
            ['group' => 'general', 'key' => 'hero_badge', 'label' => 'Hero Badge Text', 'value' => 'Best Budget Hotel in Mathura', 'type' => 'text'],
            ['group' => 'general', 'key' => 'short_description', 'label' => 'Short Description', 'value' => 'Clean AC rooms near Mathura Railway Station with complimentary breakfast, temple tour guidance, and warm hospitality for pilgrims visiting Krishna Janmabhoomi.', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'logo', 'label' => 'Logo', 'value' => '', 'type' => 'image'],
            ['group' => 'general', 'key' => 'favicon', 'label' => 'Favicon', 'value' => '', 'type' => 'image'],

            // ── Contact ──────────────────────────────────────
            ['group' => 'contact', 'key' => 'phone', 'label' => 'Phone Number', 'value' => '+919639066602', 'type' => 'tel'],
            ['group' => 'contact', 'key' => 'phone_display', 'label' => 'Phone Display Format', 'value' => '+91 96390 66602', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'whatsapp', 'label' => 'WhatsApp Number', 'value' => '919639066602', 'type' => 'tel'],
            ['group' => 'contact', 'key' => 'email', 'label' => 'Email Address', 'value' => 'sribalajidhamhotel@gmail.com', 'type' => 'email'],
            ['group' => 'contact', 'key' => 'address', 'label' => 'Full Address', 'value' => '580 Shankar Gali, Natwar Nagar, Dhauli Pyau, Mathura, Uttar Pradesh — 281001', 'type' => 'textarea'],
            ['group' => 'contact', 'key' => 'address_short', 'label' => 'Short Address', 'value' => 'Dhauli Pyau, Mathura, Uttar Pradesh', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'google_maps_url', 'label' => 'Google Maps URL', 'value' => 'https://maps.google.com/?q=Shri+Balaji+Dham+Hotel+Mathura', 'type' => 'url'],
            ['group' => 'contact', 'key' => 'google_maps_embed', 'label' => 'Google Maps Embed URL', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3533.123!2d77.671!3d27.494!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDI5JzM4LjQiTiA3N8KwNDAnMTUuNiJF!5e0!3m2!1sen!2sin!4v1', 'type' => 'url'],

            // ── Social Media ─────────────────────────────────
            ['group' => 'social', 'key' => 'facebook', 'label' => 'Facebook Page URL', 'value' => 'https://facebook.com/shribalajidham', 'type' => 'url'],
            ['group' => 'social', 'key' => 'instagram', 'label' => 'Instagram URL', 'value' => 'https://instagram.com/shribalajidham', 'type' => 'url'],
            ['group' => 'social', 'key' => 'youtube', 'label' => 'YouTube Channel URL', 'value' => 'https://youtube.com/@shribalajidham', 'type' => 'url'],
            ['group' => 'social', 'key' => 'twitter', 'label' => 'Twitter/X URL', 'value' => '', 'type' => 'url'],
            ['group' => 'social', 'key' => 'google_business', 'label' => 'Google Business Profile', 'value' => '', 'type' => 'url'],
            ['group' => 'social', 'key' => 'tripadvisor', 'label' => 'TripAdvisor URL', 'value' => '', 'type' => 'url'],

            // ── SEO ──────────────────────────────────────────
            ['group' => 'seo', 'key' => 'meta_title', 'label' => 'Meta Title', 'value' => 'Shri BalaJi Dham Hotel Mathura — Best Budget Hotel Near Railway Station', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_description', 'label' => 'Meta Description', 'value' => 'Book clean AC rooms at Shri BalaJi Dham Hotel in Mathura near Railway Station. Complimentary breakfast, temple tour guidance, free WiFi & parking. Starting ₹2,376/night.', 'type' => 'textarea'],
            ['group' => 'seo', 'key' => 'meta_keywords', 'label' => 'Meta Keywords', 'value' => 'hotel in mathura, budget hotel mathura, hotel near mathura railway station, mathura hotel, shri balaji dham, mathura vrindavan hotel', 'type' => 'textarea'],
            ['group' => 'seo', 'key' => 'og_image', 'label' => 'OG Image (Social Share)', 'value' => '', 'type' => 'image'],
            ['group' => 'seo', 'key' => 'google_analytics', 'label' => 'Google Analytics ID', 'value' => '', 'type' => 'text'],

            // ── Hero Section ─────────────────────────────────
            ['group' => 'hero', 'key' => 'hero_title', 'label' => 'Hero Title', 'value' => 'Welcome to Shri BalaJi Dham Hotel in Mathura', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'value' => 'Clean & comfortable rooms near Krishna Janmabhoomi with complimentary breakfast, free WiFi, and temple tour guidance for every pilgrim.', 'type' => 'textarea'],
            ['group' => 'hero', 'key' => 'hero_image', 'label' => 'Hero Background Image', 'value' => '', 'type' => 'image'],
            ['group' => 'hero', 'key' => 'hero_stat_1', 'label' => 'Trust Badge 1', 'value' => '1000+ Happy Pilgrims', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_stat_2', 'label' => 'Trust Badge 2', 'value' => '4.0 Google Rating', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_stat_3', 'label' => 'Trust Badge 3', 'value' => '10 min from Railway Station', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_stat_4', 'label' => 'Trust Badge 4', 'value' => '24/7 Room Service', 'type' => 'text'],

            // ── Policies ─────────────────────────────────────
            ['group' => 'policies', 'key' => 'checkin_time', 'label' => 'Check-In Time', 'value' => '2:00 PM', 'type' => 'time'],
            ['group' => 'policies', 'key' => 'checkout_time', 'label' => 'Check-Out Time', 'value' => '12:00 PM', 'type' => 'time'],
            ['group' => 'policies', 'key' => 'cancellation_policy', 'label' => 'Cancellation Policy', 'value' => 'Free cancellation up to 24 hours before check-in. No-show will be charged for one night.', 'type' => 'textarea'],
            ['group' => 'policies', 'key' => 'pet_policy', 'label' => 'Pet Policy', 'value' => 'Pets are not allowed on the property.', 'type' => 'text'],
            ['group' => 'policies', 'key' => 'smoking_policy', 'label' => 'Smoking Policy', 'value' => 'This is a smoke-free property.', 'type' => 'text'],
            ['group' => 'policies', 'key' => 'nearest_railway', 'label' => 'Nearest Railway Station', 'value' => 'Mathura Junction (MTJ) — 800m / 10 min walk', 'type' => 'text'],
            ['group' => 'policies', 'key' => 'nearest_airport', 'label' => 'Nearest Airport', 'value' => 'Agra Airport (Kheria) — 57 km / 1 hr 15 min', 'type' => 'text'],

            // ── Booking Settings ─────────────────────────────
            ['group' => 'booking', 'key' => 'min_rate', 'label' => 'Starting Room Rate', 'value' => '2376', 'type' => 'number'],
            ['group' => 'booking', 'key' => 'currency', 'label' => 'Currency Symbol', 'value' => '₹', 'type' => 'text'],
            ['group' => 'booking', 'key' => 'booking_email_notification', 'label' => 'Booking Notification Email', 'value' => 'sribalajidhamhotel@gmail.com', 'type' => 'email'],
            ['group' => 'booking', 'key' => 'whatsapp_booking_msg', 'label' => 'WhatsApp Pre-filled Message', 'value' => 'Namaste! I would like to book a room at Shri Balaji Dham Hotel, Mathura. Please share availability and rates.', 'type' => 'textarea'],
        ];

        foreach ($fields as $f) {
            $order++;
            SiteMetadata::create(array_merge($f, [
                'property_id' => $pid,
                'sort_order' => $order,
            ]));
        }

        $this->command->info('Site metadata seeded: ' . count($fields) . ' fields across 7 groups');
    }
}
