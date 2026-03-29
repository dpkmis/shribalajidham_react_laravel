<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('duration', 50);
            $table->integer('price_cents');
            $table->string('price_label', 50)->default('per person');
            $table->string('group_size', 50)->nullable();
            $table->string('places_covered', 255)->nullable();
            $table->json('includes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('festival_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('hindi_name', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('festival_month', 50);
            $table->integer('price_cents');
            $table->integer('per_night_cents')->nullable();
            $table->string('nights', 20)->nullable();
            $table->string('highlight_badge', 50)->nullable();
            $table->json('includes')->nullable();
            $table->string('image')->nullable();
            $table->string('gradient_from', 20)->nullable();
            $table->string('gradient_to', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('guest_name', 100);
            $table->string('guest_location', 100)->nullable();
            $table->tinyInteger('rating')->default(5);
            $table->text('review_text');
            $table->string('avatar')->nullable();
            $table->date('stay_date')->nullable();
            $table->string('source', 50)->default('Google');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->string('subtitle', 255)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('read_time_min')->default(5);
            $table->string('author', 100)->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('title', 150);
            $table->string('caption', 255)->nullable();
            $table->string('category', 50)->default('hotel');
            $table->string('image');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('nearby_attractions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('distance', 50)->nullable();
            $table->string('travel_time', 50)->nullable();
            $table->string('image')->nullable();
            $table->string('category', 50)->default('temple');
            $table->json('highlights')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nearby_attractions');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('festival_offers');
        Schema::dropIfExists('tour_packages');
    }
};
