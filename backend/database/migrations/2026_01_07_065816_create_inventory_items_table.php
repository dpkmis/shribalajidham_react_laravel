<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('restrict');
            $table->string('item_code', 50)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('unit', 50)->default('pcs'); // pcs, kg, ltr, box, bottle, etc.
            
            // Stock tracking
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->decimal('max_stock', 10, 2)->default(0);
            $table->decimal('reorder_point', 10, 2)->default(0);
            
            // Pricing
            $table->integer('unit_price_cents')->nullable(); // in cents
            $table->integer('last_purchase_price_cents')->nullable();
            
            // Storage & Location
            $table->string('storage_location', 200)->nullable();
            $table->string('bin_location', 50)->nullable();
            
            // Supplier info
            $table->string('supplier_name', 200)->nullable();
            $table->string('supplier_code', 50)->nullable();
            
            // Additional tracking
            $table->date('expiry_date')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->boolean('is_perishable')->default(false);
            $table->boolean('requires_approval')->default(false);
            
            // Status
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_active')->default(true);
            
            // Metadata
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id', 'category_id']);
            $table->index('status');
            $table->index('current_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
