<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HomeSetting;
use App\Models\Villa;
use Illuminate\Support\Collection;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        $settings = HomeSetting::current();
        try {
            $featuredVillas = Villa::with('images')
                ->withCount(['bookings as active_booking_today_count' => function ($query) {
                    $query->where('status', '!=', 'cancelled')
                        ->whereDate('check_in', '<=', today())
                        ->whereDate('check_out', '>', today());
                }])
                ->where('is_featured', true)
                ->where('status', 'available')
                ->take(4)
                ->get();
        } catch (Throwable) {
            $featuredVillas = new Collection;
        }

        $latitude = (float) $settings->latitude;
        $longitude = (float) $settings->longitude;
        $longitudeSpan = 360 / (2 ** $settings->map_zoom);
        $latitudeSpan = $longitudeSpan / 2;
        $mapEmbedUrl = 'https://www.openstreetmap.org/export/embed.html?'.http_build_query([
            'bbox' => implode(',', [
                $longitude - $longitudeSpan,
                $latitude - $latitudeSpan,
                $longitude + $longitudeSpan,
                $latitude + $latitudeSpan,
            ]),
            'layer' => 'mapnik',
            'marker' => $latitude.','.$longitude,
        ]);
        $mapDirectionsUrl = 'https://www.openstreetmap.org/?'.http_build_query([
            'mlat' => $latitude,
            'mlon' => $longitude,
            'zoom' => $settings->map_zoom,
        ]).'#map='.$settings->map_zoom.'/'.$latitude.'/'.$longitude;

        return view('frontend.home', compact('featuredVillas', 'settings', 'mapEmbedUrl', 'mapDirectionsUrl'));
    }
}
