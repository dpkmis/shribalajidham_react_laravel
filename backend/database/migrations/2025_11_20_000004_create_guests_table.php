<?php
// database/migrations/2025_11_20_000004_create_guests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('guests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('ulid', 26)->nullable()->unique();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            
            // Personal Information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Dr', 'Prof'])->default('Mr');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality', 100)->nullable();
            
            // Contact Information
            $table->string('email')->nullable()->index();
            $table->string('phone', 20)->nullable()->index();
            $table->string('alternate_phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            
            // Address
            $table->text('address_line1')->nullable();
            $table->text('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            
            // Business Information
            $table->string('company_name', 200)->nullable();
            $table->string('company_designation', 100)->nullable();
            $table->string('gstin', 50)->nullable()->index(); // Indian GST Number
            
            // Identity Documents
            $table->string('id_type', 50)->nullable()->comment('Passport, Aadhar, DL, etc.');
            $table->string('id_number', 100)->nullable()->index();
            $table->date('id_expiry_date')->nullable();
            $table->string('id_document_path')->nullable();
            
            // Guest Preferences
            $table->enum('preferred_language', ['en', 'hi', 'es', 'fr', 'de'])->default('en');
            $table->enum('meal_preference', ['veg', 'non-veg', 'vegan', 'jain'])->nullable();
            $table->text('special_requests')->nullable();
            $table->text('allergies')->nullable();
            
            // Guest Status
            $table->enum('guest_type', ['individual', 'corporate', 'group', 'vip'])->default('individual');
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->decimal('loyalty_points', 10, 2)->default(0);
            
            // Marketing
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('sms_consent')->default(true);
            $table->boolean('email_consent')->default(true);
            
            // System Fields
            $table->string('photo_path')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id', 'guest_type']);
            $table->index(['property_id', 'is_blacklisted']);
            $table->index(['email', 'phone']);
            $table->fullText(['first_name', 'last_name', 'email', 'phone']);
        });
    }

    public function down() {
        Schema::dropIfExists('guests');
    }
};
?>