<?php
// database/migrations/2025_11_20_000005_create_room_types_and_features.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Room Types Table
        Schema::create('room_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name', 100);
            $table->string('code', 50)->nullable()->index();
            $table->text('description')->nullable();
            $table->bigInteger('default_rate_cents')->default(0)->comment('Base rate in cents');
            $table->smallInteger('max_occupancy')->default(2);
            $table->smallInteger('max_adults')->default(2);
            $table->smallInteger('max_children')->default(0);
            $table->smallInteger('beds')->default(1);
            $table->decimal('room_size_sqm', 8, 2)->nullable()->comment('Room size in square meters');
            $table->enum('bed_type', ['single', 'double', 'queen', 'king', 'twin'])->default('double');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('amenities')->nullable()->comment('Room-specific amenities');
            $table->json('images')->nullable()->comment('Room type images');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['property_id', 'is_active']);
            $table->unique(['property_id', 'code'], 'ux_property_room_type_code');
        });

        // Room Features Table
        Schema::create('room_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->string('icon', 50)->nullable()->comment('Icon class/name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['property_id', 'code'], 'ux_property_feature_code');
        });

        // Pivot Table: Room Type <-> Features
        Schema::create('room_type_feature', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('room_type_id')->index();
            $table->unsignedBigInteger('room_feature_id')->index();
            $table->timestamps();

            $table->unique(['room_type_id', 'room_feature_id'], 'ux_room_type_feature');
            
            // Foreign Keys
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
            $table->foreign('room_feature_id')->references('id')->on('room_features')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('room_type_feature');
        Schema::dropIfExists('room_features');
        Schema::dropIfExists('room_types');
    }
};