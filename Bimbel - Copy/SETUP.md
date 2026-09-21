# 🚀 SETUP BIMBEL PORTAL PHP

Panduan lengkap untuk setup dan menjalankan Bimbel Portal berbasis PHP.

## ✅ Persyaratan Sistem

- **PHP** 7.4 atau lebih tinggi (rekomendasi: PHP 8.0+)
- **MySQL/MariaDB** 5.7+
- **Composer** (untuk dependency management)
- **Git** (opsional, untuk version control)
- **Text Editor/IDE** seperti VS Code, PhpStorm, atau Sublime Text

---

## 📦 Step 1: Install Dependencies

Pastikan Anda sudah di root folder project `d:\Bimbel`, kemudian jalankan:

```bash
composer install
```

Ini akan membuat folder `/vendor/` dan file `composer.lock`.

---

## 🗄️ Step 2: Setup Database

### 2.1 Buat Database

Buka MySQL/MariaDB dan buat database:

```sql
CREATE DATABASE bimbel_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2.2 Konfigurasi Environment

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Atau buat file `.env` baru dengan isi:

```
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=
DB_NAME=bimbel_portal
APP_ENV=development
APP_URL=http://localhost:8000
```

Sesuaikan nilai-nilai di atas dengan konfigurasi MySQL Anda.

### 2.3 Jalankan Migration

```bash
php database/migrate.php
```

Output yang sukses:
```
✓ Migration executed successfully
✓ Migration executed successfully
✓ Migration executed successfully
...
✓✓✓ All tables created successfully! ✓✓✓
```

---

## 🌐 Step 3: Jalankan Server Lokal

Gunakan built-in PHP server:

```bash
php -S localhost:8000 -t public
```

**Output:**
```
Development Server started at http://localhost:8000
Listening on http://localhost:8000
Document root is /d/Bimbel/public
```

---

## 🔐 Step 4: Akses Aplikasi

Buka browser Anda dan pergi ke: **http://localhost:8000**

### Default Flow:

1. **Halaman Awal** → Redirect ke `/login`
2. **Login** → Jika belum punya akun, klik "Daftar Sekarang"
3. **Register** → Isi form dan upload bukti pembayaran QRIS
4. **Pending** → Tunggu admin verifikasi pembayaran
5. **Dashboard** → Akses setelah akun diaktifkan admin

---

## 👤 Membuat Admin Account (Manual)

Buka MySQL dan insert data admin langsung:

```sql
INSERT INTO users (name, whatsapp, email, password, role, status)
VALUES (
    'Admin Portal',
    '0812345678',
    'admin@bimbel.id',
    '$2y$10$YOUR_BCRYPT_HASH_HERE',
    'admin',
    'active'
);
```

Untuk generate BCRYPT hash, gunakan command:

```php
php -r "echo password_hash('password123', PASSWORD_BCRYPT);"
```

---

## 📁 Struktur Folder Project

```
d:\Bimbel\
├── public/              ← Entry point & assets
│   ├── index.php       ← Main router
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   └── images/
│   └── uploads/        ← Upload files
├── src/                 ← Core PHP classes
│   ├── Auth.php        ← Authentication
│   ├── Api.php         ← API endpoints
│   └── Models/         ← Database models
├── views/              ← Template HTML
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── pending.php
│   ├── siswa/
│   │   ├── dashboard.php
│   │   ├── exam.php
│   │   ├── sudoku.php
│   │   ├── materi.php
│   │   ├── perkembangan.php
│   │   └── leaderboard.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── verifikasi.php
│   │   ├── soal.php
│   │   ├── materi.php
│   │   ├── absensi.php
│   │   └── nilai-fisik.php
│   └── layouts/
│       └── dashboard.php
├── config/             ← Konfigurasi
│   └── database.php
├── database/           ← Database
│   └── migrate.php     ← Migration script
├── composer.json       ← Dependencies
├── .env.example        ← Example environment
├── .gitignore
└── README.md
```

---

## 🎯 Fitur yang Sudah Diimplementasi

✅ **Autentikasi & Registrasi**
- Login dengan email & password
- Register dengan verifikasi pembayaran QRIS
- Status akun (pending/active)
- Session management

✅ **Database Schema**
- Tabel users, payments, questions, exam_results
- Tabel materials, sudoku_logs, attendance, physical_performance

✅ **Layout & Styling**
- Responsive design dengan CSS custom
- Color scheme sesuai desain (Deep Navy Blue, Dark Slate Blue, dll)
- Dashboard template dengan sidebar navigation

✅ **Folder Structure**
- Terorganisir dengan baik untuk scalability
- Separation of concerns (views, controllers, models)

---

## 🔄 Development Workflow

### 1. Jalankan Server

```bash
php -S localhost:8000 -t public
```

### 2. Buat Views Baru

Buat file di `/views/siswa/` atau `/views/admin/` sesuai kebutuhan.

### 3. Buat Model/Class

Buat file di `/src/` untuk logika bisnis.

### 4. Update Routes

Edit file `public/index.php` untuk tambah route baru.

### 5. Test & Debug

Gunakan `var_dump()`, `print_r()`, atau debugging tools di browser DevTools.

---

## 🐛 Troubleshooting

### Error: "Database Connection Error"

**Solusi:**
- Pastikan MySQL sudah running
- Verifikasi username, password, dan nama database di `.env`
- Pastikan database `bimbel_portal` sudah dibuat

### Error: "Call to undefined function getDBConnection()"

**Solusi:**
- Pastikan file `config/database.php` sudah di-include di file yang bersangkutan
- Gunakan: `require_once __DIR__ . '/../config/database.php';`

### Aplikasi blank/error 404

**Solusi:**
- Pastikan server berjalan dengan command: `php -S localhost:8000 -t public`
- Cek URL di browser (harus `http://localhost:8000`, bukan `localhost:8000/public`)
- Clear browser cache (Ctrl+Shift+Delete)

### Upload file tidak berfungsi

**Solusi:**
- Pastikan folder `/uploads/` dapat ditulis oleh PHP
- Jalankan command: `mkdir -p public/uploads`
- Set permissions: `chmod 755 public/uploads`

---

## 📝 Next Steps - Fitur yang Perlu Dikembangkan

### Phase 1 (Core)
- [ ] Halaman CBT exam dengan timer countdown
- [ ] Halaman Sudoku game
- [ ] Halaman materi belajar dengan kategori
- [ ] Admin dashboard untuk verifikasi akun

### Phase 2 (Advanced)
- [ ] Sistem penilaian & scoring otomatis
- [ ] Dashboard grafik perkembangan dengan Chart.js
- [ ] Papan peringkat (leaderboard) dinamis
- [ ] Absensi & nilai fisik siswa

### Phase 3 (Polish)
- [ ] API dokumentasi (API Docs)
- [ ] Unit testing dengan PHPUnit
- [ ] Caching & optimization
- [ ] Admin analytics & reporting

---

## 🔒 Security Checklist

- ✅ Password hashing dengan BCRYPT
- ✅ Session management
- ✅ File upload validation
- [ ] CSRF token untuk form submissions
- [ ] SQL injection prevention (gunakan prepared statements)
- [ ] XSS protection (htmlspecialchars())
- [ ] Rate limiting untuk login attempts

---

## 📞 Support & Questions

Untuk pertanyaan atau issue, silakan:
1. Cek README.md dan documentation
2. Review error messages di browser console
3. Cek file logs (jika ada)
4. Baca komentar di code

---

**Happy Coding! 🎉**
