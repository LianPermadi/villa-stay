<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSetting extends Model
{
    protected $fillable = [
        'content',
        'hero_background_path',
        'cta_background_path',
        'address',
        'latitude',
        'longitude',
        'map_zoom',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'map_zoom' => 'integer',
        ];
    }

    public static function defaults(): array
    {
        return [
            'site_name' => 'Villa-Sina',
            'nav_home' => 'Beranda',
            'nav_villas' => 'Villa',
            'nav_bookings' => 'Pesanan Saya',
            'nav_admin' => 'Admin',
            'nav_reports' => 'Laporan',
            'nav_settings' => 'Pengaturan',
            'nav_profile' => 'Profil',
            'nav_logout' => 'Keluar',
            'nav_login' => 'Masuk',
            'nav_register' => 'Daftar',
            'hero_title' => 'Temukan Villa Impian Anda',
            'hero_subtitle' => 'Pengalaman menginap premium di komplek villa pilihan dengan fasilitas lengkap dan pelayanan terbaik',
            'hero_button' => 'Cari Villa Sekarang',
            'features_title' => 'Kenapa Memilih Villa-Sina?',
            'features_subtitle' => 'Kami berkomitmen memberikan pengalaman menginap terbaik dengan fasilitas premium dan pelayanan ramah',
            'feature_1_title' => 'Terpercaya',
            'feature_1_description' => 'Villa dengan kualitas terbaik dan pelayanan profesional',
            'feature_2_title' => 'Fasilitas Lengkap',
            'feature_2_description' => 'Dilengkapi dengan berbagai fasilitas modern dan nyaman',
            'feature_3_title' => 'Lokasi Strategis',
            'feature_3_description' => 'Berada di kawasan wisata dengan akses mudah ke berbagai tempat',
            'featured_title' => 'Villa Unggulan',
            'featured_link' => 'Lihat Semua',
            'featured_badge' => 'Unggulan',
            'per_night_text' => '/ malam',
            'guest_unit' => 'tamu',
            'bedroom_unit' => 'kamar',
            'details_button' => 'Lihat Detail',
            'featured_empty' => 'Belum ada villa unggulan.',
            'cta_title' => 'Siap untuk Liburan Impian Anda?',
            'cta_subtitle' => 'Pesan villa Anda sekarang dan dapatkan pengalaman menginap tak terlupakan',
            'cta_button' => 'Mulai Pesan Sekarang',
            'location_title' => 'Lokasi Villa-Sina',
            'location_kicker' => 'Timor-Leste',
            'location_subtitle' => 'Temukan kami di Timor-Leste dan gunakan peta untuk mendapatkan petunjuk perjalanan.',
            'address_label' => 'Alamat',
            'map_button' => 'Buka Petunjuk Lokasi',
            'footer_description' => 'Pengalaman menginap villa premium di Timor-Leste.',
            'contact_email' => 'info@villa-sina.com',
            'contact_phone' => '+670 7700 0000',
            'footer_services_title' => 'Layanan',
            'footer_service_1' => 'Pemesanan Villa',
            'footer_service_2' => 'Layanan Kamar',
            'footer_service_3' => 'Fasilitas Umum',
            'footer_service_4' => 'Area Rekreasi',
            'footer_help_title' => 'Bantuan',
            'footer_help_1' => 'FAQ',
            'footer_help_2' => 'Syarat & Ketentuan',
            'footer_help_3' => 'Kebijakan Privasi',
            'footer_help_4' => 'Hubungi Kami',
            'footer_contact_title' => 'Kontak',
            'footer_copyright' => '© 2026 Villa-Sina. Hak Cipta Dilindungi.',
        ];
    }

    public static function current(): self
    {
        $setting = static::query()->first() ?? new static([
            'address' => 'Beach Road, Lautem District, Com, Timor-Leste',
            'latitude' => -8.3597510,
            'longitude' => 127.0610910,
            'map_zoom' => 17,
        ]);
        $setting->content = array_merge(static::defaults(), $setting->content ?? []);

        return $setting;
    }

    public function text(string $key): string
    {
        return (string) (($this->content ?? [])[$key] ?? static::defaults()[$key] ?? '');
    }

    public function backgroundUrl(string $type): string
    {
        $column = $type.'_background_path';
        $path = $this->{$column};

        if ($path && Storage::disk('public')->exists($path)) {
            return route('home-background.show', $type);
        }

        return $type === 'hero'
            ? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=85'
            : '';
    }
}
