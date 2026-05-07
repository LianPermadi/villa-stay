<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moving_average_results', function (Blueprint $table) {
            $table->id();
            $table->string('period');
            $table->decimal('actual_revenue', 15, 2);
            $table->decimal('predicted_revenue', 15, 2);
            $table->integer('months_used');
            $table->text('calculation_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moving_average_results');
    }
};
