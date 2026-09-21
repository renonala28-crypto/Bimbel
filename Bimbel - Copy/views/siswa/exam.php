<!-- CBT Exam Page - Placeholder -->
<?php
$page_title = 'Tryout CBT';
$current_page = 'exam';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi CAT - Bimbel Portal</title>
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
                <a href="/exam" class="nav-item active">Simulasi CAT</a>
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
                <div class="topbar-title"><h1>Tryout CBT 📝</h1></div>
            </div>

            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Daftar Ujian Tersedia</h2>
                    </div>
                    <p style="text-align: center; padding: 40px; color: var(--text-light);">
                        Fitur CBT Exam sedang dalam pengembangan. Harap tunggu update berikutnya!
                    </p>
                </div>
            </div>
        </main>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>
