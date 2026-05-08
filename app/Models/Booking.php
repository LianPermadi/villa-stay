<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'villa_id',
        'check_in',
        'check_out',
        'num_nights',
        'num_guests',
        'total_price',
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
        'guest_name',
        'guest_email',
        'guest_phone',
        'special_requests',
        'status',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_due_date' => 'date',
        'refund_amount' => 'decimal:2',
        'refund_date' => 'date',
        'is_overdue' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latest('created_at');
    }

    public function pendingPayment()
    {
        return $this->hasOne(Payment::class)->where('status', 'pending');
    }

    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }

    /**
     * Get the latest payment for this booking
     */
    public function payment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    /**
     * Accessor for is_overdue - computed dynamically
     * Considers the due date as inclusive (until end of day)
     */
    public function getIsOverdueAttribute($value)
    {
        // If already fully paid or cancelled/completed, not overdue
        if ($this->payment_status === 'fully_paid' || in_array($this->status, ['cancelled', 'completed'])) {
            return false;
        }

        // Check if there is a due date and if current datetime is past it (end of day)
        if ($this->payment_due_date) {
            return Carbon::now()->gt(Carbon::parse($this->payment_due_date)->endOfDay());
        }

        return false;
    }
}
