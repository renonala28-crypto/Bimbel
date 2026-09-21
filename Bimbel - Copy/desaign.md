# Software Architecture & Design Specification (DESAIGN.md)

## 1. Panduan Desain Antarmuka (UI/UX)
Sistem menggunakan tema visual yang tegas, formal, dan berdisiplin tinggi dengan penerapan palet warna berikut:
*   **Primary Color:** Deep Navy Blue (`#0B2545`) - Digunakan pada Top Navbar, Sidebar Admin, dan Tombol Utama (CTA).
*   **Secondary Color:** Dark Slate Blue (`#134074`) - Digunakan pada sub-menu, status aktif, dan grafik.
*   **Muted Accent:** Muted Slate/Steel Grey (`#8DA9C4`) - Digunakan untuk border tabel, teks sekunder, dan garis pandu grid Sudoku.
*   **Background (Light):** Light Ice Blue (`#EEF4F8`) - Digunakan sebagai warna dasar latar belakang dashboard dan lembar ujian agar teks mudah dibaca.
*   **Surface/Card Color:** Off-White/Sangat Terang (`#FDFDFD`) - Latar belakang box materi, card score, dan container login.

---

## 2. Arsitektur Data & Skema Database Utama (Logical Schema)

### 2.1 Tabel `users`
Menyimpan data akun internal untuk siswa dan manajemen admin.
*   `id` (INT, Primary Key, Auto Increment)
*   `name` (VARCHAR, 100)
*   `whatsapp` (VARCHAR, 20)
*   `email` (VARCHAR, 100, Unique)
*   `password` (VARCHAR, 255)
*   `role` (ENUM: 'admin', 'siswa')
*   `status` (ENUM: 'pending', 'active')
*   `created_at` (TIMESTAMP)

### 2.2 Tabel `payments`
Menyimpan riwayat berkas pendaftaran transaksi siswa.
*   `id` (INT, Primary Key)
*   `user_id` (INT, Foreign Key -> `users.id`)
*   `amount` (INT, Default: 100000)
*   `proof_file` (VARCHAR, 255) -- Lokasi penyimpanan path/file slip
*   `verified_at` (TIMESTAMP, Nullable)

### 2.3 Tabel `question_banks` & `exam_sessions`
Modul pengelolaan data bank soal ujian dan hasil tryout.
*   **Tabel `questions` (CRUD Admin):** `id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`.
*   **Tabel `exam_results`:** `id`, `user_id`, `score`, `duration_spent`, `completed_at`.

### 2.4 Tabel `materials`
Modul dokumen materi satu arah dengan pengelompokan folder.
*   `id` (INT, Primary Key)
*   `category` (VARCHAR, 50) -- Nilai: 'Psikologi', 'PWK', 'Lari', 'Renang', dll.
*   `title` (VARCHAR, 150)
*   `file_path` (VARCHAR, 255) -- Path file PDF/Word
*   `uploaded_at` (TIMESTAMP)

### 2.5 Tabel `sudoku_logs`
Mencatat riwayat latihan kecermatan permainan sudoku siswa.
*   `id` (INT, Primary Key)
*   `user_id` (INT, Foreign Key -> `users.id`)
*   `score` (INT)
*   `time_elapsed` (INT) -- Durasi dalam hitungan detik
*   `played_at` (TIMESTAMP)

### 2.6 Tabel `attendances`
Modul lembar absensi manual harian.
*   `id` (INT, Primary Key)
*   `user_id` (INT, Foreign Key -> `users.id`)
*   `status` (ENUM: 'Hadir', 'Absen', 'Izin')
*   `date` (DATE)
*   `marked_by` (INT, Foreign Key -> `users.id` Admin)

### 2.7 Tabel `physical_scores`
Modul pencatatan metrik capaian fisik siswa yang dilakukan manual oleh pengajar.
*   `id` (INT, Primary Key)
*   `user_id` (INT, Foreign Key -> `users.id`)
*   `running_distance_km` (FLOAT)
*   `running_time_minutes` (INT)
*   `swimming_distance_m` (INT)
*   `swimming_time_minutes` (INT)
*   `other_metrics_json` (TEXT, Nullable) -- Untuk ekpansi data push-up/sit-up
*   `tested_at` (DATE)

---

## 3. Desain Komponen Logika Teknis Game Sudoku
*   **Engine Generator:** Menggunakan pustaka Javascript ringan di sisi klien (Client-Side Grid Generator) untuk meminimalkan beban server saat merender papan angka hilang.
*   **Sinkronisasi Data:** Setelah tombol "Selesai" atau kondisi papan terisi penuh secara valid terpenuhi, skrip JavaScript memicu fungsi kirim data via API/Form POST ke backend untuk dimasukkan dalam tabel `sudoku_logs`.