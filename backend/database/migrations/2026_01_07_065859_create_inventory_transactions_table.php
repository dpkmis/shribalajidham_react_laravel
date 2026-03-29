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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('property_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('transaction_type', [
                'stock_in',
                'stock_out',
                'adjustment',
                'transfer',
                'damage',
                'expired',
                'return'
            ]);

            $table->decimal('quantity', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->integer('unit_price_cents')->nullable();

            $table->string('reference_type', 50)->nullable(); // booking, maintenance, etc.
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('from_location_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->foreignId('to_location_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            $table->text('remarks')->nullable();

            $table->date('transaction_date')->useCurrent();

            $table->timestamps();

            $table->index(['inventory_item_id', 'transaction_date']);
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
