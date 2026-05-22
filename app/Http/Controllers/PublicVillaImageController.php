<?php

namespace App\Http\Controllers;

use App\Models\VillaImage;
use Illuminate\Support\Facades\Storage;

class PublicVillaImageController extends Controller
{
    public function show(VillaImage $villaImage)
    {
        abort_unless($villaImage->image_path && Storage::disk('public')->exists($villaImage->image_path), 404);

        return Storage::disk('public')->response($villaImage->image_path);
    }
}
