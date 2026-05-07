<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create($villaId)
    {
        $villa = Villa::with('images')->findOrFail($villaId);
        return view("frontend.bookings.create", compact("villa"));
    }
    
    public function store(Request $request, $villaId)
    {
        $villa = Villa::findOrFail($villaId);
        
        $request->validate([
            "check_in" => "required|date|after:today",
            "check_out" => "required|date|after:check_in",
            "num_guests" => "required|integer|min:1|max:" . $villa->capacity,
            "guest_name" => "required|string|max:255",
            "guest_email" => "required|email",
            "guest_phone" => "required|string",
        ]);
        
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $numNights = $checkIn->diffInDays($checkOut);
        
        if (!$villa->isAvailable($request->check_in, $request->check_out)) {
            return back()->withErrors(["villa" => "Villa tidak tersedia untuk tanggal tersebut"]);
        }
        
        $totalPrice = $villa->price_per_night * $numNights;
        
        $booking = Booking::create([
            "user_id" => Auth::id(),
            "villa_id" => $villa->id,
            "check_in" => $request->check_in,
            "check_out" => $request->check_out,
            "num_nights" => $numNights,
            "num_guests" => $request->num_guests,
            "total_price" => $totalPrice,
            "guest_name" => $request->guest_name,
            "guest_email" => $request->guest_email,
            "guest_phone" => $request->guest_phone,
            "special_requests" => $request->special_requests,
            "status" => "pending",
        ]);
        
        return redirect()->route("bookings.show", $booking)->with("success", "Booking berhasil dibuat!");
    }
    
    public function index()
    {
        $bookings = Booking::where("user_id", Auth::id())->latest()->get();
        return view("frontend.bookings.index", compact("bookings"));
    }
    
    public function show(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403);
        }
        return view("frontend.bookings.show", compact("booking"));
    }
}
