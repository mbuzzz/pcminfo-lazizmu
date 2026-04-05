<div align="center">

# 🕌 PCM Genteng & Lazismu Digital Portal

**Portal Digital Pimpinan Cabang Muhammadiyah Genteng & Lazismu**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-5-FDAE4B?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTEyIDJMMiAyMmgyMEwxMiAyeiIgZmlsbD0id2hpdGUiLz48L3N2Zz4=&logoColor=white)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

---

*Portal informasi publik & manajemen donasi digital untuk PCM Genteng, Kabupaten Banyuwangi.*
*Dibangun dengan teknologi modern untuk transparansi dan efisiensi organisasi.*

</div>

---

## ✨ Fitur Utama

<table>
<tr>
<td width="50%">

### 🏛️ Portal Publik
- 📰 Berita & Artikel dengan editor konten kaya
- 📅 Agenda & Kegiatan organisasi
- 🏢 Profil Amal Usaha & Lembaga
- 👤 Struktur Pimpinan & Ortom
- 🎨 Landing page modern dengan Bento Grid

</td>
<td width="50%">

### 💰 Lazismu Digital
- 🤲 Program Donasi dengan multi-campaign
- 💳 Verifikasi donasi oleh admin
- 📊 Dashboard statistik real-time
- 📈 Grafik tren donasi 6 bulan terakhir
- 📋 Laporan penyaluran dana

</td>
</tr>
<tr>
<td>

### ⚙️ Admin Panel (Filament v5)
- 🔐 Role & Permission management
- 📝 CRUD lengkap untuk semua modul
- 🖼️ Media management dengan upload & editor
- 🌐 Pengaturan situs dinamis
- 🎛️ Collapsible sidebar modern

</td>
<td>

### 🏗️ Arsitektur
- 🧱 Domain-Driven Design (DDD)
- 🔄 Service Layer pattern
- 📦 Spatie Media Library integration
- 🔑 Spatie Permission for RBAC
- ✅ PHPUnit test suite

</td>
</tr>
</table>

---

## 🛠️ Tech Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Framework** | Laravel | 12 |
| **Admin Panel** | Filament | 5 |
| **Reactive UI** | Livewire | 4 |
| **CSS Framework** | Tailwind CSS | 4 |
| **Language** | PHP | 8.2+ |
| **Database** | MySQL / MariaDB | 5.7+ / 10.3+ |
| **Runtime** | Node.js | 22+ |
| **Package Manager** | Composer / NPM | 2.x / 10.x |

---

## 🚀 Instalasi Lokal (Development)

### Prasyarat

- PHP 8.2+ dengan ekstensi: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `gd`/`imagick`
- Composer 2.x
- Node.js 18+ & NPM 9+
- MySQL 5.7+ atau MariaDB 10.3+

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/mbuzzz/pcminfo-lazizmu.git
cd pcminfo-lazizmu

# 2. Install PHP dependencies
composer install

# 3. Install & build frontend
npm install
npm run build

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pcm_portal
# DB_USERNAME=root
# DB_PASSWORD=secret

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Buat symbolic link storage
php artisan storage:link

# 8. Jalankan development server
composer run dev
# atau secara terpisah:
# php artisan serve
# npm run dev
```

Akses aplikasi di `http://localhost:8000` dan admin panel di `http://localhost:8000/admin`.

---

## 🌐 Deploy ke Shared Hosting (cPanel)

Panduan lengkap untuk deploy aplikasi Laravel 12 ini ke shared hosting biasa (cPanel).

### 📋 Prasyarat Hosting

- PHP 8.2+ (cek di cPanel → Select PHP Version)
- MySQL/MariaDB database
- Akses SSH (**direkomendasikan**) atau File Manager
- Composer tersedia via SSH, atau upload vendor secara manual

### 📂 Langkah 1: Struktur Folder

Pada shared hosting, `public_html` adalah webroot. Kita **tidak boleh** menaruh seluruh Laravel di `public_html` demi keamanan.

```
/home/username/
├── pcm-portal/              ← Seluruh kode Laravel (di LUAR public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── ...
│
└── public_html/              ← Webroot (isi dari folder public/)
    ├── index.php             ← Modified entry point
    ├── .htaccess
    ├── build/                ← Compiled frontend assets
    ├── storage/              ← Symlink ke ../pcm-portal/storage/app/public
    └── ...
```

### 📦 Langkah 2: Upload File

**Opsi A — Via SSH (Direkomendasikan)**
```bash
# SSH ke server
ssh username@pcmgenteng.or.id

# Clone langsung di server
cd /home/username
git clone https://github.com/mbuzzz/pcminfo-lazizmu.git pcm-portal
cd pcm-portal

# Install dependencies
composer install --no-dev --optimize-autoloader
```

