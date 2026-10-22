Dump database produksi e-maintenance.dpplast.com
File: production-e-maintenance.sql

Import di aaPanel (MySQL):
1. Buat database dan user di aaPanel.
2. Import file SQL ini ke database tersebut.
3. Copy .env.example menjadi .env, isi APP_KEY, APP_URL, dan kredensial database aaPanel.
4. Jalankan:
   composer install --no-dev --optimize-autoloader
   php artisan key:generate   (jika APP_KEY masih kosong)
   php artisan storage:link
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

Document root website: folder public/
Jangan unggah file .env ke GitHub.
