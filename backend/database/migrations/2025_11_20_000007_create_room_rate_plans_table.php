<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('room_rate_plans', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('room_type_id')->index();

            $table->string('name', 100);
            $table->string('code', 50)->index();
            $table->bigInteger('rate_cents');

            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            $table->json('day_of_week')->nullable()->comment('Available days: [1,2,3...]');

            $table->boolean('is_active')->default(true);
            $table->integer('min_stay')->default(1);
            $table->integer('max_stay')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            // 👇 Explicit short index name
            $table->index(
                ['property_id', 'room_type_id', 'valid_from', 'valid_to'],
                'idx_rrp_prop_room_valid'
            );

            $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->onDelete('cascade');

            $table->foreign('room_type_id')
                ->references('id')
                ->on('room_types')
                ->onDelete('cascade');
        });

    }

    public function down() {
        Schema::dropIfExists('room_rate_plans');
    }
};

?>