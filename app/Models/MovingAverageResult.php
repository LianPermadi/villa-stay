<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovingAverageResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'actual_revenue',
        'predicted_revenue',
        'months_used',
        'calculation_data',
    ];

    protected $casts = [
        'actual_revenue' => 'decimal:2',
        'predicted_revenue' => 'decimal:2',
        'calculation_data' => 'array',
    ];
}
