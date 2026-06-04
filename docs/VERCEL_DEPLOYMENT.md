# Deploy Laravel Villa-Sina ke Vercel Gratis

Project ini sudah disiapkan untuk Vercel dengan:

- `api/index.php` sebagai entrypoint Laravel serverless.
- `vercel.json` untuk runtime PHP dan routing static asset.
- `.vercelignore` supaya folder lokal besar seperti `vendor` dan `node_modules` tidak ikut upload.
- script Composer `vercel` untuk install/build asset Vite dan cache Laravel saat build.

## 1. Push ke GitHub

Pastikan file baru sudah masuk repository, lalu push ke GitHub.

## 2. Import di Vercel

1. Buka Vercel.
2. Add New Project.
3. Import repository ini.
4. Framework Preset boleh pilih `Other`.
5. Deploy.

Vercel akan memakai `vercel.json` dan runtime `vercel-php@0.9.0`.

## 3. Environment Variables

Tambahkan environment variable berikut di Vercel Project Settings.

```env
APP_NAME=Villa-Sina
APP_ENV=production
APP_KEY=base64:ISI_DARI_php_artisan_key_generate_show
APP_DEBUG=false
APP_URL=https://domain-vercel-kamu.vercel.app

LOG_CHANNEL=stderr
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Untuk membuat `APP_KEY` lokal:

```bash
php artisan key:generate --show
```

## 4. Database Gratis

Jangan pakai SQLite lokal untuk production di Vercel karena storage serverless tidak persisten. Pilihan gratis yang cocok:

- Neon PostgreSQL
- Supabase PostgreSQL

Setelah database dibuat, isi env `DB_*` di Vercel, lalu jalankan migrasi dari komputer lokal ke database tersebut:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Pastikan `.env` lokal sementara diarahkan ke database production sebelum menjalankan perintah di atas.

## 5. Catatan File Upload

Vercel tidak cocok untuk penyimpanan file upload permanen di disk lokal. Fitur upload bukti pembayaran dan gambar villa akan lebih stabil jika `FILESYSTEM_DISK=s3` dan memakai storage eksternal seperti S3-compatible storage.

Untuk demo gratis sederhana, deploy tetap bisa jalan, tetapi file upload yang disimpan ke storage lokal tidak boleh dianggap permanen.
