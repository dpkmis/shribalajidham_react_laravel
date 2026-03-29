<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('group', 50)->index();      // general, contact, seo, social, policies, hero
            $table->string('key', 100);
            $table->string('label', 150);               // Human-readable label for CMS
            $table->text('value')->nullable();
            $table->string('type', 20)->default('text'); // text, textarea, email, tel, url, image, color, number, time
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['property_id', 'group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_metadata');
    }
};
