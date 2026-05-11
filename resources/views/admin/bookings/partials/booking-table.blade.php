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
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($bookings as $booking)
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
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center text-primary hover:text-primary-dark p-1" title="Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
