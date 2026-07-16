<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'proof_image',
        'status',
        'payment_type',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofImageExistsAttribute()
    {
        return $this->proof_image && Storage::disk('public')->exists($this->proof_image);
    }

    public function getProofImageUrlAttribute()
    {
        return $this->proof_image_exists ? route('payment-proofs.show', $this) : null;
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->booking?->formatMoney(abs((float) $this->amount)) ?? (string) $this->amount;
    }
}
