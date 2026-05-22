<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getUrlAttribute()
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return route('villa-images.show', $this);
        }

        return asset('images/villa-placeholder.svg');
    }
}
