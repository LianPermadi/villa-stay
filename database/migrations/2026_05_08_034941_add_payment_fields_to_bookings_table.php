<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('down_payment_amount', 15, 2)->nullable()->after('total_price');
            $table->decimal('remaining_amount', 15, 2)->nullable()->after('down_payment_amount');
            $table->enum('payment_status', ['none', 'dp_paid', 'fully_paid', 'refunded'])->default('none')->after('remaining_amount');
            $table->date('payment_due_date')->nullable()->after('payment_status'); // H-1 or Hari H
            $table->boolean('is_overdue')->default(false)->after('payment_due_date');
            $table->enum('reject_status', ['none', 'rejected', 'partial_refund', 'full_refund'])->default('none')->after('is_overdue');
            $table->text('rejection_reason')->nullable()->after('reject_status');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('rejection_reason');
            $table->enum('refund_status', ['none', 'pending', 'completed'])->default('none')->after('refund_amount');
            $table->date('refund_date')->nullable()->after('refund_status');
            $table->string('payment_proof_image')->nullable()->after('refund_date');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'down_payment_amount',
                'remaining_amount',
                'payment_status',
                'payment_due_date',
                'is_overdue',
                'reject_status',
                'rejection_reason',
                'refund_amount',
                'refund_status',
                'refund_date',
                'payment_proof_image',
            ]);
        });
    }
};
