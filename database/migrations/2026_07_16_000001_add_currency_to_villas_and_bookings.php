<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->char('currency', 3)->default('IDR')->after('price_per_night');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->char('currency', 3)->default('IDR')->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
