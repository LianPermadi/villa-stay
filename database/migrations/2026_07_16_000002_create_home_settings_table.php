<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->json('content')->nullable();
            $table->string('hero_background_path')->nullable();
            $table->string('cta_background_path')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->default(-8.3597510);
            $table->decimal('longitude', 10, 7)->default(127.0610910);
            $table->unsignedTinyInteger('map_zoom')->default(17);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
