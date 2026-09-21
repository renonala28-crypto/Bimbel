# Product Requirement Document (PRD) - Web Portal Bimbel Casis

## 1. Ringkasan Produk
Portal web satu pintu khusus untuk manajemen pembelajaran, ujian CBT, latihan kecermatan, dan rekapitulasi nilai fisik siswa calon siswa (casis) TNI/Polri/Kedinasan. Sistem bersifat single-program (semua user mendapatkan akses materi dan ujian yang sama). Web ini berjalan tanpa landing page pemasaran; akses langsung dimulai dari halaman autentikasi.

## 2. Fitur & Ruang Lingkup (Scope of Work)

### 2.1 Modul Autentikasi & Registrasi
*   **Halaman Login:** Input Email dan Password. Terdapat link/tombol sekunder menuju halaman pendaftaran.
*   **Halaman Pendaftaran:** Form input data siswa (Nama, WhatsApp, Email, Password).
*   **Gerbang Pembayaran Manual:** 
    *   Sistem menampilkan QRIS Statis nilai tetap (Rp 100.000).
    *   Formulir wajib unggah berkas (JPG/PNG/WEBP, maks 2 MB) sebagai bukti transfer.
*   **Status Akun:** Default akun baru adalah `Pending`. User tidak dapat mengakses dashboard siswa sebelum status diubah menjadi `Active` oleh Admin.

### 2.2 Modul Siswa (User Dashboard)
*   **Tryout CBT (Computer Based Test):** Mengerjakan paket soal pilihan ganda dengan batasan waktu (timer countdown), pengacakan soal, dan penilaian otomatis saat waktu habis atau selesai klik kumpul.
*   **Game Sudoku:** Fitur latihan kecermatan angka hilang. Menyimpan skor performa dan durasi penyelesaian ke database.
*   **Materi Belajar:** Menu baca dan unduh dokumen (.pdf/.docx) tanpa media video. Dokumen dikelompokkan berdasarkan kategori (Psikologi, PWK, Lari, Renang, dll.).
*   **Perkembangan Saya:** Dashboard grafik garis/batang yang menampilkan tren nilai CBT, skor Sudoku, dan capaian latihan fisik dari waktu ke waktu.
*   **Papan Peringkat (Leaderboard):** Menampilkan tabel peringkat nilai global dari seluruh siswa aktif.

### 2.3 Modul Admin & Pengajar (Admin Dashboard)
*   **Verifikasi Akun:** Tabel daftar akun `Pending` untuk divalidasi berkas pembayarannya dan diubah statusnya menjadi `Active`.
*   **Manajemen CBT (CRUD):** Tambah, baca, ubah, dan hapus bank soal, kunci jawaban, dan batas waktu ujian.
*   **Manajemen Materi (CRUD):** Tambah, baca, ubah, dan hapus kategori materi beserta file dokumen pendukungnya.
*   **Modul Absensi Manual (CRUD):** Lembar presensi harian siswa. Admin mencentang kehadiran (Hadir/Absen/Izin) per siswa pada tanggal berjalan.
*   **Modul Nilai Fisik (CRUD):** Input data performa fisik siswa secara berkala (Kolom input: jarak lari (km), waktu lari (menit), jarak renang (m), waktu renang (menit), dsb.).

---

## 3. Matriks Hak Akses (User Roles & Permissions)

| Fitur / Modul | Siswa (Status Active) | Admin / Pengajar | Guest / Siswa Pending |
| :--- | :---: | :---: | :---: |
| Login & Registrasi | Ya | Ya | Ya |
| Unggah Bukti Bayar | Hanya saat daftar | Tidak | Hanya saat daftar |
| Akses Ruang Belajar (CBT, Sudoku, Materi) | Ya | Tidak | Tidak |
| Grafik Perkembangan & Leaderboard | Ya (Milik Sendiri) | Ya (Seluruh Siswa) | Tidak |
| Validasi Pembayaran Akun | Tidak | Ya | Tidak |
| CRUD Soal, Kategori & Dokumen Materi | Tidak | Ya | Tidak |
| CRUD Absensi & Nilai Fisik Siswa | Tidak | Ya | Tidak |