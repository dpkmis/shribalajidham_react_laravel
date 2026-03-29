<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_inventory_blocking')->default(true);
            $table->timestamps();
        });

        Schema::create('room_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('room_type_id')->index();
            $table->unsignedBigInteger('rate_plan_id')->index();
            $table->date('rate_date')->index();
            $table->bigInteger('price_cents')->default(0);
            $table->smallInteger('min_stay')->default(1);
            $table->boolean('stop_sell')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['room_type_id','rate_plan_id','rate_date'],'ux_roomtype_rate_date_plan');
        });
    }

    public function down() {
        Schema::dropIfExists('room_rates');
        Schema::dropIfExists('rate_plans');
    }
};
?>