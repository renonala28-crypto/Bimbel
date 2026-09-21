# 🗂️ Project Directory Structure - Quick Reference

```
d:\Bimbel\                          ← Project Root
│
├── 📄 Core Configuration Files
│   ├── composer.json              ← PHP dependencies
│   ├── .env.example               ← Environment template
│   ├── .gitignore                 ← Git ignore rules
│   │
│   └── 📚 Documentation
│       ├── README.md              ← Project overview
│       ├── SETUP.md               ← Installation guide
│       ├── SUMMARY.md             ← Project summary
│       ├── product.md             ← Product requirements (USER)
│       └── desaign.md             ← Design specification (USER)
│
├── public/                        ← Web Root (Akses URL)
│   ├── index.php                  ← MAIN ENTRY POINT ⭐
│   │
│   └── assets/
│       ├── css/
│       │   └── style.css          ← Main stylesheet
│       ├── js/
│       │   └── main.js            ← Common JavaScript
│       ├── images/                ← Image assets folder
│       └── uploads/               ← User uploads (payments, files)
│
├── src/                           ← Application Logic
│   ├── Auth.php                   ← Authentication class
│   │   ├── register()
│   │   ├── login()
│   │   ├── check()
│   │   ├── user()
│   │   └── isAdmin()
│   │
│   ├── Api.php                    ← API Endpoints
│   │   ├── handleLogin()
│   │   ├── handleRegister()
│   │   ├── handleExamSubmit()
│   │   └── handleSudokuSave()
│   │
│   └── Models/                    ← (To be created)
│       ├── User.php
│       ├── Exam.php
│       └── Material.php
│
├── config/                        ← Configuration Files
│   └── database.php               ← Database connection
│       ├── PDO setup
│       ├── Constants definition
│       └── getDBConnection()
│
├── database/                      ← Database Management
│   └── migrate.php                ← SQL migration script
│       ├── users table
│       ├── payments table
│       ├── questions table
│       ├── exam_results table
│       ├── materials table
│       ├── sudoku_logs table
│       ├── attendance table
│       └── physical_performance table
│
├── views/                         ← HTML Templates
│   │
│   ├── auth/                      ← Authentication Pages
│   │   ├── login.php              ← Login form
│   │   ├── register.php           ← Registration + QRIS + upload
│   │   └── pending.php            ← Pending verification status
│   │
│   ├── siswa/                     ← Student Pages
│   │   ├── dashboard.php          ← Student dashboard
│   │   ├── exam.php               ← CBT Exam page
│   │   ├── sudoku.php             ← Sudoku game page
│   │   ├── materi.php             ← Learning materials
│   │   ├── perkembangan.php       ← Progress dashboard
│   │   └── leaderboard.php        ← Rankings
│   │
│   ├── admin/                     ← Admin Pages
│   │   ├── dashboard.php          ← Admin dashboard
│   │   ├── verifikasi.php         ← (To be created)
│   │   ├── soal.php               ← (To be created)
│   │   ├── materi.php             ← (To be created)
│   │   ├── absensi.php            ← (To be created)
│   │   └── nilai-fisik.php        ← (To be created)
│   │
│   ├── layouts/                   ← Reusable Layouts
│   │   └── dashboard.php          ← Dashboard base template
│   │
│   └── error/                     ← Error Pages
│       ├── 403.php                ← Forbidden
│       └── 404.php                ← Not found
│
└── uploads/                       ← (To be created)
    ├── payments/                  ← Payment proof files
    └── materials/                 ← Material documents
```

---

## 📋 URL Routes & Mapping

