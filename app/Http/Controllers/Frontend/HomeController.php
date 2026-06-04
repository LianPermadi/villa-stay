<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $featuredVillas = Villa::with('images')
                ->withCount(['bookings as active_booking_today_count' => function ($query) {
                    $query->where('status', '!=', 'cancelled')
                        ->whereDate('check_in', '<=', today())
                        ->whereDate('check_out', '>', today());
                }])
                ->where("is_featured", true)
                ->where("status", "available")
                ->take(4)
                ->get();
        } catch (Throwable) {
            $featuredVillas = new Collection();
        }
        
        return view("frontend.home", compact("featuredVillas"));
    }
}
