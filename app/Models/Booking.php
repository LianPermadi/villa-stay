<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function revenue()
    {
        return $this->hasOne(Revenue::class);
    }
}
