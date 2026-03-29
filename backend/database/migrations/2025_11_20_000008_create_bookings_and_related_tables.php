<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        
        // Main Bookings Table
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('ulid', 26)->nullable()->unique();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('guest_id')->nullable()->index()->comment('Primary guest');
            
            // Booking Reference
            $table->string('booking_reference', 100)->unique()->index();
            $table->string('confirmation_number', 50)->nullable()->unique();
            
            // Status & Source
            $table->enum('status', [
                'pending',
                'confirmed', 
                'checked-in',
                'checked-out',
                'cancelled',
                'no-show'
            ])->default('confirmed')->index();
            
            $table->enum('source', [
                'walk-in',
                'phone',
                'email',
                'website',
                'booking.com',
                'airbnb',
                'agoda',
                'makemytrip',
                'goibibo',
                'corporate',
                'travel-agent'
            ])->nullable()->index();
            
            // Dates
            $table->date('checkin_date')->index();
            $table->date('checkout_date')->index();
            $table->timestamp('actual_checkin_at')->nullable();
            $table->timestamp('actual_checkout_at')->nullable();
            $table->integer('nights')->virtualAs('DATEDIFF(checkout_date, checkin_date)');
            
            // Guest Details
            $table->smallInteger('number_of_adults')->default(1);
            $table->smallInteger('number_of_children')->default(0);
            $table->smallInteger('number_of_infants')->default(0);
            $table->smallInteger('total_guests')->virtualAs('number_of_adults + number_of_children');
            
            // Financial
            $table->char('currency', 3)->default('INR');
            $table->bigInteger('room_charges_cents')->default(0);
            $table->bigInteger('tax_amount_cents')->default(0);
            $table->bigInteger('discount_amount_cents')->default(0);
            $table->bigInteger('additional_charges_cents')->default(0);
            $table->bigInteger('total_amount_cents')->default(0);
            $table->bigInteger('paid_amount_cents')->default(0);
            $table->bigInteger('balance_amount_cents')->default(0);
            
            $table->enum('payment_status', [
                'unpaid',
                'partially-paid',
                'paid',
                'refunded',
                'cancelled'
            ])->default('unpaid')->index();
            
            // Booking Details
            $table->string('special_requests', 500)->nullable();
            $table->time('arrival_time')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // References
            $table->unsignedBigInteger('company_id')->nullable()->index()->comment('Corporate booking');
            $table->unsignedBigInteger('travel_agent_id')->nullable()->index();
            $table->string('agent_commission_percent', 5)->nullable();
            
            // System Fields
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id', 'status', 'checkin_date']);
            $table->index(['property_id', 'checkout_date']);
            $table->index(['property_id', 'payment_status']);
            $table->index(['checkin_date', 'checkout_date']);
            
            // Foreign Keys
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('set null');
        });

        // Booking Rooms (Room Assignments)
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index();
            $table->unsignedBigInteger('room_id')->nullable()->index();
            $table->unsignedBigInteger('room_type_id')->index();
            $table->unsignedBigInteger('rate_plan_id')->nullable()->index();
            
            // Dates
            $table->date('checkin_date')->index();
            $table->date('checkout_date')->index();
            $table->integer('nights')->virtualAs('DATEDIFF(checkout_date, checkin_date)');
            
            // Pricing
            $table->bigInteger('rate_per_night_cents')->default(0);
            $table->bigInteger('total_rate_cents')->default(0);
            $table->bigInteger('discount_cents')->default(0);
            $table->bigInteger('final_rate_cents')->default(0);
            
            // Status
            $table->enum('status', [
                'reserved',
                'confirmed',
                'checked-in',
                'checked-out',
                'cancelled',
                'no-show'
            ])->default('reserved')->index();
            
            // Guest Assignment
            $table->smallInteger('adults')->default(1);
            $table->smallInteger('children')->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index(['room_id', 'checkin_date', 'checkout_date']);
            
            // Foreign Keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('restrict');
        });

        // Additional Guests (Beyond primary guest)
        Schema::create('booking_guests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index();
            $table->unsignedBigInteger('booking_room_id')->nullable()->index();
            $table->unsignedBigInteger('guest_id')->nullable()->index();
            
            // Guest can be linked or manual entry
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('dob')->nullable();
            $table->enum('guest_type', ['adult', 'child', 'infant'])->default('adult');
            
            // ID Proof
            $table->string('id_type', 50)->nullable();
            $table->string('id_number', 100)->nullable();
            
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('set null');
        });

        // Booking Charges (Additional services, taxes, etc.)
        Schema::create('booking_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index();
            $table->unsignedBigInteger('booking_room_id')->nullable()->index();
            
            $table->enum('type', [
                'room-charge',
                'tax',
                'service-charge',
                'food-beverage',
                'laundry',
                'minibar',
                'spa',
                'transportation',
                'extra-bed',
                'early-checkin',
                'late-checkout',
                'pet-charge',
                'parking',
                'damage',
                'other'
            ])->index();
            
            $table->string('description');
            $table->bigInteger('amount_cents')->default(0);
            $table->smallInteger('quantity')->default(1);
            $table->bigInteger('total_cents')->virtualAs('amount_cents * quantity');
            
            $table->unsignedBigInteger('tax_rate_id')->nullable()->index();
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->bigInteger('tax_amount_cents')->default(0);
            
            $table->boolean('is_refundable')->default(true);
            $table->boolean('is_paid')->default(false);
            $table->date('charge_date')->nullable();
            
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });

        // Booking Payments
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index();
            $table->string('payment_reference', 100)->unique();
            
            $table->bigInteger('amount_cents')->default(0);
            
            $table->enum('method', [
                'cash',
                'card',
                'upi',
                'net-banking',
                'cheque',
                'wallet',
                'bank-transfer',
                'other'
            ])->nullable()->index();
            
            $table->enum('type', ['payment', 'refund'])->default('payment')->index();
            
            // Payment Details
            $table->string('card_last4', 4)->nullable();
            $table->string('transaction_id', 100)->nullable()->index();
            $table->string('cheque_number', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            
            // Gateway Integration
            $table->string('gateway', 50)->nullable();
            $table->json('gateway_response')->nullable();
            
            // Status
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'cancelled',
                'refunded'
            ])->default('completed')->index();
            
            $table->timestamp('paid_at')->nullable()->index();
            $table->text('remarks')->nullable();
            
            $table->unsignedBigInteger('received_by_user_id')->nullable()->index();
            $table->timestamps();
            
            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index(['paid_at', 'method']);
            
            // Foreign Keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });

        // Booking Activity Log
        Schema::create('booking_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id')->index();
            $table->string('event', 100)->index();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('performed_by_user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('booking_activities');
        Schema::dropIfExists('booking_payments');
        Schema::dropIfExists('booking_charges');
        Schema::dropIfExists('booking_guests');
        Schema::dropIfExists('booking_rooms');
        Schema::dropIfExists('bookings');
    }
};
?>