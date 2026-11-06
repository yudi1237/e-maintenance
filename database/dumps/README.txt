Dump database e-maintenance.dpplast.com
File: production-e-maintenance.sql

Koneksi MySQL (username dan password wajib diisi, tidak boleh kosong):
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_maintenance
DB_USERNAME=e_maintenance
DB_PASSWORD=Ld-0NLelw098tlG-ITLGNkhi

Import:
1. Buat database e_maintenance dan user e_maintenance dengan password di atas.
2. Import file production-e-maintenance.sql ke database tersebut.
3. Copy .env.example menjadi .env. Username dan password database sudah terisi.
4. Jalankan:
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

Document root website: folder public/
