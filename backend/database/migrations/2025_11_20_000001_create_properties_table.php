<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::create('properties', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('code')->nullable()->index();
            $table->string('name');
            $table->string('time_zone')->default('IST');
            $table->string('currency',3)->default('INR');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(){
        Schema::dropIfExists('properties');
    }
};
?>