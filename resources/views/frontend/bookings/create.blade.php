@extends("layouts.app")

@section("title", "Pesan Villa - " . $villa->name)

@section("styles")
<style>
    .payment-option {
        border: 2px solid #e5e7eb;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(45, 90, 39, 0.06);
    }

    .payment-option:hover {
        border-color: rgba(45, 90, 39, 0.45);
        background: rgba(248, 245, 240, 0.7);
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(45, 90, 39, 0.12);
    }

    .payment-option:has(input:checked) {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(45, 90, 39, 0.08), rgba(201, 169, 98, 0.12));
        box-shadow: 0 18px 38px rgba(45, 90, 39, 0.16);
    }

    .payment-check {
        border: 2px solid #d1d5db;
        background: #fff;
        color: transparent;
    }

    .payment-option:has(input:checked) .payment-check {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
    }

    .payment-option:has(input:checked) .payment-title {
        color: var(--primary);
    }

    .payment-option:has(input:checked) .payment-divider {
        border-color: rgba(45, 90, 39, 0.18);
    }

    .payment-badge {
        background: rgba(201, 169, 98, 0.22);
        color: var(--secondary);
    }
</style>
@endsection

@section("content")
<div class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route("villas.show", $villa->id) }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="animate-in">
                 <div class="h-64 relative overflow-hidden rounded-2xl mb-4">
                     @php
                         $primaryImage = $villa->images->where('is_primary', true)->first();
                     @endphp
                     @if($primaryImage && file_exists(public_path('storage/' . $primaryImage->image_path)))
                         <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                     @else
                         <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                             <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                             </svg>
                         </div>
                     @endif
                 </div>
                <h2 class="font-display text-3xl font-bold text-primary mb-2">{{ $villa->name }}</h2>
                <p class="text-gray-600 mb-4">{{ $villa->description }}</p>
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-primary font-bold text-2xl">Rp {{ number_format($villa->price_per_night, 0, ",", ".") }}</span>
                    <span class="text-gray-500">/ malam</span>
                </div>
            </div>
            
            <div class="animate-in stagger-1">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h3 class="font-display text-2xl font-bold text-primary mb-6">Form Pemesanan</h3>
                    
                    @if(session("success"))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session("success") }}
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
                    
                    <form action="{{ route("bookings.store", $villa->id) }}" method="POST">
                        @csrf
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Check-in</label>
                                <input type="date" name="check_in" value="{{ old("check_in") }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required min="{{ date("Y-m-d", strtotime("+1 day")) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Check-out</label>
                                <input type="date" name="check_out" value="{{ old("check_out") }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Tamu</label>
                            <input type="number" name="num_guests" value="{{ old("num_guests") }}" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                required min="1" max="{{ $villa->capacity }}">
                            <p class="text-sm text-gray-500 mt-1">Maksimal {{ $villa->capacity }} tamu</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="guest_name" value="{{ old("guest_name") }}" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                required>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="guest_email" value="{{ old("guest_email") }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP</label>
                                <input type="tel" name="guest_phone" value="{{ old("guest_phone") }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                            <textarea name="special_requests" rows="3" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">{{ old("special_requests") }}</textarea>
                        </div>
                        
                        <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 mb-6">
                            <h4 class="font-semibold text-primary mb-3">Informasi Pembayaran</h4>
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm">
                                    <p class="font-semibold">Pembayaran DP wajib {{ $villa->down_payment_percentage }}% dari total biaya</p>
                                    <p class="text-gray-600">Pelunasan H-7 sampai H-1 sebelum check-in</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Plan Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Jenis Pembayaran</label>
                            <div class="grid md:grid-cols-2 gap-4">
                                <label id="dp-option" for="payment_plan_dp" class="payment-option relative flex min-h-[180px] cursor-pointer flex-col overflow-hidden p-5 transition-all duration-200 ease-out">
                                    <input type="radio" id="payment_plan_dp" name="payment_plan" value="dp" {{ old('payment_plan', 'dp') == 'dp' ? 'checked' : '' }} class="sr-only">

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="payment-title block font-display text-xl font-bold text-gray-900">DP</span>
                                            <span class="mt-1 block text-sm font-semibold text-secondary">Down Payment</span>
                                        </div>
                                        <span class="payment-check flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition-all">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    </div>

                                    <div class="mt-5 flex items-center gap-2">
                                        <span class="payment-badge rounded-full px-3 py-1 text-sm font-bold">{{ $villa->down_payment_percentage }}%</span>
                                        <span class="text-sm text-gray-600">bayar di awal</span>
                                    </div>

                                    <div class="payment-divider mt-auto border-t border-gray-200 pt-4">
                                        <p class="text-sm font-medium text-gray-600">Sisanya dilunasi pada H-7 sampai H-1 sebelum check-in.</p>
                                    </div>
                                </label>

                                <label id="full-option" for="payment_plan_full" class="payment-option relative flex min-h-[180px] cursor-pointer flex-col overflow-hidden p-5 transition-all duration-200 ease-out">
                                    <input type="radio" id="payment_plan_full" name="payment_plan" value="full" {{ old('payment_plan') == 'full' ? 'checked' : '' }} class="sr-only">

                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="payment-title block font-display text-xl font-bold text-gray-900">Pelunasan Langsung</span>
                                            <span class="mt-1 block text-sm font-semibold text-secondary">Pembayaran penuh</span>
                                        </div>
                                        <span class="payment-check flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition-all">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    </div>

                                    <div class="mt-5 flex items-center gap-2">
                                        <span class="payment-badge rounded-full px-3 py-1 text-sm font-bold">100%</span>
                                        <span class="text-sm text-gray-600">lunas di awal</span>
                                    </div>

                                    <div class="payment-divider mt-auto border-t border-gray-200 pt-4">
                                        <p class="text-sm font-medium text-gray-600">Tidak ada sisa tagihan setelah booking dibuat.</p>
                                    </div>
                                </label>
                            </div>
                            @error('payment_plan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2">Ringkasan Biaya</h4>
                            <div id="cost-summary" class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Harga per malam</span>
                                    <span class="font-semibold">{{ $villa->getFormattedPriceAttribute() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Jumlah malam</span>
                                    <span class="font-semibold" id="numNightsDisplay">-</span>
                                </div>
                                <div class="border-t border-gray-200 my-2"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-primary" id="dp-label">DP ({{ $villa->down_payment_percentage }}%)</span>
                                    <span class="font-semibold text-primary" id="dpAmountDisplay">Rp -</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Sisa pembayaran</span>
                                    <span class="font-semibold" id="remainingDisplay">Rp -</span>
                                </div>
                                <div class="border-t border-gray-200 my-2"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-primary">Total Biaya</span>
                                    <span class="font-bold text-xl text-primary" id="totalPriceDisplay">Rp 0</span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary w-full justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Buat Booking &amp; Lanjut ke Pembayaran
                        </button>
                        
                        <p class="text-xs text-gray-500 text-center mt-3">
                            Dengan membuat booking, Anda menyetujui syarat &amp; ketentuan VilaStay
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkInInput = document.querySelector("input[name=check_in]");
        const checkOutInput = document.querySelector("input[name=check_out]");
        const paymentPlanInputs = document.querySelectorAll("input[name=payment_plan]");
        const numNightsDisplay = document.getElementById("numNightsDisplay");
        const totalPriceDisplay = document.getElementById("totalPriceDisplay");
        const dpAmountDisplay = document.getElementById("dpAmountDisplay");
        const remainingDisplay = document.getElementById("remainingDisplay");
        const dpLabel = document.getElementById("dp-label");
        const pricePerNight = {{ $villa->price_per_night }};
        const dpPercentage = {{ $villa->down_payment_percentage }};
        
        function formatRupiah(num) {
            return "Rp " + Math.round(num).toLocaleString("id-ID");
        }
        
        function calculateTotal() {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                const timeDiff = checkOut.getTime() - checkIn.getTime();
                const numNights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                const totalPrice = numNights * pricePerNight;
                
                // Get selected payment plan
                const selectedPlan = document.querySelector("input[name=payment_plan]:checked").value;
                let dpAmount, remainingAmount, dpLabelText;
                
                if (selectedPlan === 'full') {
                    dpAmount = totalPrice;
                    remainingAmount = 0;
                    dpLabelText = 'Pelunasan Lengkap (100%)';
                } else {
                    dpAmount = totalPrice * (dpPercentage / 100);
                    remainingAmount = totalPrice - dpAmount;
                    dpLabelText = 'DP ({{ $villa->down_payment_percentage }}%)';
                }
                
                numNightsDisplay.textContent = numNights + " malam";
                totalPriceDisplay.textContent = formatRupiah(totalPrice);
                dpAmountDisplay.textContent = formatRupiah(dpAmount);
                remainingDisplay.textContent = formatRupiah(remainingAmount);
                dpLabel.textContent = dpLabelText;
            } else {
                numNightsDisplay.textContent = "-";
                totalPriceDisplay.textContent = "Rp 0";
                dpAmountDisplay.textContent = "Rp -";
                remainingDisplay.textContent = "Rp -";
                dpLabel.textContent = 'DP ({{ $villa->down_payment_percentage }}%)';
            }
        }
        
        function updateRadioStyles() {
            return;
            if (false) {
                // DP selected — green theme
                dpOption.classList.add('border-primary', 'bg-primary/5', 'shadow-md', 'scale-[1.02]');
                dpOption.classList.remove('border-gray-200');
                
                fullOption.classList.remove('border-primary', 'shadow-md', 'scale-[1.02]');
                fullOption.classList.add('border-gray-200');
            } else {
                // Full payment selected
                fullOption.classList.add('border-primary', 'shadow-md', 'scale-[1.02]');
                fullOption.classList.remove('border-gray-200');
                
                dpOption.classList.remove('border-primary', 'bg-primary/5', 'shadow-md', 'scale-[1.02]');
                dpOption.classList.add('border-gray-200');
            }
        }
        
        checkInInput.addEventListener("change", calculateTotal);
        checkOutInput.addEventListener("change", calculateTotal);
        paymentPlanInputs.forEach(input => {
            input.addEventListener("change", function() {
                calculateTotal();
            });
        });
        
        checkInInput.addEventListener("change", function() {
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split("T")[0];
            }
        });
        
        // Initialize calculation
        calculateTotal();
    });
</script>
@endsection
