##
- PHP 8.4.3
- Composer
- Xampp

## Cara Install
- Clone repository ini
- Tunggu sampai proses clone selesai
- Buka folder porject yang sudah di clone melalui terminal
- Lakukan composer install ketik
```terminal
composer install
```
- Tunggu sampai proses selesai
- Buat database baru di phpmyadmin anda beri nama sesuka hati anda
- Copy file .env.example yang ada di dalam folder project dan ubah namanya menjadi .env
bagi yang menggunakan git bash bisa ketik seperti dibawah
```terminal
cp .env.example .env
```
- Lakukan generate key ketik 
```terminal
php artisan key:generate
```
- Buka file .env
- Ubah konfigurasi database sesuai nama database yang anda buat tadi lalu simpan
- Ubah konfigurasi API_KEY sesuai <a href="kalenderindonesia.com">kalenderindonesia.com</a>
- lakukan migrate ketik :
```terminal
php artisan migrate:fresh
```
- kemudian lakukan seeding ketik :
```terminal
php artisan db:seed
```
- Finish project laravel bisa dijalankan dengan menggunakan development server dengan cara ketik
```terminal
php artisan serve
```
- Lalu ctrl+klik pada php artisan serve --host=192.168.100.52 --port=8000
- Pastikan host sesuai dengan ip komputer yang digunakan sebagai server.