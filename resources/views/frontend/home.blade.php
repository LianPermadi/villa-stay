@extends("layouts.app")

@section("title", "Beranda - VilaStay")

@section("content")
<!-- Hero Section -->
<section class="hero-section min-h-screen flex items-center justify-center text-center text-white px-4">
    <div class="max-w-4xl mx-auto animate-in stagger-1">
        <h1 class="font-display text-5xl md:text-7xl font-bold mb-6 leading-tight">
            Temukan Villa Impian Anda
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-2xl mx-auto">
            Pengalaman menginap premium di komplek villa pilihan dengan fasilitas lengkap dan pelayanan terbaik
        </p>
        <a href="{{ route("villas.index") }}" class="btn-primary text-lg px-8 py-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
            </svg>
            Cari Villa Sekarang
        </a>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-in">
            <h2 class="font-display text-4xl font-bold text-primary mb-4">Kenapa Memilih VilaStay?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Kami berkomitmen memberikan pengalaman menginap terbaik dengan fasilitas premium dan pelayanan ramah
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center animate-in stagger-1">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">Terpercaya</h3>
                <p class="text-gray-600">Villa dengan kualitas terbaik dan pelayanan profesional</p>
            </div>
            
            <div class="text-center animate-in stagger-2">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">Fasilitas Lengkap</h3>
                <p class="text-gray-600">Dilengkapi dengan berbagai fasilitas modern dan nyaman</p>
            </div>
            
            <div class="text-center animate-in stagger-3">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">Lokasi Strategis</h3>
                <p class="text-gray-600">Berada di kawasan wisata dengan akses mudah ke berbagai tempat</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Villas -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12 animate-in">
            <h2 class="font-display text-4xl font-bold text-primary">Villa Unggulan</h2>
            <a href="{{ route("villas.index") }}" class="text-primary hover:text-primary-dark font-semibold flex items-center gap-2">
                Lihat Semua
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredVillas as $index => $villa)
            <div class="card-villa animate-in stagger-{{ $index + 1 }}">
                <div class="h-48 bg-gradient-to-br from-primary to-primary-dark relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    @if($villa->is_featured)
                    <span class="absolute top-4 left-4 badge badge-available">Unggulan</span>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="font-display text-xl font-semibold mb-2">{{ $villa->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $villa->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-primary font-bold text-lg">{{ \App\Models\Villa::find($villa->id)->formatted_price }}</span>
                        <span class="text-gray-500 text-sm">/ malam</span>
                    </div>
                    <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $villa->capacity }} tamu
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $villa->bedrooms }} kamar
                        </span>
                    </div>
                    <a href="{{ route("villas.show", $villa->id) }}" class="btn-primary w-full justify-center mt-4 text-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-primary text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-display text-4xl font-bold mb-4">Siap untuk Liburan Impian Anda?</h2>
        <p class="text-xl mb-8 text-primary-light">Pesan villa Anda sekarang dan dapatkan pengalaman menginap tak terlupakan</p>
        <a href="{{ route("villas.index") }}" class="btn-secondary text-white border-white hover:bg-white hover:text-primary">
            Mulai Pesan Sekarang
        </a>
    </div>
</section>
@endsection

@section("scripts")
<script>
    // Simple animation on scroll
    document.addEventListener("DOMContentLoaded", function() {
        const animateElements = document.querySelectorAll(".animate-in");
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(el => observer.observe(el));
    });
</script>
@endsection
