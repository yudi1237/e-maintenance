# e-Maintenance — PT Dharma Poliplast

Aplikasi pencatatan problem mesin injection berbasis **Laravel 12** dengan login, form laporan teknisi, dan dashboard admin 60 mesin.

## Fitur

- **Login wajib** sebelum mengisi form (registrasi publik dinonaktifkan; admin menambah user)
- **Form laporan** dengan field sesuai kebutuhan operasional
- **Tanggal otomatis** timezone Asia/Jakarta (WIB)
- **Nomor mesin** dropdown Mesin 1–60
- **Nama mesin** & **kategori kerusakan** dari master data (dikelola admin)
- **Kategori Other** dengan input manual
- **PIC** otomatis dari user yang login
- **Shift kerja** terdeteksi otomatis + bisa diubah manual
- **Dashboard admin** grid 60 mesin injection, klik untuk riwayat per mesin
- **Update real-time** statistik dashboard (polling 5 detik)
- **Export laporan Excel (.xlsx)** per periode (opsional filter mesin)
- **Kelola pengguna**, master nama mesin, master kategori

## Akun Demo (setelah `db:seed`)

| Peran    | NPK      | Password  |
|----------|----------|-----------|
| Admin    | 100001   | password  |
| Teknisi  | 200001   | password  |

## Cara Menjalankan

```bash
cd "c:\Users\Muhammad Prayudi\Documents\e-Maintenance"
composer install
npm install
npm run build
php artisan migrate
php artisan db:seed
php artisan serve --port=8001
```

Buka browser: **http://127.0.0.1:8001**

> Jika port 8000 sudah dipakai proyek lain (mis. iRoom DPP), gunakan port 8001 seperti di atas. Untuk memakai port 8000, hentikan dulu server proyek lain yang berjalan di port tersebut.

## Struktur Akses

- **Tanpa login** → semua URL diarahkan ke halaman login
- **Teknisi** → hanya `/laporan/baru` dan `/laporan/{id}/form-2` (termasuk submit form)
- **Admin** → dashboard `/admin/dashboard`, master data, kelola user, export report
- **Sesi idle** → logout otomatis setelah 5 menit tanpa aktivitas (server + browser)

## Database

Default menggunakan **SQLite** (`database/database.sqlite`). Untuk MySQL, ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_maintenance
DB_USERNAME=root
DB_PASSWORD=
```

## Teknologi

- Laravel 12 + Breeze (Blade)
- Tailwind CSS
- SQLite / MySQL
