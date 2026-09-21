<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Bimbel Portal</title>
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
                <a href="/dashboard" class="nav-item">Dashboard</a>
                <a href="/exam" class="nav-item">Simulasi CAT</a>
                <a href="/materi" class="nav-item">Materi Belajar</a>
                <a href="/perkembangan" class="nav-item">Perkembangan</a>
                <a href="/kalkulator-samapta" class="nav-item">Kalkulator Samapta</a>
                <a href="/leaderboard" class="nav-item active">Leaderboard</a>
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
                    <h1>Leaderboard</h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

            <div class="content-area">
                <div class="card">
                    <span class="page-kicker">Ranking</span>
                    <h2 style="margin:0 0 6px;">Papan Peringkat Siswa</h2>
                    <p style="margin:0; color:#6b7280;">Pantau posisi Anda dan tingkatkan progres belajar untuk naik peringkat.</p>
                </div>

                <div class="summary-row">
                    <div class="summary-box">
                        <span class="label">Posisi Anda</span>
                        <span class="value">-</span>
                    </div>
                    <div class="summary-box">
                        <span class="label">Skor Rata-rata</span>
                        <span class="value">0</span>
                    </div>
                    <div class="summary-box">
                        <span class="label">Total Peserta</span>
                        <span class="value">0</span>
                    </div>
                </div>

                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:12px; flex-wrap:wrap;">
                        <h2 style="margin:0;">Daftar Peringkat</h2>
                        <span class="badge badge-warning">Belum ada skor</span>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Skor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" style="text-align:center; color:#6b7280; padding:36px 16px;">Belum ada data leaderboard untuk ditampilkan. Setelah siswa mulai mengerjakan ujian, peringkat akan muncul di sini.</td>
                            </tr>
                        </tbody>
                    </table>
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
