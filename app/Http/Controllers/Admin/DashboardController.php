<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\Booking;
use App\Models\Revenue;
use App\Models\MovingAverageResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVillas = Villa::count();
        $totalBookings = Booking::count();
        $totalRevenue = Revenue::sum("amount");
        $recentBookings = Booking::with("villa", "user")->latest()->take(5)->get();
        
        // Monthly revenue data for chart
        $monthlyRevenue = Revenue::selectRaw("SUM(amount) as total, period")
            ->groupBy("period")
            ->orderBy("period")
            ->get();
        
        // Moving average predictions
        $predictions = MovingAverageResult::latest()->take(6)->get();
        
        return view("admin.dashboard", compact(
            "totalVillas",
            "totalBookings",
            "totalRevenue",
            "recentBookings",
            "monthlyRevenue",
            "predictions"
        ));
    }
}
