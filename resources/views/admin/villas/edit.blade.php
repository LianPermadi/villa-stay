@extends("layouts.app")

@section("title", "Edit Villa - Admin - VilaStay")

@section("content")
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.villas.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark mb-4 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Villa
            </a>
            <h1 class="font-display text-3xl font-bold text-primary">Edit Villa</h1>
            <p class="text-gray-600">Perbarui informasi villa</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="{{ route('admin.villas.update', $villa) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Nama Villa -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Villa <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $villa->name) }}" 
                            class="w-full px-4 py-3 rounded-lg border @error('name') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="Contoh: Villa Ocean View" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" 
                            class="w-full px-4 py-3 rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                            placeholder="Jelaskan detail villa..." required>{{ old('description', $villa->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Harga per Malam -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Harga per Malam (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_per_night" value="{{ old('price_per_night', $villa->price_per_night) }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('price_per_night') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="500000" required>
                            @error('price_per_night')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas Tamu <span class="text-red-500">*</span></label>
                            <input type="number" name="capacity" value="{{ old('capacity', $villa->capacity) }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('capacity') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="6" required>
                            @error('capacity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kamar Tidur -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kamar Tidur <span class="text-red-500">*</span></label>
                            <input type="number" name="bedrooms" value="{{ old('bedrooms', $villa->bedrooms) }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('bedrooms') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="3" required>
                            @error('bedrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kamar Mandi -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kamar Mandi <span class="text-red-500">*</span></label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms', $villa->bathrooms) }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('bathrooms') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="2" required>
                            @error('bathrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Luas Area -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Luas Area (m²)</label>
                            <input type="number" step="0.01" name="area" value="{{ old('area', $villa->area) }}" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="150.5">
                        </div>

                        <!-- Fasilitas -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas (opsional)</label>
                            @php
                                $amenitiesString = is_array(old('amenities', $villa->amenities)) 
                                    ? implode("\n", old('amenities', $villa->amenities)) 
                                    : old('amenities', $villa->amenities ?? '');
                            @endphp
                            <textarea name="amenities" rows="3" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="WiFi&#10;Kolam Renang&#10;Parkir&#10;AC">{{ $amenitiesString }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Satu fasilitas per baris. Contoh: WiFi, Kolam Renang, AC</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" 
                                class="w-full px-4 py-3 rounded-lg border @error('status') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                required>
                                <option value="available" {{ (old('status', $villa->status) == 'available') ? 'selected' : '' }}>Tersedia</option>
                                <option value="unavailable" {{ (old('status', $villa->status) == 'unavailable') ? 'selected' : '' }}>Tidak Tersedia</option>
                                <option value="maintenance" {{ (old('status', $villa->status) == 'maintenance') ? 'selected' : '' }}>Pemeliharaan</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Unggulan -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $villa->is_featured) ? 'checked' : '' }}
                            class="w-5 h-5 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">Tandai sebagai villa unggulan</label>
                    </div>

                    <!-- Gambar Saat Ini -->
                    @if($villa->images->count() > 0)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Gambar Saat Ini</label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($villa->images as $image)
                            <div class="relative group">
                                <div class="h-24 bg-gray-200 rounded-lg overflow-hidden">
                                    @if(file_exists(public_path('storage/' . $image->image_path)))
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Villa image" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <span class="absolute -top-2 -right-2 w-5 h-5 bg-green-500 text-white text-xs rounded-full flex items-center justify-center {{ $image->is_primary ? '' : 'hidden' }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Gambar biru adalah gambar utama. Upload gambar baru untuk menambah mengganti.</p>
                    </div>
                    @endif

                    <!-- Upload Gambar Baru -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tambah Gambar Baru</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition cursor-pointer">
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/jpg" class="hidden" id="image-upload">
                            <label for="image-upload" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-600">Klik untuk menambah gambar villa</p>
                                <p class="text-sm text-gray-400 mt-1">JPEG, PNG, JPG (maks 2MB per file)</p>
                            </label>
                        </div>
                        @error('images.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <a href="{{ route('admin.villas.index') }}" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Perbarui Villa
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Villa (Separate form at bottom) -->
            @if($villa->bookings->count() == 0)
            <div class="mt-8 pt-6 border-t">
                <form action="{{ route('admin.villas.destroy', $villa) }}" method="POST" 
                    onsubmit="return confirm('Yakin ingin menghapus villa ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-red-50 text-red-600 rounded-lg font-semibold hover:bg-red-100 transition">
                        Hapus Villa Secara Permanen
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
