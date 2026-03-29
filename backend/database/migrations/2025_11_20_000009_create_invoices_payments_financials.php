<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Invoices - Main invoice records
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('invoice_number', 50)->unique();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->unsignedBigInteger('guest_id')->nullable()->index();
            $table->enum('status', ['draft', 'pending', 'paid', 'partially_paid', 'overdue', 'cancelled', 'refunded'])->default('draft')->index();
            $table->enum('type', ['booking', 'folio', 'proforma', 'credit_note', 'debit_note'])->default('booking');
            $table->date('issue_date')->index();
            $table->date('due_date')->nullable()->index();
            $table->date('paid_date')->nullable();
            
            // Financial fields in cents (to avoid decimal precision issues)
            $table->bigInteger('subtotal_cents')->default(0);
            $table->bigInteger('discount_cents')->default(0);
            $table->bigInteger('tax_cents')->default(0);
            $table->bigInteger('total_cents')->default(0);
            $table->bigInteger('paid_cents')->default(0);
            $table->bigInteger('balance_cents')->default(0);
            
            $table->char('currency', 3)->default('INR');
            $table->decimal('tax_rate', 5, 2)->default(0); // e.g., 18.00 for 18%
            $table->decimal('discount_percentage', 5, 2)->default(0);
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('payment_terms', 50)->nullable(); // e.g., 'net_30', 'due_on_receipt'
            
            // Address info (for invoice printing)
            $table->text('billing_address')->nullable();
            $table->string('billing_gstin', 20)->nullable(); // GST number for India
            
            // Audit fields
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('cancelled_by_user_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->json('meta')->nullable(); // For additional flexible data
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id', 'status', 'issue_date']);
            $table->index(['guest_id', 'status']);
        });

        // Invoice Line Items - Individual charges on invoice
        Schema::create('invoice_line_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->index();
            $table->string('item_type', 50); // 'room', 'food', 'service', 'tax', 'discount', 'other'
            $table->unsignedBigInteger('reference_id')->nullable(); // Links to booking_room_id, charge_id, etc.
            $table->string('reference_type')->nullable(); // Polymorphic reference
            
            $table->string('description', 500);
            $table->integer('quantity')->default(1);
            $table->bigInteger('unit_price_cents')->default(0);
            $table->bigInteger('subtotal_cents')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->bigInteger('tax_cents')->default(0);
            $table->bigInteger('total_cents')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->bigInteger('discount_cents')->default(0);
            
            $table->date('service_date')->nullable(); // For date-specific charges
            $table->integer('sort_order')->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            // Index
            $table->index(['reference_type', 'reference_id']);
        });

        // Payments - All payment transactions
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('payment_reference', 50)->unique(); // e.g., PAY-2025-0001
            
            // Links
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->unsignedBigInteger('guest_id')->nullable()->index();
            
            // Payment details
            $table->bigInteger('amount_cents')->default(0);
            $table->char('currency', 3)->default('INR');
            $table->enum('type', ['payment', 'refund', 'advance', 'security_deposit'])->default('payment');
            $table->enum('method', ['cash', 'card', 'upi', 'net_banking', 'cheque', 'wallet', 'bank_transfer', 'other'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('completed')->index();
            
            // Transaction details
            $table->string('transaction_id', 100)->nullable()->index(); // External reference
            $table->string('gateway', 50)->nullable(); // razorpay, stripe, etc.
            $table->json('gateway_response')->nullable();
            
            // Cheque/Bank specific
            $table->string('cheque_number', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            
            // Card specific
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_brand', 20)->nullable(); // Visa, Mastercard, etc.
            
            // Timestamps
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('cleared_at')->nullable(); // For cheques
            $table->timestamp('refunded_at')->nullable();
            
            // Additional info
            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Audit
            $table->unsignedBigInteger('received_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('processed_by_user_id')->nullable();
            $table->unsignedBigInteger('refunded_by_user_id')->nullable();
            
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['property_id', 'status', 'paid_at']);
            $table->index(['guest_id', 'paid_at']);
            $table->index(['type', 'status']);
        });

        // Payment Allocations - Track which payment goes to which invoice
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id')->index();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->bigInteger('allocated_amount_cents')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            // Unique constraint - one allocation per payment-invoice pair
            $table->unique(['payment_id', 'invoice_id']);
        });

        // Accounts - Chart of accounts for double-entry bookkeeping
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('parent_account_id')->nullable()->index();
            
            $table->string('code', 20)->index(); // e.g., 1001, 2001
            $table->string('name', 200);
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->index();
            $table->enum('sub_type', [
                'current_asset', 'fixed_asset', 
                'current_liability', 'long_term_liability',
                'owner_equity', 'retained_earnings',
                'operating_revenue', 'other_revenue',
                'operating_expense', 'other_expense'
            ])->nullable();
            
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false); // System accounts cannot be deleted
            $table->integer('display_order')->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique code per property
            $table->unique(['property_id', 'code']);
            
            // Foreign key
            $table->foreign('parent_account_id')->references('id')->on('accounts')->onDelete('restrict');
        });

        // Financial Transactions - Journal entries
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('transaction_number', 50)->index(); // e.g., JE-2025-0001
            
            // Polymorphic reference to source (invoice, payment, booking, etc.)
            $table->string('reference_type', 100)->nullable()->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            
            $table->enum('type', ['journal', 'payment', 'receipt', 'adjustment', 'opening_balance', 'closing'])->default('journal');
            $table->date('transaction_date')->index();
            $table->string('description', 500);
            $table->bigInteger('total_debit_cents')->default(0);
            $table->bigInteger('total_credit_cents')->default(0);
            
            $table->enum('status', ['draft', 'posted', 'voided'])->default('posted')->index();
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('voided_by_user_id')->nullable();
            
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['property_id', 'transaction_date', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });

        // Financial Entries - Individual debit/credit entries (double-entry)
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('financial_transaction_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            
            $table->bigInteger('debit_cents')->default(0);
            $table->bigInteger('credit_cents')->default(0);
            $table->string('narration', 500)->nullable();
            $table->integer('entry_order')->default(0); // Order of entries in transaction
            
            $table->json('meta')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('financial_transaction_id')->references('id')->on('financial_transactions')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict');
            
            // Check constraint (only debit OR credit, not both)
            // Note: MySQL doesn't support CHECK constraints until 8.0.16
            // You may need to enforce this in application logic
            
            // Index
            $table->index(['account_id', 'financial_transaction_id']);
        });

        // Tax Configurations
        Schema::create('tax_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            
            $table->string('name', 100); // e.g., "GST", "VAT", "Service Tax"
            $table->string('code', 20); // e.g., "CGST", "SGST", "IGST"
            $table->decimal('rate', 5, 2); // e.g., 9.00 for 9%
            $table->enum('calculation_type', ['percentage', 'fixed'])->default('percentage');
            $table->bigInteger('fixed_amount_cents')->nullable();
            
            $table->boolean('is_compound')->default(false); // Tax on tax
            $table->boolean('is_inclusive')->default(false); // Included in price
            $table->boolean('is_active')->default(true)->index();
            
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            
            $table->unsignedBigInteger('account_id')->nullable(); // Link to tax account
            $table->integer('display_order')->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
        });

        // Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('refund_reference', 50)->unique();
            
            $table->unsignedBigInteger('payment_id')->index(); // Original payment
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->unsignedBigInteger('guest_id')->nullable()->index();
            
            $table->bigInteger('amount_cents')->default(0);
            $table->char('currency', 3)->default('INR');
            $table->enum('method', ['cash', 'card', 'upi', 'bank_transfer', 'original_method', 'other'])->default('original_method');
            $table->enum('reason', ['cancellation', 'overpayment', 'goodwill', 'dispute', 'other'])->default('cancellation');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending')->index();
            
            $table->string('transaction_id', 100)->nullable();
            $table->json('gateway_response')->nullable();
            
            $table->text('reason_description')->nullable();
            $table->text('internal_notes')->nullable();
            
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->unsignedBigInteger('initiated_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('processed_by_user_id')->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict');
            
            // Indexes
            $table->index(['property_id', 'status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('tax_configurations');
        Schema::dropIfExists('financial_entries');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_line_items');
        Schema::dropIfExists('invoices');
    }
};