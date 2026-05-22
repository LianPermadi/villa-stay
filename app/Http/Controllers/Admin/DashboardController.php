<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\Booking;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'villa_id' => 'nullable|exists:villas,id',
        ]);

        $totalVillas = Villa::count();
        $totalBookings = Booking::count();
        $recentBookings = Booking::with("villa", "user")->latest()->take(5)->get();
        $villas = Villa::orderBy('name')->get(['id', 'name']);
        
        // Villa status breakdown
        $villaStats = [
            'available' => Villa::where('status', 'available')->count(),
            'unavailable' => Villa::where('status', 'unavailable')->count(),
            'maintenance' => Villa::where('status', 'maintenance')->count(),
        ];
        
        // Recent villas with status
        $recentVillas = Villa::with('images')->latest()->take(5)->get();

        $revenueQuery = Revenue::query()
            ->when($validated['month'] ?? null, function ($query, $month) {
                $query->whereMonth('revenue_date', $month);
            })
            ->when($validated['year'] ?? null, function ($query, $year) {
                $query->whereYear('revenue_date', $year);
            })
            ->when($validated['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('revenue_date', '>=', $dateFrom);
            })
            ->when($validated['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('revenue_date', '<=', $dateTo);
            })
            ->when($validated['villa_id'] ?? null, function ($query, $villaId) {
                $query->whereHas('booking', fn ($bookingQuery) => $bookingQuery->where('villa_id', $villaId));
            });

        $totalRevenue = (clone $revenueQuery)->sum('amount');
        $filteredTransactions = (clone $revenueQuery)->count();
        $averageRevenue = $filteredTransactions > 0 ? $totalRevenue / $filteredTransactions : 0;

        // Monthly revenue data for chart, generated from the filtered dataset.
        $monthlyRevenue = (clone $revenueQuery)
            ->selectRaw("SUM(amount) as total, period")
            ->groupBy("period")
            ->orderBy("period")
            ->get();

        $movingAverageData = $this->buildMovingAverageData($monthlyRevenue);
        $nextPrediction = $movingAverageData['next_prediction'];
        $availableYears = Revenue::orderByDesc('revenue_date')
            ->get(['revenue_date'])
            ->pluck('revenue_date')
            ->map(fn ($date) => $date->format('Y'))
            ->unique()
            ->filter()
            ->values();
        $filters = [
            'month' => $validated['month'] ?? '',
            'year' => $validated['year'] ?? '',
            'date_from' => $validated['date_from'] ?? '',
            'date_to' => $validated['date_to'] ?? '',
            'villa_id' => $validated['villa_id'] ?? '',
        ];

        return view("admin.dashboard", compact(
            "totalVillas",
            "totalBookings",
            "totalRevenue",
            "filteredTransactions",
            "averageRevenue",
            "recentBookings",
            "villaStats",
            "recentVillas",
            "villas",
            "availableYears",
            "filters",
            "monthlyRevenue",
            "movingAverageData",
            "nextPrediction"
        ));
    }

    private function buildMovingAverageData($monthlyRevenue, int $window = 3): array
    {
        $history = [];
        $labels = [];
        $actual = [];
        $predicted = [];

        foreach ($monthlyRevenue as $row) {
            $amount = (float) $row->total;
            $labels[] = $row->period;
            $actual[] = $amount;
            $predicted[] = count($history) > 0
                ? round(array_sum(array_slice($history, -$window)) / min(count($history), $window), 2)
                : null;
            $history[] = $amount;
        }

        $nextPeriod = null;
        $nextValue = null;

        if (count($history) > 0) {
            $nextValue = round(array_sum(array_slice($history, -$window)) / min(count($history), $window), 2);
            $lastPeriod = end($labels);

            try {
                $nextPeriod = Carbon::createFromFormat('Y-m', $lastPeriod)->addMonth()->format('Y-m');
            } catch (\Throwable $e) {
                $nextPeriod = 'Periode berikutnya';
            }
        }

        return [
            'labels' => $labels,
            'actual' => $actual,
            'predicted' => $predicted,
            'window' => $window,
            'next_prediction' => [
                'period' => $nextPeriod,
                'value' => $nextValue,
            ],
        ];
    }
}
