<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Bimbel Portal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="logo">BI</div>
                <div>
                    <h2>Bimbel Alahaido</h2>
                    <div class="sidebar-subtitle">Portal Siswa</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="/dashboard" class="nav-item active">Dashboard</a>
                <a href="/exam" class="nav-item">Simulasi CAT</a>
                <a href="/materi" class="nav-item">Materi Belajar</a>
                <a href="/perkembangan" class="nav-item">Perkembangan</a>
                <a href="/kalkulator-samapta" class="nav-item">Kalkulator Samapta</a>
                <a href="/leaderboard" class="nav-item">Leaderboard</a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p class="user-name"><?php echo $_SESSION['user_name'] ?? 'Siswa'; ?></p>
                    <p class="user-role">Siswa Aktif</p>
                </div>
                <a href="/logout" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user_nickname'] ?? 'Siswa'); ?> 👋</h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

            <div class="content-area">
                <div class="card">
                    <span class="page-kicker">Overview</span>
                    <h2 style="margin:0 0 6px;">Dashboard Pembelajaran</h2>
                    <p style="margin:0; color:#6b7280;">Pantau progres belajar, mulai latihan, dan lihat rangking Anda di satu dashboard yang lebih rapi.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>Ujian Selesai</h3>
                        <div class="big">0</div>
                    </div>
                    <div class="stat-box">
                        <h3>Materi</h3>
                        <div class="big" style="color:#f59e0b;">0</div>
                    </div>
                    <div class="stat-box">
                        <h3>Nilai Terbaik</h3>
                        <div class="big" style="color:#ef4444;">-</div>
                    </div>
                </div>

                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:12px; flex-wrap:wrap;">
                        <h2 style="margin:0;">Fitur Utama</h2>
                        <span class="badge badge-success">Aktif</span>
                    </div>

                    <div class="module-grid">
                        <a class="module-card" href="/exam">
                            <div class="module-icon">📝</div>
                            <h3>Simulasi CAT</h3>
                            <p>Ikuti ujian dengan timer, scoring otomatis, dan pembelajaran yang lebih terarah.</p>
                            <span class="btn btn-primary">Mulai Ujian</span>
                        </a>
                        
                        <a class="module-card" href="/materi">
                            <div class="module-icon">📚</div>
                            <h3>Materi Belajar</h3>
                            <p>Akses materi pembelajaran, modul, dan dokumen pendukung dari satu tempat.</p>
                            <span class="btn btn-primary">Baca Materi</span>
                        </a>

                        <a class="module-card" href="/perkembangan">
                            <div class="module-icon">📈</div>
                            <h3>Perkembangan</h3>
                            <p>Lihat progres belajar Anda dan evaluasi capaian dari waktu ke waktu.</p>
                            <span class="btn btn-primary">Lihat Grafik</span>
                        </a>

                        <a class="module-card" href="/kalkulator-samapta">
                            <div class="module-icon">⏱️</div>
                            <h3>Kalkulator Samapta</h3>
                            <p>Ukur kesiapan fisik dan hitung persentase capaian latihan jasmani Polri/TNI.</p>
                            <span class="btn btn-primary">Hitung Capaian</span>
                        </a>

                        <a class="module-card" href="/leaderboard">
                            <div class="module-icon">🏆</div>
                            <h3>Leaderboard</h3>
                            <p>Bandingkan hasil belajar dengan teman sebaya dan tingkatkan motivasi.</p>
                            <span class="btn btn-primary">Lihat Peringkat</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Informasi Penting</h2>
                    </div>
                    <ul style="margin-left: 20px; color: var(--text-light); line-height:1.9;">
                        <li>Ikuti semua modul dengan konsisten agar progres belajar semakin maksimal.</li>
                        <li>Ujian CBT bisa diulang untuk melatih kemampuan dan kecepatan.</li>
                        <li>Gunakan halaman leaderboard sebagai motivasi untuk terus berkembang.</li>
                        <li>Hubungi admin jika ada kendala saat mengakses materi atau ujian.</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
