@extends("layouts.app")

@section("title", "Detail Booking - Villa-Sina")

@section("content")
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark mb-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Booking
                </a>
                <h1 class="font-display text-3xl font-bold text-primary">Detail Booking #{{ $booking->id }}</h1>
            </div>
            
            @if($booking->status === 'pending')
            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking?')">
                @csrf
                <button type="submit" class="btn-danger">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batalkan Booking
                </button>
            </form>
            @endif
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Booking</h2>
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ][$booking->status] ?? 'bg-gray-100 text-gray-800';
                            $statusLabel = [
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ][$booking->status] ?? $booking->status;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ms-auto {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-600 text-sm">Tanggal Check-in</span>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Tanggal Check-out</span>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Jumlah Malam</span>
                            <p class="font-medium">{{ $booking->num_nights }} malam</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Jumlah Tamu</span>
                            <p class="font-medium">{{ $booking->num_guests }} orang</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Tanggal Booking</span>
                            <p class="font-medium">{{ $booking->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($booking->payment_due_date)
                        <div>
                            <span class="text-gray-600 text-sm">Batas Pembayaran</span>
                            <p class="font-medium {{ $booking->is_overdue ? 'text-red-600' : '' }}">
                                {{ \Carbon\Carbon::parse($booking->payment_due_date)->format('d M Y') }}
                                @if($booking->is_overdue)<span class="text-xs text-red-600 font-semibold"> (Lewat batas)</span>@endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Villa Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Villa</h2>
                    <div class="flex gap-4">
                        <div class="w-24 h-24 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ $booking->villa->primary_image_url }}" alt="{{ $booking->villa->name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-primary">{{ $booking->villa->name }}</h3>
                            <p class="text-gray-600 text-sm mb-1">{{ $booking->villa->location ?? 'Lokasi tidak tersedia' }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->villa->bedrooms }} kamar tidur | {{ $booking->villa->bathrooms }} kamar mandi | Kapasitas {{ $booking->villa->capacity }} orang</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Pembayaran</h2>
                    
                    <!-- Payment Progress -->
                    <div class="flex items-center mb-6">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full {{ $booking->payment_status !== 'none' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center text-sm">
                                1
                            </div>
                            <span class="ml-2 text-sm font-medium">DP</span>
                        </div>
                        <div class="flex-1 h-1 mx-2 {{ $booking->payment_status === 'fully_paid' ? 'bg-primary' : 'bg-gray-200' }}"></div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full {{ $booking->payment_status === 'fully_paid' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center text-sm">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium">Pelunasan</span>
                        </div>
                    </div>

                    @php
                        $paymentStatus = [
                            'none' => ['label' => 'Belum dibayar', 'class' => 'text-gray-600'],
                            'dp_paid' => ['label' => 'DP Dibayar', 'class' => 'text-blue-600'],
                            'fully_paid' => ['label' => 'Lunas', 'class' => 'text-green-600'],
                            'refunded' => ['label' => 'Dikembalikan', 'class' => 'text-red-600'],
                        ][$booking->payment_status] ?? ['label' => $booking->payment_status, 'class' => 'text-gray-600'];
                    @endphp

                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-gray-600 text-sm">Total Pembayaran</span>
                            <p class="text-xl font-bold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-gray-600 text-sm">DP ({{ $booking->villa->down_payment_percentage ?? 30 }}%)</span>
                            <p class="text-xl font-semibold {{ $booking->payment_status !== 'none' ? 'text-green-600' : '' }}">
                                Rp {{ number_format($booking->down_payment_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-gray-600 text-sm">Sisa Pembayaran</span>
                            <p class="text-xl font-semibold {{ $booking->payment_status === 'fully_paid' ? 'text-green-600' : '' }}">
                                Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-gray-600 text-sm">Status Pembayaran</span>
                            <p class="text-xl font-semibold {{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</p>
                        </div>
                    </div>

                    <!-- Payment Proof Upload Section -->
                    @if(!in_array($booking->status, ['cancelled', 'completed']) && $booking->payment_status !== 'fully_paid')
                        @php
                            $dpPayment = $booking->payments->where('payment_type', 'down_payment')->first();
                            $finalPayment = $booking->payments->where('payment_type', 'final_payment')->first();
                            $pendingApproval = $booking->payments->where('status', 'pending')->whereNotNull('proof_image')->first();
                            $isFullPaymentBooking = (float) $booking->remaining_amount <= 0;
                            // Determine which payments are still needed
                            $needDP = !$isFullPaymentBooking && (!$dpPayment || $dpPayment->status !== 'verified');
                            $rawNeedFinal = $isFullPaymentBooking
                                ? (!$finalPayment || $finalPayment->status !== 'verified')
                                : ($dpPayment && $dpPayment->status === 'verified' && ($booking->remaining_amount > 0) && (!$finalPayment || $finalPayment->status !== 'verified'));
                            $finalPaymentStartDate = \Carbon\Carbon::parse($booking->check_in)->subDays(7)->startOfDay();
                            $finalPaymentEndDate = \Carbon\Carbon::parse($booking->check_in)->subDay()->endOfDay();
                            $now = \Carbon\Carbon::now();
                            $canPayFinalNow = $isFullPaymentBooking || ($rawNeedFinal && $now->betweenIncluded($finalPaymentStartDate, $finalPaymentEndDate));
                            $needFinal = $rawNeedFinal && $canPayFinalNow;
                            $canUpload = !$pendingApproval && ($needDP || $needFinal);
                        @endphp

                        @if($pendingApproval)
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg mb-4">
                                <p class="font-medium">Pembayaran sedang diverifikasi</p>
                                <p class="text-sm mt-1">Silakan tunggu admin memverifikasi bukti pembayaran Anda.</p>
                            </div>
                        @elseif($dpPayment && $dpPayment->status === 'rejected')
                            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-4">
                                <p class="font-medium">Pembayaran DP ditolak</p>
                                <p class="text-sm mt-1"><strong>Alasan:</strong> {{ $dpPayment->admin_notes }}</p>
                                <p class="text-sm mt-1">Silakan upload ulang bukti pembayaran DP.</p>
                            </div>
                        @elseif($rawNeedFinal && !$canPayFinalNow)
                            <div class="bg-primary/5 border border-primary/20 text-gray-700 p-4 rounded-lg mb-4">
                                <p class="font-medium text-primary">Jadwal pelunasan belum tersedia</p>
                                @if($now->lt($finalPaymentStartDate))
                                    <p class="text-sm mt-1">
                                        Pelunasan sisa pembayaran dapat dilakukan mulai
                                        <strong>{{ $finalPaymentStartDate->format('d M Y') }}</strong>
                                        sampai <strong>{{ $finalPaymentEndDate->format('d M Y') }}</strong>.
                                    </p>
                                @else
                                    <p class="text-sm mt-1">
                                        Batas pelunasan sudah lewat. Pelunasan hanya dapat dilakukan sampai
                                        <strong>{{ $finalPaymentEndDate->format('d M Y') }}</strong>.
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if($canUpload)
                            <form action="{{ route('bookings.upload_payment', $booking) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-5 rounded-2xl border border-primary/15 bg-primary/5 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Rekening Tujuan Transfer</p>
                                            @if($adminPaymentAccount)
                                                <h3 class="mt-1 font-display text-2xl font-bold text-primary">{{ $adminPaymentAccount->bank_name }}</h3>
                                                <p class="text-sm text-gray-600">Atas nama {{ $adminPaymentAccount->bank_account_holder }}</p>
                                            @else
                                                <h3 class="mt-1 font-display text-xl font-bold text-red-700">Rekening admin belum tersedia</h3>
                                                <p class="text-sm text-gray-600">Silakan hubungi admin sebelum melakukan transfer bank.</p>
                                            @endif
                                        </div>

                                        @if($adminPaymentAccount)
                                        <div class="rounded-xl bg-white p-4 text-left shadow-sm sm:min-w-64">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor Rekening</span>
                                            <div class="mt-1 flex items-center gap-3">
                                                <span id="admin-bank-account-number" class="font-mono text-xl font-bold text-primary">{{ $adminPaymentAccount->bank_account_number }}</span>
                                                <button type="button" onclick="copyAdminBankAccount()" class="rounded-lg border border-primary/20 px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                                                    Salin
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <p id="copy-bank-account-feedback" class="mt-3 hidden text-sm font-semibold text-green-700">Nomor rekening berhasil disalin.</p>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                                        <select name="payment_method" id="payment_method" required class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                            <option value="">Pilih Metode</option>
                                            <optgroup label="Transfer Bank">
                                                <option value="transfer_bca" {{ old('payment_method') === 'transfer_bca' ? 'selected' : '' }}>Transfer BCA</option>
                                                <option value="transfer_bni" {{ old('payment_method') === 'transfer_bni' ? 'selected' : '' }}>Transfer BNI</option>
                                                <option value="transfer_bri" {{ old('payment_method') === 'transfer_bri' ? 'selected' : '' }}>Transfer BRI</option>
                                                <option value="transfer_mandiri" {{ old('payment_method') === 'transfer_mandiri' ? 'selected' : '' }}>Transfer Mandiri</option>
                                            </optgroup>
                                            <optgroup label="E-Wallet dan Lainnya">
                                                <option value="shopeepay" {{ old('payment_method') === 'shopeepay' ? 'selected' : '' }}>ShopeePay</option>
                                                <option value="gopay" {{ old('payment_method') === 'gopay' ? 'selected' : '' }}>GoPay</option>
                                                <option value="ovo" {{ old('payment_method') === 'ovo' ? 'selected' : '' }}>OVO</option>
                                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Lainnya</option>
                                            </optgroup>
                                        </select>
                                        @error('payment_method')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div id="bank-transfer-field" class="transition-all duration-300 ease-out">
                                        <label for="transaction_id" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Rekening Transfer <span class="text-red-500">*</span></label>
                                        <input type="text" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" class="w-full px-4 py-3 rounded-lg border @error('transaction_id') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Contoh: 1234567890">
                                        <p class="text-xs text-gray-500 mt-1">Wajib untuk Transfer Bank. Untuk e-wallet dan metode lain, field ini otomatis disembunyikan.</p>
                                        @error('transaction_id')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div id="payment-method-hint" class="mb-4 hidden rounded-xl border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-gray-700 transition-all duration-300">
                                    <span class="font-semibold text-primary">Metode non-transfer dipilih.</span>
                                    Nomor rekening tidak diperlukan untuk metode ini.
                                </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pembayaran</label>
                                            <div class="flex flex-col gap-2">
                                                @if($needDP)
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="payment_type" value="down_payment" checked class="w-4 h-4 text-primary focus:ring-primary">
                                                    <span>Down Payment (DP) - Rp {{ number_format($booking->down_payment_amount, 0, ',', '.') }}</span>
                                                </label>
                                                @endif
                                                @if($needFinal)
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="payment_type" value="final_payment" {{ ( !$needDP && $needFinal ) ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary">
                                                    <span>
                                                        @if($isFullPaymentBooking)
                                                            Pelunasan Lengkap (100%) - Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                                        @else
                                                            Pelunasan (H-7 s/d H-1) - Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}
                                                        @endif
                                                    </span>
                                                </label>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($booking->remaining_amount == 0)
                                                    Pembayaran lunas sekali pembayaran
                                                @else
                                                    Pelunasan dapat dilakukan setelah DP terverifikasi pada H-7 sampai H-1 check-in
                                                @endif
                                            </p>
                                        </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Bukti Pembayaran</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg text-center hover:border-primary transition">
                                        <input type="file" id="proof_image" name="proof_image" accept="image/jpeg,image/png,image/jpg" class="hidden" required onchange="previewImage(this)">
                                        <label for="proof_image" class="block cursor-pointer p-6">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-gray-600">Klik untuk upload bukti pembayaran</p>
                                            <p class="text-sm text-gray-400 mt-1">JPEG, PNG, JPG (maks 2MB)</p>
                                        </label>
                                    </div>
                                    <!-- Image Preview -->
                                    <p id="image-preview-error" class="mt-3 hidden text-sm font-semibold text-red-600"></p>
                                    <div id="image-preview" class="mt-4 hidden">
                                        <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                        <div class="relative inline-block">
                                            <img id="preview-img" src="" alt="Preview" class="max-w-xs max-h-48 rounded-lg border border-gray-200 object-contain">
                                            <button type="button" onclick="clearImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (opsional)</label>
                                    <textarea name="notes" rows="2" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                                </div>

                                <button type="submit" class="btn-primary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload Bukti Pembayaran
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- Refund Info -->
                    @if($booking->reject_status !== 'none')
                    <div class="border-t pt-6">
                        <div class="bg-{{ $booking->reject_status === 'full_refund' ? 'green' : 'yellow' }}-50 border border-{{ $booking->reject_status === 'full_refund' ? 'green' : 'yellow' }}-200 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-{{ $booking->reject_status === 'full_refund' ? 'green' : 'yellow' }}-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold">
                                        @if($booking->reject_status === 'rejected')
                                            Booking Ditolak
                                        @elseif($booking->reject_status === 'partial_refund')
                                            Pengembalian Dana Parsial
                                        @else
                                            Pengembalian Dana Lengkap
                                        @endif
                                    </h4>
                                    <p class="text-sm mt-1">{{ $booking->rejection_reason }}</p>
                                    @if($booking->refund_amount)
                                    <p class="text-sm font-semibold mt-2">
                                        Jumlah pengembalian: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}
                                        @if($booking->refund_status === 'completed')
                                            <span class="text-green-600">(Sudah dibayarkan)</span>
                                        @else
                                            <span class="text-yellow-600">(Menunggu proses)</span>
                                        @endif
                                    </p>
                                    @php
                                        $refundPayment = $booking->payments->where('payment_type', 'refund')->sortByDesc('created_at')->first();
                                        $refundProofUrl = $refundPayment?->proof_image_url;
                                    @endphp
                                    @if($refundProofUrl)
                                    <button type="button"
                                        class="js-payment-proof-trigger mt-3 inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-primary shadow-sm ring-1 ring-green-100 hover:ring-primary"
                                        data-proof-url="{{ $refundProofUrl }}"
                                        data-proof-title="Bukti Pengembalian Dana Booking #{{ $booking->id }}">
                                        Lihat bukti pengembalian dana
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </button>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Guest Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Tamu</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">Nama</span>
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

                <!-- Payment Proof Details -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Riwayat Pembayaran</h3>
                    <div class="space-y-4">
                        @forelse($booking->payments as $payment)
                        <div class="border rounded-lg p-3 {{ $payment->status === 'verified' ? 'border-green-200 bg-green-50' : ($payment->status === 'rejected' ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50') }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span>
                                    <p class="text-sm text-gray-600">Rp {{ number_format(abs($payment->amount), 0, ',', '.') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $payment->status === 'verified' ? 'bg-green-100 text-green-800' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            @if($payment->payment_method)
                            <p class="text-sm"><strong>Metode:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            @endif
                            @if($payment->transaction_id)
                            <p class="text-sm"><strong>Transaksi:</strong> {{ $payment->transaction_id }}</p>
                            @endif
                            @php
                                $proofUrl = $payment->proof_image_url;
                                $proofTitle = ucfirst(str_replace('_', ' ', $payment->payment_type)) . ($payment->transaction_id ? ' - ' . $payment->transaction_id : '');
                            @endphp
                            @if($proofUrl)
                            <div class="mt-2">
                                <button type="button"
                                    class="js-payment-proof-trigger group w-full overflow-hidden rounded-lg border border-gray-200 bg-white text-left transition hover:border-primary hover:shadow-md"
                                    data-proof-url="{{ $proofUrl }}"
                                    data-proof-title="{{ $proofTitle }}">
                                    <img src="{{ $proofUrl }}" alt="Bukti pembayaran {{ $payment->transaction_id }}" class="h-36 w-full object-cover transition duration-300 group-hover:scale-105">
                                    <span class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-primary">
                                        Lihat bukti pembayaran
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            @else
                            <div class="mt-2 rounded-lg border border-dashed border-gray-300 bg-white/60 p-3 text-center text-xs text-gray-500">
                                Bukti pembayaran belum tersedia
                            </div>
                            @endif
                            @if($payment->admin_notes)
                            <div class="mt-2 p-2 bg-white/50 rounded text-xs">
                                <strong>Catatan Admin:</strong> {{ $payment->admin_notes }}
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">Belum ada pembayaran</p>
                        @endforelse
                    </div>
                </div>

                <!-- Villa Quick Info -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Villa</h3>
                    <p class="font-bold text-primary">{{ $booking->villa->name }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $booking->villa->location }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $booking->villa->bedrooms }} Bedroom | {{ $booking->villa->bathrooms }} Bathroom | {{ $booking->villa->area }} m²</p>
                </div>
            </div>
        </div>
    </div>
 </div>

 <div id="payment-proof-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 p-4" role="dialog" aria-modal="true" aria-labelledby="payment-proof-title">
     <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">
         <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
             <h3 id="payment-proof-title" class="font-display text-xl font-bold text-primary">Bukti Pembayaran</h3>
             <button type="button" onclick="closePaymentProofModal()" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-800" aria-label="Tutup modal">
                 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                 </svg>
             </button>
         </div>
         <div class="bg-gray-50 p-4">
             <img id="payment-proof-modal-img" src="" alt="Bukti pembayaran" class="mx-auto max-h-[75vh] w-auto max-w-full rounded-lg object-contain shadow-sm">
         </div>
     </div>
 </div>
 @endsection

 @section("scripts")
 <script>
     const paymentProofModal = document.getElementById('payment-proof-modal');
     const paymentProofModalImg = document.getElementById('payment-proof-modal-img');
     const paymentProofTitle = document.getElementById('payment-proof-title');

     function openPaymentProofModal(src, title) {
         paymentProofModalImg.src = src;
         paymentProofTitle.textContent = title || 'Bukti Pembayaran';
         paymentProofModal.classList.remove('hidden');
         paymentProofModal.classList.add('flex');
         document.body.classList.add('overflow-hidden');
     }

     function closePaymentProofModal() {
         paymentProofModal.classList.add('hidden');
         paymentProofModal.classList.remove('flex');
         paymentProofModalImg.src = '';
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

     function copyAdminBankAccount() {
         const accountNumber = document.getElementById('admin-bank-account-number');
         const feedback = document.getElementById('copy-bank-account-feedback');

         if (!accountNumber || !navigator.clipboard) {
             return;
         }

         navigator.clipboard.writeText(accountNumber.textContent.trim()).then(function() {
             if (feedback) {
                 feedback.classList.remove('hidden');
                 window.setTimeout(function() {
                     feedback.classList.add('hidden');
                 }, 2500);
             }
         });
     }

     document.addEventListener('DOMContentLoaded', function() {
         const paymentMethod = document.getElementById('payment_method');
         const bankTransferField = document.getElementById('bank-transfer-field');
         const transactionInput = document.getElementById('transaction_id');
         const paymentMethodHint = document.getElementById('payment-method-hint');
         const bankTransferMethods = ['transfer_bca', 'transfer_bni', 'transfer_bri', 'transfer_mandiri'];

         if (!paymentMethod || !bankTransferField || !transactionInput || !paymentMethodHint) {
             return;
         }

         function syncPaymentForm() {
             const isBankTransfer = bankTransferMethods.includes(paymentMethod.value);

             transactionInput.required = isBankTransfer;
             transactionInput.disabled = !isBankTransfer;

             if (isBankTransfer) {
                 bankTransferField.classList.remove('hidden', 'opacity-0', 'translate-y-2');
                 paymentMethodHint.classList.add('hidden');
             } else {
                 transactionInput.value = '';
                 bankTransferField.classList.add('hidden', 'opacity-0', 'translate-y-2');
                 paymentMethodHint.classList.toggle('hidden', !paymentMethod.value);
             }
         }

         paymentMethod.addEventListener('change', syncPaymentForm);
         syncPaymentForm();
     });

     function previewImage(input) {
         const previewContainer = document.getElementById('image-preview');
         const previewImg = document.getElementById('preview-img');
         const previewError = document.getElementById('image-preview-error');
         const allowedTypes = ['image/jpeg', 'image/png'];
         const maxSize = 2 * 1024 * 1024;
         
         if (input.files && input.files[0]) {
             const file = input.files[0];

             if (!allowedTypes.includes(file.type)) {
                 clearImage();
                 previewError.textContent = 'Format bukti pembayaran harus JPG, JPEG, atau PNG.';
                 previewError.classList.remove('hidden');
                 return;
             }

             if (file.size > maxSize) {
                 clearImage();
                 previewError.textContent = 'Ukuran bukti pembayaran maksimal 2MB.';
                 previewError.classList.remove('hidden');
                 return;
             }

             previewError.textContent = '';
             previewError.classList.add('hidden');
             const reader = new FileReader();
             reader.onload = function(e) {
                 previewImg.src = e.target.result;
                 previewContainer.classList.remove('hidden');
             }
             reader.readAsDataURL(input.files[0]);
         }
     }
     
     function clearImage() {
         const input = document.getElementById('proof_image');
         const previewContainer = document.getElementById('image-preview');
         const previewImg = document.getElementById('preview-img');
         
         input.value = '';
         previewImg.src = '';
         previewContainer.classList.add('hidden');
         const previewError = document.getElementById('image-preview-error');
         if (previewError) {
             previewError.textContent = '';
             previewError.classList.add('hidden');
         }
     }
 </script>
 @endsection
