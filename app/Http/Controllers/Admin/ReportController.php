<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revenue;
use App\Models\MovingAverageResult;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $revenues = Revenue::selectRaw("SUM(amount) as total, period")
            ->groupBy("period")
            ->orderBy("period")
            ->get();
        
        $predictions = MovingAverageResult::latest()->take(12)->get();
        
        $totalRevenue = Revenue::sum("amount");
        $totalTransactions = Revenue::count();
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        $topVillas = Booking::selectRaw("villa_id, COUNT(*) as total_bookings, SUM(total_price) as total_revenue")
            ->groupBy("villa_id")
            ->orderBy("total_revenue", "desc")
            ->take(5)
            ->with("villa")
            ->get();
        
        return view("admin.reports.index", compact(
            "revenues",
            "predictions",
            "totalRevenue",
            "totalTransactions",
            "avgTransaction",
            "topVillas"
        ));
    }
}
