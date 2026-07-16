@extends('layouts.app')

@section('title', 'Pengaturan Beranda - Villa-Sina')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>#location-picker { min-height: 460px; z-index: 1; }</style>
@endsection

@section('content')
<div class="min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-secondary">Administrasi</p>
                <h1 class="font-display text-4xl font-bold text-primary">Pengaturan Beranda</h1>
                <p class="mt-2 text-gray-600">Ubah tulisan, background, kontak, alamat, dan titik lokasi yang tampil kepada pengunjung.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="btn-secondary text-center">Lihat Beranda</a>
        </div>

        @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
            <p class="font-bold">Pengaturan belum dapat disimpan:</p>
            <ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif

        @php
            $sections = [
                'Identitas & Kontak' => [
                    'site_name' => ['Nama situs', false],
                    'footer_description' => ['Deskripsi footer', true],
                    'contact_email' => ['Email kontak', false],
                    'contact_phone' => ['Nomor telepon', false],
                ],
                'Navigasi & Footer' => [
                    'nav_home' => ['Menu beranda', false],
                    'nav_villas' => ['Menu villa', false],
                    'nav_bookings' => ['Menu pesanan', false],
                    'nav_admin' => ['Menu admin', false],
                    'nav_reports' => ['Menu laporan', false],
                    'nav_settings' => ['Menu pengaturan', false],
                    'nav_profile' => ['Menu profil', false],
                    'nav_logout' => ['Menu keluar', false],
                    'nav_login' => ['Tombol masuk', false],
                    'nav_register' => ['Tombol daftar', false],
                    'footer_services_title' => ['Judul kolom layanan', false],
                    'footer_service_1' => ['Layanan 1', false],
                    'footer_service_2' => ['Layanan 2', false],
                    'footer_service_3' => ['Layanan 3', false],
                    'footer_service_4' => ['Layanan 4', false],
                    'footer_help_title' => ['Judul kolom bantuan', false],
                    'footer_help_1' => ['Bantuan 1', false],
                    'footer_help_2' => ['Bantuan 2', false],
                    'footer_help_3' => ['Bantuan 3', false],
                    'footer_help_4' => ['Bantuan 4', false],
                    'footer_contact_title' => ['Judul kolom kontak', false],
                    'footer_copyright' => ['Tulisan hak cipta', false],
                ],
                'Bagian Hero' => [
                    'hero_title' => ['Judul utama', false],
                    'hero_subtitle' => ['Subjudul utama', true],
                    'hero_button' => ['Tulisan tombol', false],
                ],
                'Bagian Keunggulan' => [
                    'features_title' => ['Judul bagian', false],
                    'features_subtitle' => ['Subjudul bagian', true],
                    'feature_1_title' => ['Judul keunggulan 1', false],
                    'feature_1_description' => ['Deskripsi keunggulan 1', true],
                    'feature_2_title' => ['Judul keunggulan 2', false],
                    'feature_2_description' => ['Deskripsi keunggulan 2', true],
                    'feature_3_title' => ['Judul keunggulan 3', false],
                    'feature_3_description' => ['Deskripsi keunggulan 3', true],
                ],
                'Bagian Villa Unggulan' => [
                    'featured_title' => ['Judul bagian', false],
                    'featured_link' => ['Tulisan tautan semua villa', false],
                    'featured_badge' => ['Tulisan badge', false],
                    'per_night_text' => ['Satuan harga', false],
                    'guest_unit' => ['Satuan kapasitas', false],
                    'bedroom_unit' => ['Satuan kamar', false],
                    'details_button' => ['Tulisan tombol detail', false],
                    'featured_empty' => ['Tulisan saat data kosong', false],
                ],
                'Bagian Ajakan' => [
                    'cta_title' => ['Judul ajakan', false],
                    'cta_subtitle' => ['Subjudul ajakan', true],
                    'cta_button' => ['Tulisan tombol ajakan', false],
                ],
                'Bagian Lokasi' => [
                    'location_title' => ['Judul lokasi', false],
                    'location_kicker' => ['Tulisan kecil lokasi', false],
                    'location_subtitle' => ['Deskripsi lokasi', true],
                    'address_label' => ['Label alamat', false],
                    'map_button' => ['Tulisan tombol peta', false],
                ],
            ];
        @endphp

        <form method="POST" action="{{ route('admin.settings.home.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <section class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="font-display text-2xl font-bold text-primary mb-5">Gambar Background</h2>
                <div class="grid lg:grid-cols-2 gap-6">
                    @foreach(['hero' => 'Background Hero', 'cta' => 'Background Bagian Ajakan'] as $type => $label)
                    <div class="rounded-xl border p-4">
                        <p class="font-bold text-gray-800 mb-3">{{ $label }}</p>
                        @if($settings->backgroundUrl($type))
                        <img src="{{ $settings->backgroundUrl($type) }}" alt="{{ $label }}" class="w-full h-44 object-cover rounded-lg bg-gray-100 mb-4">
                        @else
                        <div class="w-full h-44 rounded-lg bg-primary mb-4 flex items-center justify-center text-white">Warna utama</div>
                        @endif
                        <input type="file" name="{{ $type }}_background" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-white">
                        <label class="mt-3 flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="remove_{{ $type }}_background" value="1" class="rounded"> Gunakan background bawaan</label>
                        <p class="mt-2 text-xs text-gray-500">JPG, PNG, atau WebP. Maksimal 6 MB.</p>
                    </div>
                    @endforeach
                </div>
            </section>

            @foreach($sections as $sectionTitle => $fields)
            <section class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="font-display text-2xl font-bold text-primary mb-5">{{ $sectionTitle }}</h2>
                <div class="grid md:grid-cols-2 gap-5">
                    @foreach($fields as $key => [$label, $isTextarea])
                    <div class="{{ $isTextarea ? 'md:col-span-2' : '' }}">
                        <label for="content_{{ $key }}" class="block text-sm font-semibold text-gray-700 mb-2">{{ $label }}</label>
                        @if($isTextarea)
                        <textarea id="content_{{ $key }}" name="content[{{ $key }}]" rows="3" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">{{ old("content.$key", $settings->text($key)) }}</textarea>
                        @else
                        <input id="content_{{ $key }}" type="text" name="content[{{ $key }}]" value="{{ old("content.$key", $settings->text($key)) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                        @endif
                    </div>
                    @endforeach
                </div>
            </section>
            @endforeach

            <section class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="font-display text-2xl font-bold text-primary mb-2">Alamat & Titik Peta</h2>
                <p class="text-sm text-gray-500 mb-5">Klik area peta atau geser marker ke posisi Villa-Sina. Latitude dan longitude akan terisi otomatis.</p>
                <div class="grid md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                            <p class="font-semibold text-gray-700">Pilih titik pada peta</p>
                            <button type="button" id="use-com-location" class="btn-secondary text-sm px-4 py-2">Ke Beach Road, Com</button>
                        </div>
                        <div id="location-picker" class="rounded-2xl border border-gray-300 shadow-inner overflow-hidden"></div>
                        <p class="mt-2 text-xs text-gray-500">Marker dapat digeser. Anda juga dapat memperbesar peta untuk memilih bangunan dengan lebih tepat.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat lengkap</label>
                        <textarea id="address" name="address" rows="3" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">{{ old('address', $settings->address ?: 'Beach Road, Lautem District, Com, Timor-Leste') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                        <input id="latitude" type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $settings->latitude) }}" required readonly class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                        <input id="longitude" type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $settings->longitude) }}" required readonly class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Zoom peta (1-19)</label>
                        <input type="number" min="1" max="19" name="map_zoom" value="{{ old('map_zoom', $settings->map_zoom) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary">
                    </div>
                </div>
            </section>

            <div class="sticky bottom-4 flex justify-end">
                <button type="submit" class="btn-primary shadow-xl">Simpan Pengaturan Beranda</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const zoomInput = document.querySelector('[name="map_zoom"]');
        const addressInput = document.getElementById('address');
        const comLocation = { latitude: -8.359751, longitude: 127.061091 };
        const initialLatitude = Number(latitudeInput.value) || comLocation.latitude;
        const initialLongitude = Number(longitudeInput.value) || comLocation.longitude;
        const initialZoom = Number(zoomInput.value) || 17;
        const map = L.map('location-picker').setView([initialLatitude, initialLongitude], initialZoom);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([initialLatitude, initialLongitude], { draggable: true }).addTo(map);

        function setLocation(latitude, longitude, moveMap = false) {
            const lat = Number(latitude).toFixed(7);
            const lng = Number(longitude).toFixed(7);
            latitudeInput.value = lat;
            longitudeInput.value = lng;
            marker.setLatLng([lat, lng]);
            if (moveMap) map.setView([lat, lng], 17);
        }

        map.on('click', event => setLocation(event.latlng.lat, event.latlng.lng));
        map.on('zoomend', () => { zoomInput.value = map.getZoom(); });
        marker.on('dragend', event => {
            const point = event.target.getLatLng();
            setLocation(point.lat, point.lng);
        });

        document.getElementById('use-com-location').addEventListener('click', function() {
            setLocation(comLocation.latitude, comLocation.longitude, true);
            addressInput.value = 'Beach Road, Lautem District, Com, Timor-Leste';
            zoomInput.value = 17;
        });
    });
</script>
@endsection
