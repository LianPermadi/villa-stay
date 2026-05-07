<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
