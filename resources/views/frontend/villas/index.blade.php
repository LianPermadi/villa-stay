@extends("layouts.app")

@section("title", "Daftar Villa - Villa-Sina")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center animate-in">
            <h1 class="font-display text-4xl md:text-5xl font-bold text-primary mb-4">Daftar Villa</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Temukan villa impian Anda untuk liburan yang tak terlupakan</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-12 animate-in stagger-1">
            <form action="{{ route('villas.index') }}" method="GET" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                        <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 orang</option>
                        <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 orang</option>
                        <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 orang</option>
                        <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+ orang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Harga</label>
                    <select name="price_range" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        <option value="">Semua Harga</option>
                        <option value="0-1000000" {{ request('price_range') == '0-1000000' ? 'selected' : '' }}>< Rp 1.000.000</option>
                        <option value="1000000-2500000" {{ request('price_range') == '1000000-2500000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.500.000</option>
                        <option value="2500000-5000000" {{ request('price_range') == '2500000-5000000' ? 'selected' : '' }}>Rp 2.500.000 - Rp 5.000.000</option>
                        <option value="5000000-" {{ request('price_range') == '5000000-' ? 'selected' : '' }}>> Rp 5.000.000</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
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
                    <img src="{{ $villa->primary_image_url }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                    @if($villa->is_featured)
                    <span class="absolute top-4 left-4 badge badge-available">Unggulan</span>
                    @endif
                    <span class="absolute top-4 right-4 badge {{ $villa->is_occupied_today ? 'badge-cancelled' : 'badge-available' }}">
                        {{ $villa->occupancy_label }}
                    </span>
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
