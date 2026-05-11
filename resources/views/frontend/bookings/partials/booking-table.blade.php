<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Villa</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tamu</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pembayaran</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($bookings as $booking)
            @php
                $bookingBadge = [
                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => 'Pending'],
                    'confirmed' => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'Confirmed'],
                    'completed' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Completed'],
                    'cancelled' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Cancelled'],
                ][$booking->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'label' => $booking->status];

                $paymentBadge = [
                    'none' => ['class' => 'bg-gray-100 text-gray-800', 'label' => 'Belum bayar'],
                    'dp_paid' => ['class' => 'bg-blue-100 text-blue-800', 'label' => 'DP Paid'],
                    'fully_paid' => ['class' => 'bg-green-100 text-green-800', 'label' => 'Lunas'],
                    'refunded' => ['class' => 'bg-red-100 text-red-800', 'label' => 'Refunded'],
                ][$booking->payment_status] ?? ['class' => 'bg-gray-100 text-gray-800', 'label' => $booking->payment_status];
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $booking->villa->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-gray-600">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->num_nights }} malam</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-gray-600">{{ $booking->num_guests }} tamu</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $bookingBadge['class'] }}">
                        {{ $bookingBadge['label'] }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $paymentBadge['class'] }}">
                        {{ $paymentBadge['label'] }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('bookings.show', $booking) }}" class="text-primary hover:text-primary-dark font-medium">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
