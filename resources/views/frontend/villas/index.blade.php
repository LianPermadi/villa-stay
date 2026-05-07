@extends("layouts.app")

@section("title", "Daftar Villa - VilaStay")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center animate-in">
            <h1 class="font-display text-4xl md:text-5xl font-bold text-primary mb-4">Daftar Villa</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Temukan villa impian Anda untuk liburan yang tak terlupakan</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-12 animate-in stagger-1">
            <form action="{{ route('villas.index') }}" method="GET" class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Villa</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Nama villa...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas Tamu</label>
                    <select name="capacity" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <option value="">Pilih kapasitas</option>
                        <option value="2">2 orang</option>
                        <option value="4">4 orang</option>
                        <option value="6">6 orang</option>
                        <option value="8">8+ orang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Minimum</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Maksimum</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                        placeholder="Rp 0">
                </div>
                <div class="md:col-span-2 lg:col-span-4">
                    <button type="submit" class="btn-primary w-full md:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Villa
                    </button>
                </div>
            </form>
        </div>
        
        <div class="flex justify-between items-center mb-8 animate-in stagger-2">
            <p class="text-gray-600">Menampilkan {{ $villas->total() }} villa</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($villas as $villa)
            <div class="card-villa">
                <div class="h-48 relative overflow-hidden">
                    @php
                        $primaryImage = $villa->images->where('is_primary', true)->first();
                    @endphp
                    @if($primaryImage && file_exists(public_path('storage/' . $primaryImage->image_path)))
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                            <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    @if($villa->is_featured)
                    <span class="absolute top-4 left-4 badge badge-available">Unggulan</span>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="font-display text-xl font-semibold mb-2">{{ $villa->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $villa->description }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-primary font-bold text-2xl">Rp {{ number_format($villa->price_per_night, 0, ',', '.') }}</span>
                        <span class="text-gray-500 text-sm">/ malam</span>
                    </div>
                    <div class="flex items-center text-gray-600 text-sm mb-4">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ $villa->capacity }} tamu
                    </div>
                    <a href="{{ route('villas.show', $villa->id) }}" class="btn-primary block text-center">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">Tidak ada villa yang ditemukan</p>
            </div>
            @endforelse
        </div>
        
        <div class="mt-12 text-center">
            {{ $villas->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