**Opsi B — Via File Manager / FTP**
1. Di komputer lokal, jalankan:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. Compress seluruh project menjadi `.zip` (**kecuali** `node_modules/` dan `.git/`)
3. Upload ke `/home/username/` via File Manager
4. Extract, rename folder menjadi `pcm-portal`

### 📝 Langkah 3: Setup `public_html`

Salin **isi** folder `pcm-portal/public/` ke `public_html/`:

```bash
# Via SSH
cp -r /home/username/pcm-portal/public/* /home/username/public_html/
cp /home/username/pcm-portal/public/.htaccess /home/username/public_html/
```

### ✏️ Langkah 4: Edit `index.php`

Edit file `/home/username/public_html/index.php` — ubah path bootstrap agar menunjuk ke folder project yang benar:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek maintenance mode
if (file_exists($maintenance = __DIR__.'/../pcm-portal/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require __DIR__.'/../pcm-portal/vendor/autoload.php';

// Bootstrap
$app = require_once __DIR__.'/../pcm-portal/bootstrap/app.php';

// Handle request
$app->handleRequest(Request::capture());
```

### ⚙️ Langkah 5: Konfigurasi `.env`

```bash
cd /home/username/pcm-portal
cp .env.example .env
```

Edit `.env` dengan konfigurasi production:

```env
APP_NAME="PCM Genteng Portal"
APP_ENV=production
APP_KEY=              # akan di-generate di step berikutnya
APP_DEBUG=false
APP_URL=https://pcmgenteng.or.id

LOG_CHANNEL=single

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_pcmportal
DB_USERNAME=username_dbuser
DB_PASSWORD=your_db_password

FILESYSTEM_DISK=public

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 🔑 Langkah 6: Finalisasi Setup

```bash
cd /home/username/pcm-portal

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate --force

# Buat symlink storage (link public_html/storage → pcm-portal/storage/app/public)
# Jika php artisan storage:link tidak bekerja, buat manual:
ln -s /home/username/pcm-portal/storage/app/public /home/username/public_html/storage

# Optimasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components

# Buat User Admin Baru
php artisan make:filament-user

# Beri akses Super Admin ke user yang baru dibuat
php artisan tinker --execute="App\Models\User::latest()->first()->assignRole('super_admin');"

# Set permission folder
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # sesuaikan user
```

### 🔒 Langkah 7: `.htaccess` Production

Pastikan file `/home/username/public_html/.htaccess` berisi:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Block sensitive files
<FilesMatch "^\.env|\.git|composer\.(json|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 🔄 Update Aplikasi

Setiap kali ada update baru dari repository:

```bash
cd /home/username/pcm-portal

# Pull perubahan terbaru
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Jalankan migrasi baru (jika ada)
php artisan migrate --force

# Clear & rebuild cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
```

---

## 🐛 Troubleshooting

<details>
<summary><strong>500 Internal Server Error</strong></summary>

```bash
# Cek log error
tail -50 storage/logs/laravel.log

# Pastikan permission benar
chmod -R 775 storage bootstrap/cache

# Pastikan .env sudah benar
php artisan config:clear
```
</details>

<details>
<summary><strong>Assets / CSS tidak tampil</strong></summary>

```bash
# Pastikan build folder ada di public_html
cp -r public/build /home/username/public_html/build

# Cek APP_URL di .env sudah sesuai domain
```
</details>

<details>
<summary><strong>Gambar tidak tampil</strong></summary>

```bash
# Pastikan symlink storage sudah dibuat
ls -la /home/username/public_html/storage

# Jika belum ada, buat manual:
ln -s /home/username/pcm-portal/storage/app/public /home/username/public_html/storage
```
</details>

<details>
<summary><strong>php artisan storage:link gagal</strong></summary>

Pada beberapa shared hosting, symlink dibatasi. Solusi alternatif:

1. **Via cPanel File Manager**: Gunakan fitur symlink
2. **Via PHP script**: Buat file `create-symlink.php` di `public_html/`:
   ```php
   <?php
   symlink('/home/username/pcm-portal/storage/app/public', '/home/username/public_html/storage');
   echo 'Symlink created!';
   ```
   Akses via browser, lalu **hapus file ini** setelah selesai.
</details>

---

## 📜 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<div align="center">

**Dibuat dengan ❤️ untuk PCM Genteng, Kabupaten Banyuwangi**

*Powered by Laravel 12 · Filament v5 · Livewire v4 · Tailwind CSS v4*

</div>
