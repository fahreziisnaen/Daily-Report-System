# 📝 Daily Report Application - Project Engineering Team

![Laravel](https://img.shields.io/badge/Laravel-12.0.1-red?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4.2-blue?style=flat&logo=php)
![Alpine.js](https://img.shields.io/badge/Alpine.js-%23000000.svg?style=flat&logo=alpine.js)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-%2338B2AC.svg?style=flat&logo=tailwind-css)
![MariaDB](https://img.shields.io/badge/MariaDB-%23003545.svg?style=flat&logo=mariadb)

## 📝 Deskripsi
Aplikasi web untuk manajemen laporan harian tim Project Engineering. Dibangun menggunakan Laravel, Alpine.js, dan Tailwind CSS, aplikasi ini memudahkan pencatatan, pelacakan, serta pengelolaan aktivitas dan user secara real-time.

## 🌟 Fitur Utama
- ✅ Pembuatan & pengelolaan laporan harian (CRUD)
- 📊 Dashboard statistik laporan & rekap
- ⏰ Reminder harian untuk pengisian laporan
- 👥 Manajemen pengguna berbasis role: Super Admin & Employee
- 🔒 Reset password, aktivasi/non-aktivasi, dan penghapusan user
- 📱 Interface responsif & mobile-friendly (dengan bottom navigation)
- 🖼️ Upload & preview avatar serta tanda tangan digital (signature)
- 🔍 Fitur filter & pencarian laporan (nama, tanggal, lokasi, project)
- 📥 Export laporan lembur (overtime) ke Excel
- 🛡️ Hak akses granular (Spatie Permission)

## 🛠️ Teknologi
- ![Laravel](https://img.shields.io/badge/Laravel-12.0.1-red?style=flat&logo=laravel) Laravel 12.x
- ![PHP](https://img.shields.io/badge/PHP-8.4.2-blue?style=flat&logo=php) PHP 8.4.x
- ![Alpine.js](https://img.shields.io/badge/Alpine.js-%23000000.svg?style=flat&logo=alpine.js) Alpine.js 3.x
- ![TailwindCSS](https://img.shields.io/badge/TailwindCSS-%2338B2AC.svg?style=flat&logo=tailwind-css) Tailwind CSS 3.x
- ![MariaDB](https://img.shields.io/badge/MariaDB-%23003545.svg?style=flat&logo=mariadb) MariaDB/MySQL
- Spatie Laravel Permission
- Vite (asset build)
- Storage public link (untuk avatar & signature)

## 💻 Persyaratan Sistem
- ✅ PHP >= 8.4.2
- ✅ Composer
- ✅ Node.js & NPM
- ✅ Database MariaDB/MySQL
- ✅ Web Server (Apache/Nginx)

## 🚀 Instalasi
```bash
# 1. Clone repository
git clone https://github.com/fahreziisnaen/Daily-Report-System.git

# 2. Masuk ke direktori proyek
cd Daily-Report-System

# 3. Install dependensi PHP dengan Composer
composer install

# 4. Install dependensi JavaScript dengan NPM
npm install

# 5. Salin file konfigurasi .env
cp .env.example .env

# 6. Sesuaikan konfigurasi di file .env

# 7. Generate application key
php artisan key:generate

# 8. Jalankan migrasi database
php artisan migrate

# 9. Jalankan seeder untuk data awal (roles, admin, dsb)
php artisan db:seed

# 10. Buat storage link untuk upload avatar & signature
php artisan storage:link

# 11. Compile asset dengan Vite
npm run build

# 12. Jalankan development server
php artisan serve
```

## 📋 Penggunaan
- 🔐 Login menggunakan kredensial yang diberikan
- 📝 Buat laporan harian dengan mengisi detail pekerjaan
- 📈 Pantau progress di dashboard & rekap
- 👥 Admin/Super Admin dapat mengelola pengguna, reset password, aktivasi/non-aktivasi, dan melihat semua laporan
- 👤 Employee hanya dapat mengelola laporan mereka sendiri
- 🖼️ Upload dan preview avatar serta tanda tangan digital di halaman profile
- 📱 Nikmati tampilan mobile-friendly dengan bottom navigation

## 🤝 Kontribusi
Proyek ini dikembangkan oleh tim Project Engineering PT. Internet Pratama Indonesia.

## 📄 Lisensi
⚖️ MIT License

## 👨‍💻 Developed by
Fahrezi Isnaen Fauzan

## 📞 Kontak
Untuk informasi lebih lanjut, silakan hubungi tim pengembang di [fahrezifauzan.vercel.app](https://fahrezifauzan.vercel.app)

