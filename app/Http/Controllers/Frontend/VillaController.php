<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VillaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'currency' => ['nullable', Rule::in(Currency::codes())],
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $query = Villa::where('status', 'available')
            ->with('images')
            ->withCount(['bookings as active_booking_today_count' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today());
            }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        $query->when($request->filled('currency'), fn ($query) => $query->where('currency', $request->currency))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price_per_night', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price_per_night', '<=', $request->max_price));

        $villas = $query->paginate(9);

        return view('frontend.villas.index', compact('villas'));
    }

    public function show($id)
    {
        $villa = Villa::with('images')
            ->withCount(['bookings as active_booking_today_count' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today());
            }])
            ->findOrFail($id);

        $availableDates = [];
        $bookedDates = $villa->bookings()
            ->where('status', '!=', 'cancelled')
            ->get()
            ->pluck('check_in')
            ->merge($villa->bookings()->where('status', '!=', 'cancelled')->get()->pluck('check_out'));

        return view('frontend.villas.show', compact('villa', 'bookedDates'));
    }
}
