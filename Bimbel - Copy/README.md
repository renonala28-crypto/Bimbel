# Bimbel Portal - PHP Application

Portal pembelajaran berbasis web untuk siswa calon TNI/Polri dengan fitur CBT, Sudoku, dan materi belajar.

## Persyaratan
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Composer (untuk dependency management)

## Setup Awal

### 1. Install Dependencies
```bash
composer install
```

### 2. Konfigurasi Database
Buat file `.env` di root project:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=password
DB_NAME=bimbel_portal
```

### 3. Jalankan Migration Database
```bash
php database/migrate.php
```

### 4. Jalankan Server Lokal
```bash
php -S localhost:8000 -t public
```
Akses di: http://localhost:8000

## Struktur Folder
- `public/` - File entry point & assets statis
- `src/` - Core aplikasi (Controllers, Models, Helpers)
- `config/` - Konfigurasi database & aplikasi
- `database/` - SQL migrations & seeders
- `views/` - Template HTML
- `assets/` - CSS, JS, gambar

## Fitur Utama
1. **Autentikasi** - Login/Registrasi siswa & admin
2. **CBT Exam** - Ujian pilihan ganda dengan timer
3. **Sudoku Game** - Latihan kecermatan
4. **Materi Belajar** - Upload & download dokumen
5. **Dashboard Admin** - Manajemen user, soal, materi
6. **Papan Peringkat** - Leaderboard nilai siswa
