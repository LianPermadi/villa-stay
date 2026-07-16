<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Revenue;
use App\Models\Villa;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'currency' => ['nullable', Rule::in(Currency::codes())],
        ]);

        $reportCurrency = $validated['currency'] ?? Currency::DEFAULT;

        $totalVillas = Villa::count();
        $totalBookings = Booking::count();
        $recentBookings = Booking::with('villa', 'user')->latest()->take(5)->get();
        $villas = Villa::orderBy('name')->get(['id', 'name']);

        // Villa status breakdown
        $villaStats = [
            'available' => Villa::where('status', 'available')->count(),
            'unavailable' => Villa::where('status', 'unavailable')->count(),
            'maintenance' => Villa::where('status', 'maintenance')->count(),
        ];

        // Recent villas with status
        $recentVillas = Villa::with('images')
            ->withCount(['bookings as active_booking_today_count' => function ($query) {
                $query->where('status', '!=', 'cancelled')
                    ->whereDate('check_in', '<=', today())
                    ->whereDate('check_out', '>', today());
            }])
            ->latest()
            ->take(5)
            ->get();

        $dateFrom = $request->has('date_from') ? $request->input('date_from') : now()->subMonths(6)->format('Y-m-d');
        $dateTo = $request->has('date_to') ? $request->input('date_to') : now()->addMonths(6)->format('Y-m-d');

        $revenueQuery = Revenue::query()
            ->when($validated['month'] ?? null, function ($query, $month) {
                $query->whereMonth('revenue_date', $month);
            })
            ->when($validated['year'] ?? null, function ($query, $year) {
                $query->whereYear('revenue_date', $year);
            })
            ->when($dateFrom, function ($query, $df) {
                $query->whereDate('revenue_date', '>=', $df);
            })
            ->when($dateTo, function ($query, $dt) {
                $query->whereDate('revenue_date', '<=', $dt);
            })
            ->when($validated['villa_id'] ?? null, function ($query, $villaId) {
                $query->whereHas('booking', fn ($bookingQuery) => $bookingQuery->where('villa_id', $villaId));
            })
            ->whereHas('booking', fn ($bookingQuery) => $bookingQuery->where('currency', $reportCurrency));

        $totalRevenue = (clone $revenueQuery)->sum('amount');
        $filteredTransactions = (clone $revenueQuery)->count();
        $averageRevenue = $filteredTransactions > 0 ? $totalRevenue / $filteredTransactions : 0;

        // Monthly revenue data for chart, generated from the filtered dataset.
        $monthlyRevenue = (clone $revenueQuery)
            ->selectRaw('SUM(amount) as total, period')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // K-Means Clustering for Villa Performance
        $villasPerformance = (clone $revenueQuery)
            ->join('bookings', 'revenues.booking_id', '=', 'bookings.id')
            ->join('villas', 'bookings.villa_id', '=', 'villas.id')
            ->selectRaw('villas.id as id, villas.name as name, SUM(revenues.amount) as revenue, COUNT(DISTINCT revenues.booking_id) as bookings')
            ->groupBy('villas.id', 'villas.name')
            ->get()
            ->toArray();

        // If no filters are applied but some villas have 0 revenue, we might want to include them,
        // but clustering only those with revenue is also fine (or we fetch all and left join).
        // Let's stick to the active ones based on revenue filter for accurate clustering.
        $kmeansResult = $this->buildKMeansClusteringData($villasPerformance, 3);

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
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'villa_id' => $validated['villa_id'] ?? '',
            'currency' => $reportCurrency,
        ];

        return view('admin.dashboard', compact(
            'totalVillas',
            'totalBookings',
            'totalRevenue',
            'filteredTransactions',
            'averageRevenue',
            'recentBookings',
            'villaStats',
            'recentVillas',
            'villas',
            'availableYears',
            'filters',
            'monthlyRevenue',
            'kmeansResult',
            'reportCurrency',
        ));
    }

    private function buildKMeansClusteringData(array $data, int $k = 3): array
    {
        if (empty($data)) {
            return ['data' => [], 'summary' => []];
        }
        if (count($data) < $k) {
            $k = count($data);
        }

        // Extract values for normalization
        $revenues = array_column($data, 'revenue');
        $bookings = array_column($data, 'bookings');

        $minRev = min($revenues) ?: 0;
        $maxRev = max($revenues) ?: 1;
        if ($minRev == $maxRev) {
            $maxRev = $minRev + 1;
        }

        $minBkg = min($bookings) ?: 0;
        $maxBkg = max($bookings) ?: 1;
        if ($minBkg == $maxBkg) {
            $maxBkg = $minBkg + 1;
        }

        // Normalize
        foreach ($data as &$item) {
            $item['norm_revenue'] = ($item['revenue'] - $minRev) / ($maxRev - $minRev);
            $item['norm_bookings'] = ($item['bookings'] - $minBkg) / ($maxBkg - $minBkg);
        }
        unset($item);

        // Initialize centroids randomly
        $centroids = [];
        $usedIndices = [];
        while (count($centroids) < $k) {
            $idx = rand(0, count($data) - 1);
            if (! in_array($idx, $usedIndices)) {
                $usedIndices[] = $idx;
                $centroids[] = [
                    'revenue' => $data[$idx]['norm_revenue'],
                    'bookings' => $data[$idx]['norm_bookings'],
                ];
            }
        }

        $clusters = [];
        $iterations = 0;
        $maxIterations = 100;
        $hasChanged = true;

        while ($hasChanged && $iterations < $maxIterations) {
            $hasChanged = false;
            $newClusters = array_fill(0, $k, []);

            // Assign points to nearest centroid
            foreach ($data as $idx => $item) {
                $minDist = PHP_FLOAT_MAX;
                $closestCentroid = 0;

                foreach ($centroids as $cIdx => $centroid) {
                    $dist = pow($item['norm_revenue'] - $centroid['revenue'], 2) +
                            pow($item['norm_bookings'] - $centroid['bookings'], 2);
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $closestCentroid = $cIdx;
                    }
                }
                $newClusters[$closestCentroid][] = $idx;
            }

            // Update centroids
            foreach ($newClusters as $cIdx => $clusterIndices) {
                if (count($clusterIndices) > 0) {
                    $sumRev = 0;
                    $sumBkg = 0;
                    foreach ($clusterIndices as $idx) {
                        $sumRev += $data[$idx]['norm_revenue'];
                        $sumBkg += $data[$idx]['norm_bookings'];
                    }
                    $newRev = $sumRev / count($clusterIndices);
                    $newBkg = $sumBkg / count($clusterIndices);

                    if (abs($centroids[$cIdx]['revenue'] - $newRev) > 0.0001 ||
                        abs($centroids[$cIdx]['bookings'] - $newBkg) > 0.0001) {
                        $hasChanged = true;
                    }

                    $centroids[$cIdx]['revenue'] = $newRev;
                    $centroids[$cIdx]['bookings'] = $newBkg;
                }
            }

            $clusters = $newClusters;
            $iterations++;
        }

        // Sort clusters by average revenue to assign logical labels (Tinggi, Sedang, Rendah)
        $clusterStats = [];
        foreach ($clusters as $cIdx => $indices) {
            $avgRev = 0;
            if (count($indices) > 0) {
                $sumRev = 0;
                foreach ($indices as $idx) {
                    $sumRev += $data[$idx]['revenue'];
                }
                $avgRev = $sumRev / count($indices);
            }
            $clusterStats[] = [
                'index' => $cIdx,
                'avg_revenue' => $avgRev,
                'indices' => $indices,
            ];
        }

        usort($clusterStats, fn ($a, $b) => $b['avg_revenue'] <=> $a['avg_revenue']);
        $labels = ['Tinggi', 'Sedang', 'Rendah'];

        $finalData = [];
        $clusterSummaries = [];
        foreach ($clusterStats as $rank => $stat) {
            $label = $labels[$rank] ?? "Cluster $rank";
            $clusterSummaries[$label] = [];
            foreach ($stat['indices'] as $idx) {
                $item = $data[$idx];
                $item['cluster'] = $label;
                $finalData[] = $item;
                $clusterSummaries[$label][] = $item;
            }
        }

        return [
            'data' => $finalData,
            'summary' => $clusterSummaries,
        ];
    }
}
