@extends("layouts.app")

@section("title", "Riwayat Pemesanan - VilaStay")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="font-display text-4xl font-bold text-primary mb-4">Riwayat Pemesanan</h1>
            <p class="text-gray-600">Kelola dan lihat riwayat pemesanan villa Anda</p>
        </div>
        
        @if(session("success"))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session("success") }}
        </div>
        @endif
        
        @if($bookings->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada pemesanan</h3>
            <p class="text-gray-500 mb-6">Mulai petualangan Anda dengan memesan villa impian</p>
            <a href="{{ route('villas.index') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Cari Villa
            </a>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Villa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tamu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $booking->villa->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-600">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->num_nights }} malam</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-600">{{ $booking->num_guests }} tamu</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($booking->status === 'confirmed')
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Dikonfirmasi</span>
                                @elseif($booking->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                @elseif($booking->status === 'cancelled')
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                @else
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-primary hover:text-primary-dark font-medium">Lihat Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection