<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
$adminName = $_SESSION['user_name'] ?? 'Admin';
$adminEmail = $_SESSION['user_email'] ?? 'admin@bimbelalahaido.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Admin Bimbel Alahaido</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        :root {
            --sidebar-bg: #0b1a2e;
            --sidebar-active-bg: rgba(255, 255, 255, 0.12);
            --sidebar-group-bg: rgba(255, 255, 255, 0.05);
            --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
            --sidebar-text: #94a3b8;
            --sidebar-text-white: #ffffff;
            --primary-blue: #1d4ed8;
            --primary-blue-hover: #1e40af;
            --card-border: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: var(--sidebar-text-white);
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            flex-shrink: 0;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            padding: 0 6px;
        }

        .sidebar-brand-logo {
            width: 44px;
            height: 44px;
            background: #ffffff;
            color: #0b1a2e;
            font-weight: 800;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .sidebar-brand-text h2 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
        }

        .sidebar-brand-text span {
            font-size: 12px;
            color: #94a3b8;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background: var(--sidebar-hover-bg);
            color: #ffffff;
        }

        .nav-item.active {
            background: var(--sidebar-active-bg);
            color: #ffffff;
            font-weight: 600;
        }

        .nav-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .nav-chevron {
            color: #64748b;
            font-size: 11px;
        }

        .nav-group {
            background: var(--sidebar-group-bg);
            border-radius: 14px;
            padding: 6px;
            margin: 4px 0;
        }

        .nav-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 10px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .nav-group-items {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin-top: 0;
            padding-left: 6px;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height 0.25s ease, opacity 0.2s ease, margin-top 0.25s ease;
        }

        .nav-group.open .nav-group-items {
            margin-top: 4px;
            max-height: 500px;
            opacity: 1;
        }

        .nav-group-header .nav-chevron {
            display: inline-block;
            transition: transform 0.2s ease;
            transform: rotate(0deg);
        }

        .nav-group.open .nav-group-header .nav-chevron {
            transform: rotate(180deg);
        }

        .nav-sub-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-sub-item svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .nav-sub-item:hover {
            background: var(--sidebar-hover-bg);
            color: #ffffff;
        }

        .nav-sub-item.active {
            background: var(--sidebar-active-bg);
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 20px;
        }

        .btn-sidebar-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px 14px;
            background: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-sidebar-logout:hover {
            background: rgba(220, 38, 38, 0.15);
            border-color: #ef4444;
            color: #fca5a5;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }

        .topbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            flex-shrink: 0;
        }

        .topbar-title h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }

        .topbar-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .content-area {
            padding: 32px;
            max-width: 860px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Alerts */
        .alert-box {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Profile Summary Card */
        .admin-profile-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .admin-avatar-lg {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(29, 78, 216, 0.25);
        }

        .admin-details h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .admin-details p {
            margin: 4px 0 0;
            font-size: 13.5px;
            color: #64748b;
        }

        /* Form Card */
        .password-form-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .card-heading {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
        }

        .card-description {
            font-size: 13.5px;
            color: #64748b;
            margin: 0 0 24px 0;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            padding: 11px 44px 11px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .form-input:focus {
            border-color: var(--primary-blue);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .toggle-pwd-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-pwd-btn:hover {
            color: #475569;
        }

        .strength-meter {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
            display: flex;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }

        .strength-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .btn-submit-password {
            margin-top: 12px;
            padding: 12px 24px;
            background: var(--primary-blue);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-password:hover {
            background: var(--primary-blue-hover);
        }

        .btn-submit-password:active {
            transform: scale(0.99);
        }

        .security-tips-card {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 20px 24px;
        }

        .security-tips-card h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .security-tips-card ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-logo">BI</div>
                <div class="sidebar-brand-text">
                    <h2>Bimbel Alahaido</h2>
                    <span>Portal Admin</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <a href="/dashboard" class="nav-item">
                    <div class="nav-item-left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <div class="nav-group open">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <div class="nav-item-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>laporan</span>
                        </div>
                        <span class="nav-chevron">▼</span>
                    </div>
                    <div class="nav-group-items">
                        <a href="/admin/detail-laporan" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Detail Laporan Perkembangan</span>
                        </a>
                        <a class="nav-sub-item" href="/admin/verifikasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="">Verifikasi Akun</span>
                        </a>
                        <a href="/admin/ganti-password" class="nav-sub-item active">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Ganti Password</span>
                        </a>
                        <a href="/laporan-perkembangan" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                            <span>Laporan Perkembangan Siswa</span>
                        </a>
                    </div>
                </div>

                <!-- Group Operasional -->
                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <div class="nav-item-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Operasional</span>
                        </div>
                        <span class="nav-chevron">▼</span>
                    </div>
                    <div class="nav-group-items">
                        <a href="/admin/absensi" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Absensi</span>
                        </a>
                        <a href="/admin/nilai" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Nilai</span>
                        </a>
                        <a href="#" class="nav-sub-item" onclick="alert('Menu Jadwal'); return false;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                            <span>Jadwal</span>
                        </a>
                    </div>
                </div>

                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <div class="nav-item-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Konten</span>
                        </div>
                        <span class="nav-chevron">▼</span>
                    </div>
                    <div class="nav-group-items">
                        <a href="#" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Tugas dan Koreksi</span>
                        </a>
                        <a href="/admin/izin-konten" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Izin Konten</span>
                        </a>
                        <a href="/admin/konten" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                            <span>Konten</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="/logout" class="btn-sidebar-logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Keluar</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-title">
                    <h1>Pengaturan Kata Sandi Akun</h1>
                    <div class="topbar-subtitle">Perbarui kredensial login portal admin Bimbel Alahaido</div>
                </div>
            </header>

            <div class="content-area">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert-box alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?php echo htmlspecialchars($_GET['success']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert-box alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                    </div>
                <?php endif; ?>

                <!-- PROFILE INFO -->
                <div class="admin-profile-card">
                    <div class="admin-avatar-lg">
                        <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                    </div>
                    <div class="admin-details">
                        <h3><?php echo htmlspecialchars($adminName); ?></h3>
                        <p><?php echo htmlspecialchars($adminEmail); ?> · <strong style="color:var(--primary-blue);">Administrator Portal</strong></p>
                    </div>
                </div>

                <!-- FORM CARD -->
                <div class="password-form-card">
                    <h3 class="card-heading">Ubah Kata Sandi</h3>
                    <p class="card-description">Pastikan kata sandi baru Anda kuat dan tidak mudah ditebak.</p>

                    <form method="POST" action="/api/admin/ganti-password" id="changePasswordForm">
                        <!-- PASSWORD SAAT INI -->
                        <div class="form-group">
                            <label class="form-label">Kata Sandi Saat Ini</label>
                            <div class="input-wrapper">
                                <input type="password" name="current_password" id="current_password" class="form-input" placeholder="Masukkan kata sandi lama Anda" required>
                                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('current_password', this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- PASSWORD BARU -->
                        <div class="form-group">
                            <label class="form-label">Kata Sandi Baru</label>
                            <div class="input-wrapper">
                                <input type="password" name="new_password" id="new_password" class="form-input" placeholder="Minimal 8 karakter" required oninput="checkStrength(this.value)">
                                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('new_password', this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <div class="strength-meter">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <span class="strength-text" id="strengthText">Kekuatan kata sandi: Masukkan password</span>
                        </div>

                        <!-- KONFIRMASI PASSWORD BARU -->
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-wrapper">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-input" placeholder="Ulangi kata sandi baru" required oninput="checkMatch()">
                                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('confirm_password', this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <span class="strength-text" id="matchText"></span>
                        </div>

                        <button type="submit" class="btn-submit-password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>Simpan Kata Sandi Baru</span>
                        </button>
                    </form>
                </div>

                <!-- TIPS KEAMANAN -->
                <div class="security-tips-card">
                    <h4>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Tips Keamanan Akun Admin
                    </h4>
                    <ul>
                        <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol (misal: <code>Admin#2026!</code>).</li>
                        <li>Jangan gunakan kata sandi yang sama dengan akun pribadi lainnya.</li>
                        <li>Ganti kata sandi secara berkala demi menjaga keamanan data siswa dan materi bimbel.</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleNavGroup(headerEl) {
            const group = headerEl.closest('.nav-group');
            if (!group) return;
            group.classList.toggle('open');
        }

        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.style.color = 'var(--primary-blue)';
            } else {
                input.type = 'password';
                btn.style.color = '#94a3b8';
            }
        }

        function checkStrength(val) {
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            if (val.length === 0) {
                bar.style.width = '0%';
                text.textContent = 'Kekuatan kata sandi: Masukkan password';
                text.style.color = '#64748b';
            } else if (score <= 1) {
                bar.style.width = '25%';
                bar.style.backgroundColor = '#ef4444';
                text.textContent = 'Kekuatan kata sandi: Lemah (minimal 8 karakter)';
                text.style.color = '#ef4444';
            } else if (score === 2) {
                bar.style.width = '50%';
                bar.style.backgroundColor = '#f59e0b';
                text.textContent = 'Kekuatan kata sandi: Cukup';
                text.style.color = '#f59e0b';
            } else if (score === 3) {
                bar.style.width = '75%';
                bar.style.backgroundColor = '#3b82f6';
                text.textContent = 'Kekuatan kata sandi: Baik';
                text.style.color = '#3b82f6';
            } else {
                bar.style.width = '100%';
                bar.style.backgroundColor = '#10b981';
                text.textContent = 'Kekuatan kata sandi: Sangat Kuat 👍';
                text.style.color = '#10b981';
            }
            checkMatch();
        }

        function checkMatch() {
            const pwd = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            const text = document.getElementById('matchText');

            if (!confirm) {
                text.textContent = '';
                return;
            }

            if (pwd === confirm) {
                text.textContent = '✓ Kata sandi cocok';
                text.style.color = '#10b981';
            } else {
                text.textContent = '✗ Kata sandi belum cocok';
                text.style.color = '#ef4444';
            }
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
