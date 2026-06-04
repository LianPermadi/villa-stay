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

    public function getPrimaryImageUrlAttribute()
    {
        $images = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->orderByDesc('is_primary')->orderBy('sort_order')->get();

        $image = $images->firstWhere('is_primary', true) ?? $images->sortBy('sort_order')->first();

        return $image?->url ?? asset('images/villa-placeholder.svg');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getIsOccupiedTodayAttribute()
    {
        if (array_key_exists('active_booking_today_count', $this->attributes)) {
            return (int) $this->attributes['active_booking_today_count'] > 0;
        }

        return $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>', today())
            ->exists();
    }

    public function getOccupancyLabelAttribute()
    {
        return $this->is_occupied_today ? 'Terisi hari ini' : 'Kosong hari ini';
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
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn)
            ->exists();

        return !$conflictingBookings && $this->status === 'available';
    }
}
