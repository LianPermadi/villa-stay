@extends("layouts.app")

@section("title", "Dashboard Admin - Villa-Sina")

@section("styles")
<style>
    .analytics-panel {
        background:
            linear-gradient(135deg, rgba(45, 90, 39, 0.08), rgba(201, 169, 98, 0.11)),
            #ffffff;
    }

    .analytics-input {
        background: rgba(255, 255, 255, 0.92);
    }
</style>
@endsection

@section("content")
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-display text-3xl font-bold text-primary">Dashboard Admin</h1>
            <p class="text-gray-600">Selamat datang di panel admin Villa-Sina</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Villa</p>
                        <p class="text-3xl font-bold text-primary">{{ $totalVillas }}</p>
                    </div>
                    <a href="{{ route('admin.villas.index') }}" class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center hover:bg-primary/20 transition" title="Kelola Villa">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Aktif / Siap Booking</p>
                        <p class="text-3xl font-bold text-green-600">{{ $villaStats['available'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Tidak Aktif</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $villaStats['unavailable'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Renovasi / Maintenance</p>
                        <p class="text-3xl font-bold text-red-600">{{ $villaStats['maintenance'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Villas -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-display text-2xl font-bold text-primary">Villa Terbaru</h2>
                <a href="{{ route('admin.villas.index') }}" class="text-primary hover:text-primary-dark font-medium flex items-center gap-1">
                    Kelola Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentVillas as $villa)
                <div class="border rounded-xl overflow-hidden hover:shadow-lg transition">
                    <div class="h-24 relative bg-gray-100">
                        <img src="{{ $villa->primary_image_url }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                        <span class="absolute top-2 right-2 badge 
                            {{ $villa->status === 'available' ? 'badge-available' : 
                               ($villa->status === 'maintenance' ? 'badge-pending' : 'badge-cancelled') }}">
                            @if($villa->status === 'available')
                                Aktif
                            @elseif($villa->status === 'unavailable')
                                Tidak Aktif
                            @elseif($villa->status === 'maintenance')
                                Renovasi
                            @else
                                {{ $villa->status }}
                            @endif
                        </span>
                        <span class="absolute bottom-2 left-2 badge {{ $villa->is_occupied_today ? 'badge-cancelled' : 'badge-available' }}">
                            {{ $villa->occupancy_label }}
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 text-sm">{{ $villa->name }}</h3>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-primary font-bold">Rp {{ number_format($villa->price_per_night, 0, ',', '.') }}</span>
                            <span class="text-gray-500 text-xs">/ malam</span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span>{{ $villa->capacity }} tamu</span>
                            <span>{{ $villa->bedrooms }} kmr</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-display text-2xl font-bold text-primary">Pemesanan Terbaru</h2>
                <a href="{{ route("admin.bookings.index") }}" class="text-primary hover:text-primary-dark font-medium flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 text-sm font-semibold text-gray-600">Villa</th>
                            <th class="text-left py-3 text-sm font-semibold text-gray-600">Tamu</th>
                            <th class="text-left py-3 text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="text-left py-3 text-sm font-semibold text-gray-600">Total</th>
                            <th class="text-left py-3 text-sm font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3">{{ $booking->villa->name }}</td>
                            <td class="py-3">{{ $booking->guest_name }}</td>
                            <td class="py-3">{{ \Carbon\Carbon::parse($booking->check_in)->format("d M Y") }}</td>
                            <td class="py-3 font-semibold">Rp {{ number_format($booking->total_price, 0, ",", ".") }}</td>
                            <td class="py-3">
                                <span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Belum ada pemesanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Analytics Filters and Charts -->
        <div class="analytics-panel rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between mb-6">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Revenue Analytics</p>
                    <h2 class="font-display text-3xl font-bold text-primary mt-1">Grafik Pendapatan & Prediksi</h2>
                    <p class="text-gray-600 mt-1">Filter data pendapatan dan prediksi Moving Average berdasarkan periode atau villa.</p>
                </div>

                @if(request()->hasAny(['month', 'year', 'date_from', 'date_to', 'villa_id']))
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary text-sm text-center">
                    Reset Filter
                </a>
                @endif
            </div>

            <form id="analytics-filter-form" action="{{ route('admin.dashboard') }}" method="GET" class="grid md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                    <select name="month" class="analytics-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <option value="">Semua Bulan</option>
                        @foreach([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ] as $monthValue => $monthLabel)
                        <option value="{{ $monthValue }}" {{ (string) $filters['month'] === (string) $monthValue ? 'selected' : '' }}>{{ $monthLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="analytics-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <option value="">Semua Tahun</option>
                        @forelse($availableYears as $year)
                        <option value="{{ $year }}" {{ (string) $filters['year'] === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
                        @empty
                        <option value="{{ now()->year }}" {{ (string) $filters['year'] === (string) now()->year ? 'selected' : '' }}>{{ now()->year }}</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="analytics-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="analytics-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Villa</label>
                    <select name="villa_id" class="analytics-input w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <option value="">Semua Villa</option>
                        @foreach($villas as $villa)
                        <option value="{{ $villa->id }}" {{ (string) $filters['villa_id'] === (string) $villa->id ? 'selected' : '' }}>{{ $villa->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-primary/10">
                    <p class="text-sm font-semibold text-gray-500">Total Pendapatan Filter</p>
                    <p class="mt-2 text-3xl font-bold text-primary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-primary/10">
                    <p class="text-sm font-semibold text-gray-500">Transaksi Terfilter</p>
                    <p class="mt-2 text-3xl font-bold text-primary">{{ $filteredTransactions }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-primary/10">
                    <p class="text-sm font-semibold text-gray-500">Rata-rata Transaksi</p>
                    <p class="mt-2 text-3xl font-bold text-primary">Rp {{ number_format($averageRevenue, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid xl:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="font-display text-xl font-bold text-primary">Grafik Pendapatan</h3>
                            <p class="text-sm text-gray-500">Pendapatan bulanan sesuai filter.</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm border border-gray-100">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                        <div>
                            <h3 class="font-display text-xl font-bold text-primary">Prediksi Moving Average</h3>
                            <p class="text-sm text-gray-500">Prediksi memakai rata-rata {{ $movingAverageData['window'] }} periode terakhir dari data terfilter.</p>
                        </div>
                        <div class="rounded-xl bg-primary/5 px-4 py-3 text-sm">
                            <span class="block text-gray-500">Prediksi berikutnya</span>
                            <span class="font-bold text-primary">
                                @if($nextPrediction['value'] !== null)
                                    {{ $nextPrediction['period'] }} - Rp {{ number_format($nextPrediction['value'], 0, ',', '.') }}
                                @else
                                    Belum cukup data
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="movingAverageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterForm = document.getElementById("analytics-filter-form");
        let submitTimer = null;

        filterForm.querySelectorAll("select, input[type=date]").forEach(function(input) {
            input.addEventListener("change", function() {
                window.clearTimeout(submitTimer);
                submitTimer = window.setTimeout(function() {
                    filterForm.submit();
                }, 250);
            });
        });

        const formatRupiah = function(value) {
            return "Rp " + Number(value || 0).toLocaleString("id-ID");
        };

        const ctx = document.getElementById("revenueChart").getContext("2d");
        const revenueData = @json($monthlyRevenue);
        
        const labels = revenueData.map(item => item.period);
        const data = revenueData.map(item => item.total);
        
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Pendapatan (Rp)",
                    data: data,
                    backgroundColor: "rgba(45, 90, 39, 0.78)",
                    borderColor: "rgba(45, 90, 39, 1)",
                    borderWidth: 1,
                    borderRadius: 10,
                    hoverBackgroundColor: "rgba(201, 169, 98, 0.85)"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1100,
                    easing: "easeOutQuart"
                },
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return "Pendapatan: " + formatRupiah(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatRupiah(value);
                            }
                        }
                    }
                }
            }
        });

        const movingAverageData = @json($movingAverageData);
        const movingAverageCtx = document.getElementById("movingAverageChart").getContext("2d");

        new Chart(movingAverageCtx, {
            type: "line",
            data: {
                labels: movingAverageData.labels,
                datasets: [
                    {
                        label: "Pendapatan Aktual",
                        data: movingAverageData.actual,
                        borderColor: "rgba(45, 90, 39, 1)",
                        backgroundColor: "rgba(45, 90, 39, 0.12)",
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: "Prediksi Moving Average",
                        data: movingAverageData.predicted,
                        borderColor: "rgba(201, 169, 98, 1)",
                        backgroundColor: "rgba(201, 169, 98, 0.12)",
                        borderWidth: 3,
                        borderDash: [8, 6],
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: false,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: "easeOutQuart"
                },
                interaction: {
                    intersect: false,
                    mode: "index"
                },
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ": " + formatRupiah(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatRupiah(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
