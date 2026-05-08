@extends("layouts.app")

@section("title", "Tambah Villa - Admin - VilaStay")

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
            <h1 class="font-display text-3xl font-bold text-primary">Tambah Villa Baru</h1>
            <p class="text-gray-600">Isi informasi villa yang akan ditambahkan</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="{{ route('admin.villas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Nama Villa -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Villa <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" 
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
                            placeholder="Jelaskan detail villa..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Harga per Malam -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Harga per Malam (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_per_night" value="{{ old('price_per_night') }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('price_per_night') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="500000" required>
                            @error('price_per_night')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kapasitas -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas Tamu <span class="text-red-500">*</span></label>
                            <input type="number" name="capacity" value="{{ old('capacity') }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('capacity') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="6" required>
                            @error('capacity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kamar Tidur -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kamar Tidur <span class="text-red-500">*</span></label>
                            <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('bedrooms') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="3" required>
                            @error('bedrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kamar Mandi -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kamar Mandi <span class="text-red-500">*</span></label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms') }}" 
                                class="w-full px-4 py-3 rounded-lg border @error('bathrooms') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="2" required>
                            @error('bathrooms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Luas Area -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Luas Area (m²)</label>
                            <input type="number" step="0.01" name="area" value="{{ old('area') }}" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="150.5">
                        </div>

                        <!-- Fasilitas -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas (opsional)</label>
                            <textarea name="amenities" rows="3" 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                placeholder="WiFi&#10;Kolam Renang&#10;Parkir&#10;AC">{{ old('amenities', '') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Satu fasilitas per baris. Contoh: WiFi, Kolam Renang, AC</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" 
                                class="w-full px-4 py-3 rounded-lg border @error('status') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-transparent transition"
                                required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Aktif / Siap Booking</option>
                                <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Renovasi / Maintenance</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Unggulan -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="w-5 h-5 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">Tandai sebagai villa unggulan</label>
                    </div>

                    <!-- Upload Gambar -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Villa</label>
                        
                        <!-- Image Preview Container -->
                        <div id="image-preview-container" class="grid grid-cols-3 gap-4 mb-4"></div>
                        <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition cursor-pointer">
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/jpg" class="hidden" id="image-upload">
                            <label for="image-upload" class="cursor-pointer">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-600">Klik untuk upload gambar villa</p>
                                <p class="text-sm text-gray-400 mt-1">JPEG, PNG, JPG (maks 2MB per file)</p>
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Gambar pertama akan dijadikan gambar utama.</p>
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
                            Simpan Villa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
    let primaryIndex = 0;
    let fileInputs = [];

    document.getElementById('image-upload').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const previewContainer = document.getElementById('image-preview-container');
        
        // Store files
        fileInputs = fileInputs.concat(files);
        
        // Clear and rebuild all previews
        previewContainer.innerHTML = '';
        
        // Auto-select first image as primary if none selected
        if (primaryIndex === -1 && fileInputs.length > 0) {
            primaryIndex = 0;
        }
        
        fileInputs.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group cursor-pointer';
                    previewDiv.dataset.index = index;
                    previewDiv.onclick = function(event) { setPrimary(event, this); };
                    const isPrimary = (index == primaryIndex) ? '' : 'hidden';
                    previewDiv.innerHTML = `
                        <div class="h-24 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                        </div>
                        <span class="absolute -top-2 -right-2 w-5 h-5 bg-green-500 text-white text-xs rounded-full flex items-center justify-center ${isPrimary}" data-badge>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        </span>
                        <span class="absolute top-2 right-2 text-xs text-white bg-black bg-opacity-50 px-2 py-1 rounded" data-label>${index + 1}</span>
                        <button type="button" onclick="removePreview(event, this)" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition" style="z-index: 10;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Update file input with all accumulated files
        updateFileInput();
    });

    function setPrimary(event, element) {
        event.stopPropagation();
        const index = parseInt(element.dataset.index);
        const allBadges = document.querySelectorAll('#image-preview-container [data-badge]');
        allBadges.forEach(badge => badge.classList.add('hidden'));
        
        const badge = element.querySelector('[data-badge]');
        badge.classList.remove('hidden');
        
        primaryIndex = index;
        document.getElementById('primary_image_index').value = index;
    }

    function removePreview(event, button) {
        event.stopPropagation();
        const preview = button.parentElement;
        const index = parseInt(preview.dataset.index);
        preview.remove();
        
        // Remove file from array
        fileInputs.splice(index, 1);
        
        // Adjust primaryIndex
        if (index < primaryIndex) {
            primaryIndex--;
        } else if (index === primaryIndex && fileInputs.length > 0) {
            primaryIndex = 0;
        } else if (fileInputs.length === 0) {
            primaryIndex = -1;
        }
        
        rebuildPreviews();
        updateFileInput();
    }

    function rebuildPreviews() {
        const previewContainer = document.getElementById('image-preview-container');
        previewContainer.innerHTML = '';
        
        fileInputs.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group cursor-pointer';
                    previewDiv.dataset.index = index;
                    previewDiv.onclick = function(event) { setPrimary(event, this); };
                    const isPrimary = (index == primaryIndex) ? '' : 'hidden';
                    previewDiv.innerHTML = `
                        <div class="h-24 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                        </div>
                        <span class="absolute -top-2 -right-2 w-5 h-5 bg-green-500 text-white text-xs rounded-full flex items-center justify-center ${isPrimary}" data-badge>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        </span>
                        <span class="absolute top-2 right-2 text-xs text-white bg-black bg-opacity-50 px-2 py-1 rounded" data-label>${index + 1}</span>
                        <button type="button" onclick="removePreview(event, this)" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition" style="z-index: 10;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function updateFileInput() {
        const fileInput = document.getElementById('image-upload');
        const dt = new DataTransfer();
        fileInputs.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Update primary index in hidden input
        document.getElementById('primary_image_index').value = primaryIndex;
    }
</script>
@endsection
