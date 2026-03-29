<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){

        Schema::create('media', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('disk')->default('local');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size_bytes')->default(0);
            $table->string('mediable_type')->nullable();
            $table->unsignedBigInteger('mediable_id')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamps();

            $table->index(['mediable_type','mediable_id']);
        });

        Schema::create('pages', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('status')->default('draft');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name');
            $table->string('location')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id')->index();
            $table->string('title');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->integer('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

    }

    public function down(){
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('media');
    }
};
?>