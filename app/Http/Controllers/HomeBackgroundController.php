<?php

namespace App\Http\Controllers;

use App\Models\HomeSetting;
use Illuminate\Support\Facades\Storage;

class HomeBackgroundController extends Controller
{
    public function __invoke(string $type)
    {
        abort_unless(in_array($type, ['hero', 'cta'], true), 404);
        $path = HomeSetting::current()->{$type.'_background_path'};
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
