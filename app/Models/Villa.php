<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_night',
        'capacity',
        'bedrooms',
        'bathrooms',
        'area',
        'status',
        'is_featured',
        'amenities',
        'down_payment_percentage',
        'payment_due_days',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'area' => 'decimal:2',
        'down_payment_percentage' => 'decimal:2',
        'payment_due_days' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(VillaImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(VillaImage::class)->where('is_primary', true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price_per_night, 0, ',', '.');
    }

    public function calculateDownPaymentAmount($numNights)
    {
        $total = $this->price_per_night * $numNights;
        return round(($total * $this->down_payment_percentage) / 100, 2);
    }

    public function getPaymentDueDateAttribute()
    {
        if ($this->payment_due_days == 0) {
            return 'Hari H';
        } elseif ($this->payment_due_days == 1) {
            return 'H-1';
        } else {
            return 'H-' . $this->payment_due_days;
        }
    }

    public function isAvailable($checkIn, $checkOut)
    {
        $conflictingBookings = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })
            ->exists();

        return !$conflictingBookings && $this->status === 'available';
    }
}
