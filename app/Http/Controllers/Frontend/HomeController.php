<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
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
        
        return view("frontend.home", compact("featuredVillas"));
    }
}
