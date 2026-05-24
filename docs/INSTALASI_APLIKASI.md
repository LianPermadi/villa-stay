# Panduan Instalasi Aplikasi Villa-Sina

Dokumen ini menjelaskan cara instalasi aplikasi Villa-Sina dari awal sampai aplikasi bisa berjalan di browser.

## 1. Kebutuhan Sistem

Pastikan perangkat sudah memiliki:

| Kebutuhan | Versi/Informasi |
| --- | --- |
| PHP | Minimal PHP 8.2 |
| Composer | Untuk instalasi dependency Laravel |
| Node.js dan npm | Untuk menjalankan Vite/Tailwind |
| Database | SQLite atau MySQL |
| Web browser | Chrome, Edge, Firefox, atau browser lain |

Jika menggunakan Windows, aplikasi ini dapat dijalankan dengan XAMPP selama versi PHP sudah sesuai.

## 2. Ambil Source Code Aplikasi

Jika source code berasal dari repository Git:

```bash
git clone <url-repository>
cd villa-sina
```

Jika source code diberikan dalam bentuk file ZIP:

1. Extract file ZIP.
2. Masuk ke folder project melalui terminal.

Contoh:

```bash
cd D:\Aplikasi\joki\villa-stay
```

## 3. Install Dependency PHP

Jalankan perintah:

```bash
composer install
```

Perintah ini akan membuat folder `vendor` dan mengunduh dependency Laravel.

## 4. Install Dependency Frontend

Jalankan perintah:

```bash
npm install
```

Perintah ini akan membuat folder `node_modules`.

## 5. Buat File Environment

Salin file `.env.example` menjadi `.env`.

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

## 6. Atur Konfigurasi `.env`

Buka file `.env`, lalu pastikan nama aplikasi sudah benar:

```env
APP_NAME="Villa-Sina"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## 7. Generate Application Key

Jalankan:

```bash
php artisan key:generate
```

Perintah ini akan mengisi nilai `APP_KEY` pada file `.env`.

## 8. Konfigurasi Database

### Opsi A: Menggunakan SQLite

Ini opsi paling sederhana untuk menjalankan aplikasi secara lokal.

Pastikan `.env` berisi:

```env
DB_CONNECTION=sqlite
```

Buat file database:

Windows PowerShell:

```powershell
New-Item -ItemType File database/database.sqlite -Force
```

Linux/macOS:

```bash
touch database/database.sqlite
```

### Opsi B: Menggunakan MySQL

Buat database baru, misalnya:

```sql
CREATE DATABASE villa_sina;
```

Lalu ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=villa_sina
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL di perangkat.

## 9. Jalankan Migrasi Database

Jalankan:

```bash
php artisan migrate
```

Perintah ini akan membuat tabel seperti `users`, `villas`, `bookings`, `payments`, `revenues`, dan tabel lain yang dibutuhkan aplikasi.

## 10. Isi Data Awal

Jalankan seeder:

```bash
php artisan db:seed
```

Seeder akan membuat data awal seperti akun admin, akun user, data villa, gambar villa, booking contoh, dan data pendapatan.

Jika ingin menjalankan migrasi dari awal sekaligus seed:

```bash
php artisan migrate:fresh --seed
```

Catatan: perintah `migrate:fresh --seed` akan menghapus semua data lama di database.

## 11. Buat Storage Link

Aplikasi menyimpan gambar villa, bukti pembayaran, dan bukti pengembalian dana di storage Laravel.

Jalankan:

```bash
php artisan storage:link
```

Jika link sudah pernah dibuat, Laravel akan memberi informasi bahwa link sudah tersedia.

## 12. Jalankan Build Frontend

Untuk mode production/build:

```bash
npm run build
```

Untuk mode development:

```bash
npm run dev
```

Pada development, biarkan terminal `npm run dev` tetap berjalan.

## 13. Jalankan Server Laravel

Buka terminal baru, lalu jalankan:

```bash
php artisan serve
```

Secara default aplikasi akan berjalan di:

```text
http://127.0.0.1:8000
```

Buka URL tersebut di browser.

## 14. Akun Login Default

Setelah menjalankan seeder, gunakan akun berikut:

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@villa-sina.com | password |
| User | john@example.com | password |

Jika sebelumnya ada user lain di database lokal, semua password dapat disamakan menjadi `password` dengan perintah khusus melalui Laravel/PHP, tetapi untuk instalasi baru cukup gunakan data dari seeder.

## 15. Alur Pengecekan Aplikasi

Setelah aplikasi berjalan, lakukan pengecekan berikut:

1. Buka halaman beranda.
2. Buka daftar villa.
3. Login sebagai user.
4. Buat booking villa.
5. Upload bukti pembayaran.
6. Login sebagai admin.
7. Buka dashboard admin.
8. Buka detail booking.
9. Verifikasi atau tolak pembayaran.
10. Jika tolak pembayaran dengan refund, proses refund dan upload bukti pengembalian.
11. Login kembali sebagai user dan cek status booking.

## 16. Perintah Ringkas Instalasi

Untuk instalasi cepat dengan SQLite:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Untuk Windows PowerShell:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File database/database.sqlite -Force
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

## 17. Troubleshooting

### Composer tidak dikenali

Pastikan Composer sudah terinstall dan sudah masuk ke PATH.

Cek:

```bash
composer --version
```

### PHP tidak dikenali

Pastikan PHP sudah terinstall dan masuk ke PATH.

Cek:

```bash
php -v
```

### Error `APP_KEY` kosong

Jalankan:

```bash
php artisan key:generate
```

### Error database SQLite tidak ditemukan

Pastikan file berikut ada:

```text
database/database.sqlite
```

Jika belum ada, buat file tersebut lalu ulangi migrasi.

### Gambar atau bukti pembayaran tidak muncul

Jalankan:

```bash
php artisan storage:link
```

Pastikan juga file upload tersimpan di `storage/app/public`.

### Perubahan `.env` tidak terbaca

Bersihkan cache config:

```bash
php artisan config:clear
```

### Tampilan Blade tidak berubah

Bersihkan compiled view:

```bash
php artisan view:clear
```

### Port 8000 sudah digunakan

Jalankan server pada port lain:

```bash
php artisan serve --port=8080
```

Lalu buka:

```text
http://127.0.0.1:8080
```

## 18. Perintah Verifikasi

Untuk memastikan aplikasi tidak error secara dasar:

```bash
php artisan test
```

Jika berhasil, akan muncul informasi bahwa semua test passed.

## 19. Struktur Folder Penting

| Folder/File | Fungsi |
| --- | --- |
| `app/Http/Controllers` | Controller aplikasi frontend, admin, auth, dan file publik. |
| `app/Models` | Model database seperti User, Villa, Booking, Payment, Revenue. |
| `database/migrations` | Struktur tabel database. |
| `database/seeders` | Data awal aplikasi. |
| `resources/views` | Tampilan Blade frontend dan admin. |
| `routes/web.php` | Route utama aplikasi. |
| `routes/auth.php` | Route login, register, dan logout. |
| `public` | Entry point aplikasi dan asset publik. |
| `storage/app/public` | Lokasi file upload. |
| `docs` | Dokumentasi use case, ERD, dan instalasi. |

