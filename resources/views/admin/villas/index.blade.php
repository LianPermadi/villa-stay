@extends("layouts.app")

@section("title", "Kelola Villa - Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="font-display text-3xl font-bold text-primary">Kelola Villa</h1>
                <p class="text-gray-600">Tambah, edit, atau hapus data villa</p>
            </div>
            <a href="{{ route('admin.villas.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Villa
            </a>
        </div>

        <!-- Villas Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Villa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Harga</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Kapasitas</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Unggulan</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($villas as $villa)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @php
                                            $primaryImage = $villa->images->where('is_primary', true)->first();
                                        @endphp
                                        @if($primaryImage && file_exists(public_path('storage/' . $primaryImage->image_path)))
                                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $villa->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $villa->name }}</h3>
                                        <p class="text-sm text-gray-500 line-clamp-1">{{ Str::limit($villa->description, 50) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-primary font-bold">Rp {{ number_format($villa->price_per_night, 0, ',', '.') }}</span>
                                <span class="text-gray-500 text-sm">/ malam</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $villa->capacity }} tamu
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $villa->status === 'available' ? 'badge-available' : ($villa->status === 'maintenance' ? 'badge-pending' : 'badge-cancelled') }}">
                                    {{ match($villa->status) {
                                        'available' => 'Tersedia',
                                        'unavailable' => 'Tidak Tersedia',
                                        'maintenance' => 'Pemeliharaan',
                                        default => $villa->status
                                    } }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($villa->is_featured)
                                    <span class="badge badge-available">Unggulan</span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.villas.edit', $villa) }}" class="btn-secondary text-sm py-2 px-4">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.villas.destroy', $villa) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus villa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-sm py-2 px-4 text-red-600 border-red-300 hover:bg-red-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada villa. <a href="{{ route('admin.villas.create') }}" class="text-primary hover:underline">Tambah villa pertama</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $villas->links() }}
        </div>
    </div>
</div>
@endsection
