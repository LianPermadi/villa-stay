@extends("layouts.app")

@section("title", "Detail Booking - Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark mb-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Booking
                </a>
                <h1 class="font-display text-3xl font-bold text-primary">Detail Booking #{{ $booking->id }}</h1>
            </div>
            
            <div class="flex gap-2">
                <form action="{{ route('admin.bookings.update_status', $booking) }}" method="POST" class="inline">
                    @csrf
                    <select name="status" onchange="if(confirm('Ubah status booking?')) this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary">
                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Payment Status Alert -->
        @if($booking->payment && $booking->payment->status === 'pending')
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Pembayaran menunggu verifikasi</strong> - Upload bukti pembayaran oleh customer perlu diverifikasi</span>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('admin.bookings.verify', $booking) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                        @csrf
                        <input type="hidden" name="payment_id" value="{{ $booking->payment->id }}">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Verifikasi
                        </button>
                    </form>
                    <button onclick="showRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($booking->reject_status !== 'none' && $booking->reject_status !== 'rejected')
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold">
                        @if($booking->reject_status === 'partial_refund')
                            Pengembalian Dana Parsial
                        @elseif($booking->reject_status === 'full_refund')
                            Pengembalian Dana Lengkap
                        @endif
                    </p>
                    <p class="text-sm mt-1">Jumlah: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</p>
                    <p class="text-sm">Status: <span class="font-semibold">{{ $booking->refund_status === 'completed' ? 'Sudah dibayarkan' : 'Menunggu proses' }}</span></p>
                    @if($booking->refund_status === 'pending')
                    <form action="{{ route('admin.bookings.process_refund', $booking) }}" method="POST" class="mt-2 inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Proses Pengembalian</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Booking</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-600 text-sm">Check-in</span>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Check-out</span>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Malam</span>
                            <p class="font-medium">{{ $booking->num_nights }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Tamu</span>
                            <p class="font-medium">{{ $booking->num_guests }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Tanggal Booking</span>
                            <p class="font-medium">{{ $booking->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Batas Pembayaran</span>
                            <p class="font-medium {{ $booking->is_overdue ? 'text-red-600' : '' }}">
                                {{ $booking->payment_due_date ? \Carbon\Carbon::parse($booking->payment_due_date)->format('d M Y') : '-' }}
                                @if($booking->is_overdue)<span class="text-xs text-red-600 font-semibold">(Lewat batas)</span>@endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Villa Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Villa</h2>
                    <div class="flex gap-4">
                        @php
                            $villaImage = $booking->villa->primaryImage;
                        @endphp
                        <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                            @if($villaImage && file_exists(public_path('storage/' . $villaImage->image_path)))
                                <img src="{{ asset('storage/' . $villaImage->image_path) }}" alt="{{ $booking->villa->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-primary">{{ $booking->villa->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $booking->villa->location ?? '' }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->villa->bedrooms }} kamar tidur | {{ $booking->villa->bathrooms }} kamar mandi | Kapasitas {{ $booking->villa->capacity }} orang</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h2>
                    
                    @if($booking->payment)
                    <div class="mb-6">
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-gray-50 p-3 rounded">
                                <span class="text-gray-600 text-sm">Total</span>
                                <p class="font-bold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <span class="text-gray-600 text-sm">DP ({{ $booking->villa->down_payment_percentage }}%)</span>
                                <p class="font-bold">Rp {{ number_format($booking->down_payment_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <span class="text-gray-600 text-sm">Sisa</span>
                                <p class="font-bold">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <span class="text-gray-600 text-sm">Status</span>
                                <p class="font-bold {{ $booking->payment->status === 'verified' ? 'text-green-600' : ($booking->payment->status === 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                    {{ ucfirst($booking->payment->status) }}
                                </p>
                            </div>
                        </div>

                        @if($booking->payment->payment_method)
                        <div class="border-t pt-4">
                            <h4 class="font-medium mb-2">Detail Pembayaran</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Metode</span>
                                    <span>{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID</span>
                                    <span class="font-mono text-xs">{{ $booking->payment->transaction_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jenis</span>
                                    <span>{{ $booking->payment->payment_type === 'down_payment' ? 'DP' : 'Pelunasan' }}</span>
                                </div>
                                @if($booking->payment->proof_image)
                                @php
                                    $proofUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($booking->payment->proof_image);
                                    $proofTitle = ($booking->payment->payment_type === 'down_payment' ? 'DP' : 'Pelunasan') . ' - ' . $booking->payment->transaction_id;
                                @endphp
                                <div class="mt-3">
                                    <span class="text-gray-600 block mb-2">Bukti Transfer</span>
                                    <button type="button"
                                        class="js-payment-proof-trigger group w-full max-w-sm overflow-hidden rounded-lg border border-gray-200 bg-white text-left transition hover:border-primary hover:shadow-md"
                                        data-proof-url="{{ $proofUrl }}"
                                        data-proof-title="{{ $proofTitle }}">
                                        <img src="{{ $proofUrl }}" alt="Bukti pembayaran {{ $booking->payment->transaction_id }}" class="h-48 w-full object-cover transition duration-300 group-hover:scale-105">
                                        <span class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-primary">
                                            Klik untuk memperbesar bukti pembayaran
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                                @endif
                                @if($booking->payment->admin_notes)
                                <div class="mt-2 p-2 bg-gray-50 rounded text-xs">
                                    <strong>Catatan Admin:</strong> {{ $booking->payment->admin_notes }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-gray-500 text-center py-8">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p>Belum ada pembayaran dikirimkan</p>
                    </div>
                    @endif
                </div>

                <!-- Rejection Info -->
                @if($booking->reject_status !== 'none')
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-red-800 mb-2">Pembayaran Ditolak</h2>
                    <p class="text-red-700 mb-2">{{ $booking->rejection_reason }}</p>
                    @if($booking->refund_amount)
                    <p class="font-semibold">Pengembalian dana: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</p>
                    <p class="text-sm text-red-600">Status: {{ $booking->refund_status === 'completed' ? 'Sudah dibayarkan' : 'Menunggu proses' }}</p>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Guest Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Tamu</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">Nama Lengkap</span>
                            <p class="font-medium">{{ $booking->guest_name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Email</span>
                            <p class="font-medium">{{ $booking->guest_email }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">No. HP</span>
                            <p class="font-medium">{{ $booking->guest_phone }}</p>
                        </div>
                        @if($booking->special_requests)
                        <div>
                            <span class="text-gray-600 text-sm">Permintaan Khusus</span>
                            <p class="font-medium">{{ $booking->special_requests }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Villa Quick Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Villa</h3>
                    <p class="font-bold text-primary">{{ $booking->villa->name }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $booking->villa->location }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $booking->villa->bedrooms }} Bedroom | {{ $booking->villa->bathrooms }} Bathroom | {{ $booking->villa->area }} m²</p>
                </div>

                <!-- Payment Actions -->
                @if($booking->payment && $booking->payment->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h3 class="font-semibold text-yellow-800 mb-4">Aksi Pembayaran</h3>
                    <form action="{{ route('admin.bookings.verify', $booking) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="payment_id" value="{{ $booking->payment->id }}">
                        <input type="hidden" name="admin_notes" value="">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium">
                            Verifikasi Pembayaran
                        </button>
                    </form>
                    <button onclick="showRejectModal()" class="w-full bg-red-100 hover:bg-red-200 text-red-700 py-2 rounded-lg font-medium">
                        Tolak Pembayaran
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="paymentProofModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/75 p-4" role="dialog" aria-modal="true" aria-labelledby="paymentProofTitle">
    <div class="relative w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h3 id="paymentProofTitle" class="font-display text-xl font-bold text-primary">Bukti Pembayaran</h3>
            <button type="button" onclick="closePaymentProofModal()" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800" aria-label="Tutup modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="bg-gray-50 p-4">
            <img id="paymentProofImage" src="" alt="Bukti pembayaran" class="mx-auto max-h-[78vh] w-auto max-w-full rounded-lg object-contain shadow-sm">
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($booking->payment && $booking->payment->status === 'pending')
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Tolak Pembayaran</h3>
        <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
            @csrf
            <input type="hidden" name="payment_id" value="{{ $booking->payment->id }}">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea name="rejection_reason" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Pengembalian</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="refund_type" value="none" checked class="w-4 h-4">
                        <span>Tidak ada pengembalian</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="refund_type" value="partial" class="w-4 h-4">
                        <span>Pengembalian parsial</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="refund_type" value="full" class="w-4 h-4">
                        <span>Pengembalian full</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Tolak Pembayaran</button>
            </div>
        </form>
    </div>
</div>

@endif

 <script>
 const paymentProofModal = document.getElementById('paymentProofModal');
 const paymentProofImage = document.getElementById('paymentProofImage');
 const paymentProofTitle = document.getElementById('paymentProofTitle');

 function openPaymentProofModal(src, title) {
     paymentProofImage.src = src;
     paymentProofTitle.textContent = title || 'Bukti Pembayaran';
     paymentProofModal.classList.remove('hidden');
     paymentProofModal.classList.add('flex');
     document.body.classList.add('overflow-hidden');
 }

 function closePaymentProofModal() {
     paymentProofModal.classList.add('hidden');
     paymentProofModal.classList.remove('flex');
     paymentProofImage.src = '';
     document.body.classList.remove('overflow-hidden');
 }

 document.querySelectorAll('.js-payment-proof-trigger').forEach(function(button) {
     button.addEventListener('click', function() {
         openPaymentProofModal(this.dataset.proofUrl, this.dataset.proofTitle);
     });
 });

 paymentProofModal.addEventListener('click', function(event) {
     if (event.target === paymentProofModal) {
         closePaymentProofModal();
     }
 });

 document.addEventListener('keydown', function(event) {
     if (event.key === 'Escape' && !paymentProofModal.classList.contains('hidden')) {
         closePaymentProofModal();
     }
 });

 function showRejectModal() {
     const rejectModal = document.getElementById('rejectModal');
     if (rejectModal) {
         rejectModal.style.display = 'flex';
     }
 }
 function hideRejectModal() {
     const rejectModal = document.getElementById('rejectModal');
     if (rejectModal) {
         rejectModal.style.display = 'none';
     }
 }
 </script>
 @endsection
