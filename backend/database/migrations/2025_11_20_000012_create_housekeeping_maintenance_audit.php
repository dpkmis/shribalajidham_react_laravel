<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){

        Schema::create('housekeeping_tasks', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('room_id')->nullable()->index();
            $table->string('task_type')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to_user_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->timestamp('reported_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_tasks', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('room_id')->nullable()->index();
            $table->string('severity')->default('low');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reported_by_user_id')->nullable()->index();
            $table->string('status')->default('open');
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address',45)->nullable();
            $table->timestamps();

            $table->index(['auditable_type','auditable_id']);
        });

        Schema::create('settings', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['property_id','key']);
        });

    }

    public function down(){
        Schema::dropIfExists('settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('maintenance_tasks');
        Schema::dropIfExists('housekeeping_tasks');
    }
};
?>