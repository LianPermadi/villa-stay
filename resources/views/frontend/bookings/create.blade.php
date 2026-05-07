@extends("layouts.app")

@section("title", "Pesan Villa - " . $villa->name)

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
                    <div class="absolute inset-0 bg-black/20"></div>
                    <!-- Image placeholder with icon -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="absolute inset-0 bg-black/20"></div>
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
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Tambahan</label>
                            <textarea name="special_requests" rows="3" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">{{ old("special_requests") }}</textarea>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2">Ringkasan Biaya</h4>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Harga per malam</span>
                                <span class="font-semibold">Rp {{ number_format($villa->price_per_night, 0, ",", ".") }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Jumlah malam</span>
                                <span class="font-semibold" id="numNightsDisplay">-</span>
                            </div>
                            <div class="border-t border-gray-200 my-2"></div>
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-primary">Total Biaya</span>
                                <span class="font-bold text-xl text-primary" id="totalPriceDisplay">Rp 0</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary w-full justify-center">
                            Konfirmasi Pemesanan
                        </button>
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
        const numNightsDisplay = document.getElementById("numNightsDisplay");
        const totalPriceDisplay = document.getElementById("totalPriceDisplay");
        const pricePerNight = {{ $villa->price_per_night }};
        
        function calculateTotal() {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                const timeDiff = checkOut.getTime() - checkIn.getTime();
                const numNights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                const total = numNights * pricePerNight;
                
                numNightsDisplay.textContent = numNights + " malam";
                totalPriceDisplay.textContent = "Rp " + total.toLocaleString("id-ID");
            } else {
                numNightsDisplay.textContent = "-";
                totalPriceDisplay.textContent = "Rp 0";
            }
        }
        
        checkInInput.addEventListener("change", calculateTotal);
        checkOutInput.addEventListener("change", calculateTotal);
        
        checkInInput.addEventListener("change", function() {
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split("T")[0];
            }
        });
    });
</script>
@endsection
