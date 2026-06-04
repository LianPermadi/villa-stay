<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use Illuminate\Http\Request;

class VillaController extends Controller
{
    public function index(Request $request)
    {
        $query = Villa::where("status", "available")
            ->with('images')
            ->withCount(['bookings as active_booking_today_count' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today());
            }]);
        
        if ($request->has("search")) {
            $query->where("name", "like", "%" . $request->search . "%");
        }
        
        if ($request->has("capacity")) {
            $query->where("capacity", ">=", $request->capacity);
        }
        
        if ($request->has("min_price")) {
            $query->where("price_per_night", ">=", $request->min_price);
        }
        
        if ($request->has("max_price")) {
            $query->where("price_per_night", "<=", $request->max_price);
        }
        
        $villas = $query->paginate(9);
        
        return view("frontend.villas.index", compact("villas"));
    }
    
    public function show($id)
    {
        $villa = Villa::with("images")
            ->withCount(['bookings as active_booking_today_count' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today());
            }])
            ->findOrFail($id);
        
        $availableDates = [];
        $bookedDates = $villa->bookings()
            ->where("status", "!=", "cancelled")
            ->get()
            ->pluck("check_in")
            ->merge($villa->bookings()->where("status", "!=", "cancelled")->get()->pluck("check_out"));
        
        return view("frontend.villas.show", compact("villa", "bookedDates"));
    }
}
