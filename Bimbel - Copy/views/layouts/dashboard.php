<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2><?php echo APP_NAME; ?></h2>
            </div>

            <nav class="sidebar-nav">
                <?php if ($_SESSION['user_role'] === 'siswa'): ?>
                    <a href="/dashboard" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                    <a href="/exam" class="nav-item <?php echo $current_page === 'exam' ? 'active' : ''; ?>">
                        📝 Tryout CBT
                    </a>
                    <a href="/sudoku" class="nav-item <?php echo $current_page === 'sudoku' ? 'active' : ''; ?>">
                        🎮 Sudoku Game
                    </a>
                    <a href="/materi" class="nav-item <?php echo $current_page === 'materi' ? 'active' : ''; ?>">
                        📚 Materi Belajar
                    </a>
                    <a href="/perkembangan" class="nav-item <?php echo $current_page === 'perkembangan' ? 'active' : ''; ?>">
                        📈 Perkembangan
                    </a>
                    <a href="/kalkulator-samapta" class="nav-item <?php echo $current_page === 'kalkulator-samapta' ? 'active' : ''; ?>">
                        ⏱️ Kalkulator Samapta
                    </a>
                    <a href="/leaderboard" class="nav-item <?php echo $current_page === 'leaderboard' ? 'active' : ''; ?>">
                        🏆 Leaderboard
                    </a>

                <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/dashboard" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        📊 Dashboard Admin
                    </a>
                    <a href="/admin/verifikasi" class="nav-item <?php echo $current_page === 'verifikasi' ? 'active' : ''; ?>">
                        ✓ Verifikasi Akun
                    </a>
                    <a href="/admin/soal" class="nav-item <?php echo $current_page === 'soal' ? 'active' : ''; ?>">
                        📝 Manajemen Soal
                    </a>
                    <a href="/admin/materi" class="nav-item <?php echo $current_page === 'materi' ? 'active' : ''; ?>">
                        📚 Manajemen Materi
                    </a>
                    <a href="/admin/absensi" class="nav-item <?php echo $current_page === 'absensi' ? 'active' : ''; ?>">
                        📋 Absensi Siswa
                    </a>
                    <a href="/admin/nilai-fisik" class="nav-item <?php echo $current_page === 'nilai-fisik' ? 'active' : ''; ?>">
                        💪 Nilai Fisik
                    </a>
                    <a href="/admin/izin-materi" class="nav-item <?php echo $current_page === 'izin-materi' ? 'active' : ''; ?>">
                        📖 Izin Materi
                    </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p class="user-name"><?php echo $_SESSION['user_name']; ?></p>
                    <p class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></p>
                </div>
                <a href="/logout" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

            <div class="content-area">
                <?php // Konten dinamis akan diinsert di sini ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
        // Update jam real-time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
