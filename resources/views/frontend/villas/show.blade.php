@extends("layouts.app")

@section("title", $villa->name . " - VilaStay")

@section("content")
<div class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route("villas.index") }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark mb-6 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Villa
        </a>
        
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Villa Gallery -->
            <div class="animate-in">
                <div class="h-96 relative overflow-hidden rounded-2xl mb-4">
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
                    @if($villa->is_featured)
                    <span class="absolute top-4 left-4 badge badge-available">Unggulan</span>
                    @endif
                    <span class="absolute top-4 right-4 badge {{ $villa->status === "available" ? "badge-available" : "badge-pending" }}">
                        {{ $villa->status === "available" ? "Tersedia" : "Tidak Tersedia" }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    @forelse($villa->images as $image)
                    <div class="h-24 bg-gray-200 rounded-lg overflow-hidden">
                        @if(file_exists(public_path('storage/' . $image->image_path)))
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Villa image" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary/30 to-primary-dark/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Villa Details -->
            <div class="animate-in stagger-1">
                <h1 class="font-display text-4xl font-bold text-primary mb-4">{{ $villa->name }}</h1>
                <p class="text-gray-600 mb-6">{{ $villa->description }}</p>
                
                <div class="flex items-center gap-6 mb-6">
                    <span class="text-primary font-bold text-3xl">Rp {{ number_format($villa->price_per_night, 0, ',', '.') }}</span>
                    <span class="text-gray-500">/ malam</span>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Kapasitas</span>
                        <p class="font-semibold">{{ $villa->capacity }} orang</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Kamar Tidur</span>
                        <p class="font-semibold">{{ $villa->bedrooms }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Kamar Mandi</span>
                        <p class="font-semibold">{{ $villa->bathrooms }}</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="font-display text-lg font-semibold mb-3">Fasilitas</h3>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $amenities = is_string($villa->amenities) ? json_decode($villa->amenities, true) : $villa->amenities;
                        @endphp
                        @if(is_array($amenities) && count($amenities) > 0)
                            @foreach($amenities as $amenity)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                {{ $amenity }}
                            </span>
                            @endforeach
                        @else
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">WiFi</span>
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">AC</span>
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">Kolam Renang</span>
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">Parkir</span>
                        @endif
                    </div>
                </div>
                
                @auth
                <a href="{{ route('bookings.create', $villa->id) }}" class="btn-primary w-full md:w-auto text-center block">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Pesan Sekarang
                </a>
                @else
                <a href="{{ route('login') }}" class="btn-primary w-full md:w-auto text-center block">
                    Masuk untuk Memesan
                </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
