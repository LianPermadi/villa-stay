<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with("villa", "user")->latest()->paginate(10);
        return view("admin.bookings.index", compact("bookings"));
    }
    
    public function show(Booking $booking)
    {
        $booking->load("villa", "user", "payment");
        return view("admin.bookings.show", compact("booking"));
    }
    
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            "status" => "required|in:pending,confirmed,completed,cancelled",
        ]);
        
        $booking->update(["status" => $request->status]);
        
        if ($request->status === "completed") {
            $booking->payment()->update(["status" => "verified"]);
        }
        
        return redirect()->route("admin.bookings.index")->with("success", "Status booking berhasil diperbarui!");
    }
}
