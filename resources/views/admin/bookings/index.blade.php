@extends("layouts.app")

@section("title", "Daftar Booking - Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="font-display text-3xl font-bold text-primary">Daftar Booking</h1>
            <p class="text-gray-600">Kelola semua pemesanan villa</p>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" placeholder="Cari berdasarkan nama user atau villa..." class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                </div>
                <select class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Villa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">DP</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pembayaran</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">#{{ $booking->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->villa->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($booking->villa->location ?? '', 20) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $booking->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->num_nights }} malam</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="text-primary font-medium">Rp {{ number_format($booking->down_payment_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $bookingStatus = [
                                        'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Pending'],
                                        'confirmed' => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'Confirmed'],
                                        'completed' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Completed'],
                                        'cancelled' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Cancelled'],
                                    ][$booking->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'label' => $booking->status];
                                    $paymentStatus = [
                                        'none' => ['class' => 'bg-gray-100 text-gray-800', 'label' => 'Belum bayar'],
                                        'dp_paid' => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'DP Paid'],
                                        'fully_paid' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Lunas'],
                                        'refunded' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Dikembalikan'],
                                    ][$booking->payment_status] ?? ['class' => 'bg-gray-100 text-gray-800', 'label' => $booking->payment_status];
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $bookingStatus['class'] }}">
                                        {{ $bookingStatus['label'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium w-fit {{ $paymentStatus['class'] }}">
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary hover:text-primary-dark p-1" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($booking->payment && $booking->payment->status === 'pending')
                                    <form action="{{ route('admin.bookings.verify', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                        @csrf
                                        <input type="hidden" name="payment_id" value="{{ $booking->payment->id }}">
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1" title="Verifikasi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p>Tidak ada booking</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $bookings->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
