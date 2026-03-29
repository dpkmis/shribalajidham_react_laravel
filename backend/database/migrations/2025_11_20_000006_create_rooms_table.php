<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2025_11_20_000006_create_rooms_table.php
return new class extends Migration {
    public function up() {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('room_number', 50);
            $table->unsignedBigInteger('room_type_id')->index();
            $table->smallInteger('floor')->nullable()->index();
            $table->string('block', 50)->nullable()->comment('Building/Block name');
            $table->string('wing', 50)->nullable()->comment('Wing/Section');
            
            // Status Management
            $table->enum('status', [
                'available',
                'occupied',
                'reserved',
                'maintenance',
                'out-of-order',
                'blocked'
            ])->default('available')->index();
            
            $table->enum('housekeeping_status', [
                'clean',
                'dirty',
                'inspected',
                'out-of-service',
                'pickup'
            ])->default('clean')->index();
            
            // Pricing
            $table->bigInteger('price_override_cents')->nullable()->comment('Override default rate');
            
            // Features
            $table->boolean('is_smoking')->default(false);
            $table->boolean('is_accessible')->default(false)->comment('ADA/Accessible room');
            $table->boolean('is_connecting')->default(false)->comment('Has connecting door');
            $table->unsignedBigInteger('connecting_room_id')->nullable()->comment('Connected room ID');
            
            // Operational
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('last_maintenance_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['property_id', 'room_number'], 'ux_property_room_number');
            $table->index(['property_id', 'status']);
            $table->index(['property_id', 'room_type_id', 'status']);
            $table->index(['property_id', 'floor', 'status']);
            
            // Foreign Keys
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('restrict');
            $table->foreign('connecting_room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    public function down() {
        Schema::dropIfExists('rooms');
    }
};
?>