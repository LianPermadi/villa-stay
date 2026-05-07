@extends("layouts.app")

@section("title", "Dashboard Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-display text-3xl font-bold text-primary">Dashboard Admin</h1>
            <p class="text-gray-600">Selamat datang di panel admin VilaStay</p>
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
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-secondary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pemesanan</p>
                        <p class="text-3xl font-bold text-secondary">{{ $totalBookings }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-accent">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-accent">Rp {{ number_format($totalRevenue, 0, ",", ".") }}</p>
                    </div>
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
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
                    <div class="h-32 relative bg-gray-100">
                        @php
                            $primaryImage = $villa->images->where('is_primary', true)->first();
                        @endphp
                        @if($primaryImage && file_exists(public_path('storage/' . $primaryImage->image_path)))
                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                                <svg class="w-10 h-10 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 badge 
                            {{ $villa->status === 'available' ? 'badge-available' : 
                               ($villa->status === 'maintenance' ? 'badge-pending' : 'badge-cancelled') }}">
                            @if($villa->status === 'available')
                                Aktif / Siap Booking
                            @elseif($villa->status === 'unavailable')
                                Tidak Aktif
                            @elseif($villa->status === 'maintenance')
                                Renovasi
                            @else
                                {{ $villa->status }}
                            @endif
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $villa->name }}</h3>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-primary font-bold">Rp {{ number_format($villa->price_per_night, 0, ',', '.') }}</span>
                            <span class="text-gray-500">/ malam</span>
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
        
        <!-- Revenue Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="font-display text-2xl font-bold text-primary mb-6">Grafik Pendapatan</h2>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
                    backgroundColor: "rgba(45, 90, 39, 0.7)",
                    borderColor: "rgba(45, 90, 39, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return "Rp " + value.toLocaleString("id-ID");
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