```
GET /                          → Redirect to /login or /dashboard
GET /login                     → views/auth/login.php
GET /register                  → views/auth/register.php
POST /api/login                → src/Api.php::handleLogin()
POST /api/register             → src/Api.php::handleRegister()
GET /logout                    → Destroy session
GET /pending                   → views/auth/pending.php

GET /dashboard                 → views/siswa/dashboard.php (if siswa)
                                  or views/admin/dashboard.php (if admin)
GET /exam                      → views/siswa/exam.php
GET /sudoku                    → views/siswa/sudoku.php
GET /materi                    → views/siswa/materi.php
GET /perkembangan              → views/siswa/perkembangan.php
GET /leaderboard               → views/siswa/leaderboard.php

GET /admin/verifikasi          → views/admin/verifikasi.php
GET /admin/soal                → views/admin/soal.php
GET /admin/materi              → views/admin/materi.php
GET /admin/absensi             → views/admin/absensi.php
GET /admin/nilai-fisik         → views/admin/nilai-fisik.php

POST /api/exam/submit          → src/Api.php::handleExamSubmit()
POST /api/sudoku/save          → src/Api.php::handleSudokuSave()

(default)                      → views/error/404.php
```

---

## 🔑 Key Files to Know

### Entry Point
- **`public/index.php`** - Main router, DO NOT DELETE

### Database Connection
- **`config/database.php`** - Import this untuk koneksi database

### Authentication
- **`src/Auth.php`** - Gunakan class ini untuk login/register logic

### Database Setup
- **`database/migrate.php`** - Run: `php database/migrate.php`

### Main CSS
- **`assets/css/style.css`** - All styling, modify warna di sini

### Main JavaScript
- **`assets/js/main.js`** - Common functions, tambah utility di sini

---

## 🚀 Cara Menjalankan

### 1. Install PHP & MySQL
```bash
# Windows: Use XAMPP, WAMP, or Laragon
# macOS: Use Homebrew
# Linux: Use apt-get or package manager
```

### 2. Copy .env
```bash
cp .env.example .env
# Edit .env dengan database credentials
```

### 3. Run Migration
```bash
php database/migrate.php
```

### 4. Start Server
```bash
php -S localhost:8000 -t public
```

### 5. Open Browser
```
http://localhost:8000
```

---

## 📝 Development Workflow

### To Add a New Page:
1. Create file di `views/siswa/` atau `views/admin/`
2. Tambah route di `public/index.php`
3. Link dari menu/button yang ada

### To Add a New API Endpoint:
1. Tambah function di `src/Api.php`
2. Add route di `public/index.php`
3. Test dengan Postman atau browser

### To Add New Database Table:
1. Tambah SQL di `database/migrate.php`
2. Run migration: `php database/migrate.php`
3. Create corresponding Model class di `src/Models/`

### To Style New Elements:
1. Add CSS classes ke element di HTML
2. Define styles di `assets/css/style.css`
3. Use color variables yang sudah defined

---

## 🎯 Color Reference

Use in CSS/HTML:
```css
var(--primary)      /* #0B2545 - Deep Navy Blue */
var(--secondary)    /* #134074 - Dark Slate Blue */
var(--muted)        /* #8DA9C4 - Steel Grey */
var(--bg-light)     /* #EEF4F8 - Light Ice Blue */
var(--surface)      /* #FDFDFD - Off-White */
var(--success)      /* #28a745 - Green */
var(--warning)      /* #ffc107 - Yellow */
var(--danger)       /* #dc3545 - Red */
```

---

## 📚 Important Classes

### Auth.php
```php
$auth = new Auth();
$auth->register($name, $whatsapp, $email, $password, $proof_file);
$auth->login($email, $password);
Auth::check();           // Check if logged in
Auth::user();            // Get current user data
Auth::isAdmin();         // Check if user is admin
```

### Database Connection
```php
require_once __DIR__ . '/../config/database.php';
$pdo = getDBConnection();
```

---

## 🐛 Debugging Tips

1. **Check database:**
   ```bash
   # Use MySQL Workbench or PhpMyAdmin
   SELECT * FROM users;
   ```

2. **Check PHP errors:**
   - Browser console (F12)
   - PHP error log
   - var_dump() output

3. **Test route:**
   - Type URL directly di browser
   - Check public/index.php route matching

4. **Debug session:**
   ```php
   echo "<pre>";
   var_dump($_SESSION);
   echo "</pre>";
   ```

---

**Everything is ready! Start coding! 🚀**
