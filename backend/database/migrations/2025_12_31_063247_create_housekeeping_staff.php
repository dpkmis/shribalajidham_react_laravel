<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('housekeeping_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            $table->string('staff_code', 50)->unique();
            $table->string('full_name', 200);
            $table->string('email', 200)->nullable();
            $table->string('phone', 20)->nullable();

            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'temporary'])->default('full-time');
            $table->enum('shift', ['morning', 'afternoon', 'evening', 'night', 'rotating'])->default('morning');

            $table->date('joining_date')->nullable();
            $table->date('leaving_date')->nullable();

            $table->integer('max_rooms_per_day')->default(12);
            $table->text('specializations')->nullable(); // JSON: ['deep-cleaning', 'laundry', 'turndown']

            $table->boolean('is_supervisor')->default(false);
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'is_active']);
            $table->index('shift');
        });

        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('housekeeping_staff')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('task_type', [
                'checkout-cleaning',
                'daily-cleaning',
                'deep-cleaning',
                'turndown-service',
                'maintenance-cleaning',
                'inspection',
            ])->default('daily-cleaning');

            $table->enum('status', [
                'pending',
                'assigned',
                'in-progress',
                'completed',
                'inspected',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('inspected_at')->nullable();

            $table->integer('estimated_duration_minutes')->default(30);
            $table->integer('actual_duration_minutes')->nullable();

            // Task details
            $table->text('checklist_items')->nullable(); // JSON checklist
            $table->text('completed_items')->nullable(); // JSON of completed items
            $table->text('special_instructions')->nullable();

            // Quality tracking
            $table->integer('quality_rating')->nullable(); // 1-5
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('inspection_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Guest info
            $table->boolean('is_occupied')->default(false);
            $table->boolean('guest_present')->default(false);
            $table->boolean('do_not_disturb')->default(false);

            $table->text('staff_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['room_id', 'scheduled_date']);
            $table->index(['assigned_to', 'status']);
            $table->index(['property_id', 'scheduled_date', 'status']);
        });

        Schema::create('housekeeping_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            $table->string('name', 200);
            $table->string('code', 50)->unique();
            $table->enum('checklist_type', [
                'checkout-cleaning',
                'daily-cleaning',
                'deep-cleaning',
                'turndown-service',
                'inspection',
            ]);

            $table->text('description')->nullable();
            $table->text('items')->nullable(); // JSON array of checklist items

            $table->integer('estimated_duration_minutes')->default(30);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // $table->index(['property_id', 'checklist_type', 'is_active']);
        });

        Schema::create('housekeeping_supplies_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('housekeeping_tasks')->onDelete('cascade');
            $table->integer('inventory_item_id');
            $table->foreignId('staff_id')->constrained('housekeeping_staff')->onDelete('cascade');

            $table->decimal('quantity_used', 10, 2);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('task_id');
            // $table->index('inventory_item_id');
        });

        Schema::create('housekeeping_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('housekeeping_staff')->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'half-day', 'leave', 'sick'])->default('present');

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->integer('rooms_cleaned')->default(0);
            $table->integer('tasks_completed')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'attendance_date']);
            $table->index('attendance_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('housekeeping_attendance');
        Schema::dropIfExists('housekeeping_supplies_usage');
        Schema::dropIfExists('housekeeping_checklists');
        Schema::dropIfExists('housekeeping_tasks');
        Schema::dropIfExists('housekeeping_staff');
    }
};
