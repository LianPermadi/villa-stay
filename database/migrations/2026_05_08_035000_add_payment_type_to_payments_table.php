<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_type', ['down_payment', 'final_payment', 'refund'])->default('down_payment')->after('status');
            $table->text('notes')->nullable()->after('payment_type');
            $table->string('admin_notes')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'notes', 'admin_notes']);
        });
    }
};
