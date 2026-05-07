@extends("layouts.app")

@section("title", "Profil Pengguna - VilaStay")

@section("content")
<div class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-display text-4xl font-bold text-primary mb-4">Profil Pengguna</h1>
            <p class="text-gray-600">Kelola informasi profil Anda</p>
        </div>
        
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
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="animate-in">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-primary to-primary-dark rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h2 class="font-semibold text-lg text-gray-900">{{ Auth::user()->name }}</h2>
                        <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <div class="space-y-2">
                        <a href="#profile" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary/10 text-primary font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Informasi Profil
                        </a>
                        <a href="#bookings" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Riwayat Pemesanan
                        </a>
                        <a href="#settings" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="md:col-span-2 space-y-8">
                <!-- Profile Form -->
                <div id="profile" class="bg-white rounded-2xl shadow-lg p-8 animate-in">
                    <h2 class="font-display text-2xl font-bold text-primary mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profil
                    </h2>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    required>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP</label>
                                <input type="tel" name="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                    placeholder="Contoh: 081234567890">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                                <input type="text" value="{{ ucfirst(Auth::user()->role) }}" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-100 text-gray-600" 
                                    readonly>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                            <textarea name="address" rows="3" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                placeholder="Masukkan alamat lengkap Anda...">{{ old('address', Auth::user()->address) }}</textarea>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-primary px-8 py-3 rounded-xl font-semibold">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Recent Bookings -->
                <div id="bookings" class="bg-white rounded-2xl shadow-lg p-8 animate-in stagger-1">
                    <h2 class="font-display text-2xl font-bold text-primary mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Riwayat Pemesanan Terbaru
                    </h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Villa</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tamu</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse(Auth::user()->bookings()->latest()->take(5)->get() as $booking)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $booking->villa->name }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-600">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->num_nights }} malam</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-600">{{ $booking->num_guests }} tamu</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($booking->status === 'confirmed')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Dikonfirmasi</span>
                                        @elseif($booking->status === 'pending')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                        @elseif($booking->status === 'cancelled')
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                        @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada riwayat pemesanan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(Auth::user()->bookings()->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('bookings.index') }}" class="text-primary hover:text-primary-dark font-medium">
                            Lihat semua riwayat pemesanan →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection