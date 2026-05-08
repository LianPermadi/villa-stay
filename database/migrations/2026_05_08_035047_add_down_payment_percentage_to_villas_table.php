<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->decimal('down_payment_percentage', 5, 2)->default(30.00)->after('status');
            $table->integer('payment_due_days')->default(0)->after('down_payment_percentage'); // 0 = Hari H, 1 = H-1, etc.
        });
    }

    public function down(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn(['down_payment_percentage', 'payment_due_days']);
        });
    }
};
