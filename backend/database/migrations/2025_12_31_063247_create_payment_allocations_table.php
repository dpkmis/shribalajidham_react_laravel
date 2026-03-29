<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->bigInteger('allocated_amount_cents')->default(0);
            $table->timestamp('allocation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('notes')->nullable();
            $table->foreignId('allocated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('payment_id');
            $table->index('invoice_id');
            $table->index('allocation_date');
            
            // Unique constraint to prevent duplicate allocations
            $table->unique(['payment_id', 'invoice_id'], 'unique_payment_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};