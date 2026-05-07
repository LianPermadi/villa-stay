@extends("layouts.app")

@section("title", "Detail Pemesanan - VilaStay")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Riwayat
            </a>
        </div>
        
        @if(session("success"))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session("success") }}
        </div>
        @endif
        
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="font-display text-3xl font-bold mb-2">{{ $booking->villa->name }}</h1>
                        <p class="text-primary-light text-lg">Detail Pemesanan</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                        @if($booking->status === 'confirmed')
                        Dikonfirmasi
                        @elseif($booking->status === 'pending')
                        Menunggu
                        @elseif($booking->status === 'cancelled')
                        Dibatalkan
                        @else
                        {{ ucfirst($booking->status) }}
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Detail Villa -->
                    <div class="animate-in">
                        <h2 class="font-display text-xl font-semibold text-primary mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Detail Villa
                        </h2>
                        <div class="bg-gray-50 rounded-xl p-6">
                            <div class="aspect-video bg-gradient-to-br from-primary/20 to-primary-light/20 rounded-lg mb-4"></div>
                            <h3 class="font-semibold text-lg mb-2">{{ $booking->villa->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $booking->villa->description }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $booking->villa->capacity }} tamu
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $booking->villa->bedrooms }} kamar tidur
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Pemesanan -->
                    <div class="animate-in stagger-1">
                        <h2 class="font-display text-xl font-semibold text-primary mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Detail Pemesanan
                        </h2>
                        <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">ID Pemesanan</span>
                                <span class="font-mono text-sm text-gray-900">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Tanggal Check-in</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($booking->check_in)->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Tanggal Check-out</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($booking->check_out)->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Durasi Menginap</span>
                                <span class="font-semibold">{{ $booking->num_nights }} malam</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Jumlah Tamu</span>
                                <span class="font-semibold">{{ $booking->num_guests }} orang</span>
                            </div>
                            @if($booking->special_requests)
                            <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                                <span class="text-gray-600">Catatan Tambahan</span>
                                <span class="font-semibold text-right max-w-xs">{{ $booking->special_requests }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Detail Tamu -->
                <div class="mt-8 animate-in stagger-2">
                    <h2 class="font-display text-xl font-semibold text-primary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Detail Tamu
                    </h2>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                                <p class="font-semibold text-lg">{{ $booking->guest_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Email</label>
                                <p class="font-semibold text-lg">{{ $booking->guest_email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">No. HP</label>
                                <p class="font-semibold text-lg">{{ $booking->guest_phone }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Dipesan Oleh</label>
                                <p class="font-semibold text-lg">{{ $booking->user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ringkasan Biaya -->
                <div class="mt-8 animate-in stagger-3">
                    <h2 class="font-display text-xl font-semibold text-primary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Ringkasan Biaya
                    </h2>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Harga per malam</span>
                                <span class="font-semibold">Rp {{ number_format($booking->villa->price_per_night, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jumlah malam</span>
                                <span class="font-semibold">{{ $booking->num_nights }} malam</span>
                            </div>
                            <div class="border-t border-gray-200 my-3"></div>
                            <div class="flex justify-between items-center text-lg">
                                <span class="font-semibold text-gray-900">Total Biaya</span>
                                <span class="font-bold text-2xl text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('bookings.index') }}" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Kembali ke Daftar
                    </a>
                    @if($booking->status === 'pending')
                    <button class="px-6 py-3 rounded-xl bg-red-500 text-white font-semibold hover:bg-red-600 transition" onclick="confirmCancel()">
                        Batalkan Pemesanan
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($booking->status === 'pending')
<script>
function confirmCancel() {
    if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) {
        // Implementasi cancel booking
        alert('Fitur pembatalan akan segera tersedia');
    }
}
</script>
@endif

@endsection