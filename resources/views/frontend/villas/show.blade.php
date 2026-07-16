@extends("layouts.app")

@section("title", $villa->name . " - Villa-Sina")

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
            <!-- Villa Gallery -->
            <div class="animate-in">
                <!-- Main Carousel Container -->
                <div class="relative group">
                    <div id="gallery-carousel" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar h-96 rounded-2xl mb-4 relative scroll-smooth">
                        @php
                            $allImages = collect([['url' => $villa->primary_image_url]]);
                            foreach($villa->images as $img) {
                                // Add non-primary images to the list
                                if($img->url !== $villa->primary_image_url) {
                                    $allImages->push(['url' => $img->url]);
                                }
                            }
                        @endphp

                        @foreach($allImages as $index => $img)
                        <div id="gallery-img-{{ $index }}" class="w-full flex-shrink-0 snap-center relative">
                            <img src="{{ $img['url'] }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                            
                            <!-- Badges on the first image -->
                            @if($index === 0)
                                @if($villa->is_featured)
                                <span class="absolute top-4 left-4 badge badge-available shadow-md">Unggulan</span>
                                @endif
                                <span class="absolute top-4 right-4 badge shadow-md {{ $villa->status === "available" ? "badge-available" : ($villa->status === "maintenance" ? "badge-pending" : "badge-cancelled") }}">
                                    @if($villa->status === "available")
                                        Aktif / Siap Booking
                                    @elseif($villa->status === "unavailable")
                                        Tidak Aktif
                                    @elseif($villa->status === "maintenance")
                                        Renovasi
                                    @else
                                        {{ $villa->status }}
                                    @endif
                                </span>
                                <span class="absolute bottom-4 right-4 badge shadow-md {{ $villa->is_occupied_today ? 'badge-cancelled' : 'badge-available' }}">
                                    {{ $villa->occupancy_label }}
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Navigation Arrows -->
                    @if($allImages->count() > 1)
                    <button onclick="scrollGallery(-1)" class="absolute top-1/2 left-4 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-lg hover:bg-white text-gray-800 transition z-10 opacity-0 group-hover:opacity-100 focus:opacity-100 outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollGallery(1)" class="absolute top-1/2 right-4 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-lg hover:bg-white text-gray-800 transition z-10 opacity-0 group-hover:opacity-100 focus:opacity-100 outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($allImages->count() > 1)
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                    @foreach($allImages as $index => $img)
                    <button onclick="scrollToImage({{ $index }})" class="h-16 sm:h-20 bg-gray-200 rounded-lg overflow-hidden focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition hover:opacity-100 opacity-70">
                        <img src="{{ $img['url'] }}" alt="Thumbnail {{ $index }}" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
                
                <style>
                    .hide-scrollbar::-webkit-scrollbar { display: none; }
                    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                </style>

                <script>
                    function scrollGallery(direction) {
                        const carousel = document.getElementById('gallery-carousel');
                        const scrollAmount = carousel.clientWidth;
                        carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
                    }
                    function scrollToImage(index) {
                        const carousel = document.getElementById('gallery-carousel');
                        const scrollAmount = carousel.clientWidth;
                        carousel.scrollTo({ left: index * scrollAmount, behavior: 'smooth' });
                    }
                </script>
            </div>
            
            <!-- Villa Details -->
            <div class="animate-in stagger-1">
                <h1 class="font-display text-4xl font-bold text-primary mb-4">{{ $villa->name }}</h1>
                <p class="text-gray-600 mb-6">{{ $villa->description }}</p>
                
                <div class="flex items-center gap-6 mb-6">
                    <span class="text-primary font-bold text-3xl">{{ $villa->formatted_price }}</span>
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
