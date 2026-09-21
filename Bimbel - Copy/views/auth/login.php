<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Masuk ke akun Anda atau daftar sebagai calon siswa pada lembaga tujuan.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* ===== SPLIT AUTH LAYOUT ===== */
        * { font-family: 'Inter', -apple-system, sans-serif; }

        body.split-auth-page {
            display: flex;
            min-height: 100vh;
            background: #f0f2f5;
            padding: 0;
            margin: 0;
        }

        /* ---- Left Panel ---- */
        .split-left {
            width: 300px;
            min-width: 300px;
            background: linear-gradient(160deg, #0d2240 0%, #133a5e 60%, #1a5276 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 36px 28px 28px;
            position: relative;
            overflow: hidden;
        }

        .split-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .split-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 44px;
        }

        .brand-icon {
            width: 50px; height: 50px;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.15);
            flex-shrink: 0;
        }

        .brand-text-sub {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .brand-text-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #fff;
        }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            margin-bottom: 28px;
            width: fit-content;
            backdrop-filter: blur(4px);
        }

        .portal-badge svg { width:14px; height:14px; }

        .hero-title {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            color: #fff;
        }

        .hero-title span {
            color: #f0c040;
        }

        .hero-desc {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            line-height: 1.7;
            margin-bottom: 36px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: rgba(255,255,255,0.72);
            line-height: 1.5;
        }

        .feature-list li .feat-icon {
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .left-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 10px;
            color: rgba(255,255,255,0.35);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* ---- Right Panel ---- */
        .split-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            overflow-y: auto;
        }

        .auth-card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 20px;
            padding: 44px 48px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.08);
            animation: slideUpFade 0.5s ease-out both;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .auth-header-title h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0d2240;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .auth-header-title p {
            font-size: 13px;
            color: #6b7280;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #15803d;
            white-space: nowrap;
        }

        /* Tabs */
        .auth-tabs {
            display: flex;
            background: #f3f4f6;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 28px;
            gap: 4px;
        }

        .auth-tab {
            flex: 1;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: all 0.25s ease;
        }

        .auth-tab.active {
            background: #fff;
            color: #0d2240;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .auth-tab svg { width: 16px; height: 16px; }

        /* Alert */
        .alert-logout {
            background: #fefce8;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger-new {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #b91c1c;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form */
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #1a5276;
            box-shadow: 0 0 0 3.5px rgba(26,82,118,0.1);
        }

        .form-group-new {
            margin-bottom: 20px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .form-input {
            padding-right: 46px;
        }

        .toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .toggle-eye:hover { color: #374151; }
        .toggle-eye svg { width: 18px; height: 18px; }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .remember-row input[type=checkbox] {
            width: 16px; height: 16px;
            accent-color: #1a5276;
            cursor: pointer;
            flex-shrink: 0;
        }

        .remember-row label {
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
        }

        /* Primary Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #133a5e 0%, #1a5276 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: all 0.2s ease;
            letter-spacing: -0.2px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0d2240 0%, #133a5e 100%);
            box-shadow: 0 6px 20px rgba(13,34,64,0.35);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); box-shadow: none; }
        .btn-login svg { width: 18px; height: 18px; }

        .login-note {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .login-note svg { width: 13px; height: 13px; color: #9ca3af; }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #0b2545;
            text-decoration: none;
            background: #eef4f8;
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid #8da9c4;
            transition: all 0.2s ease;
            margin-bottom: 22px;
        }

        .btn-back-home:hover {
            background: #0b2545;
            color: #ffffff;
            border-color: #0b2545;
        }

        .btn-back-home svg {
            width: 16px;
            height: 16px;
        }

        /* ===== MODAL PERSIAPAN ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(3px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: all;
        }

        .modal-box {
            background: #fff;
            border-radius: 18px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            transform: scale(0.92) translateY(20px);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .modal-overlay.open .modal-box {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            background: linear-gradient(135deg, #0d2240 0%, #1a5276 100%);
            color: #fff;
            padding: 22px 24px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header-left .modal-icon {
            font-size: 22px;
        }

        .modal-header h2 {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .modal-close {
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #fff;
            font-size: 18px;
            transition: background 0.2s;
        }

        .modal-close:hover { background: rgba(255,255,255,0.25); }

        .modal-body {
            overflow-y: auto;
            padding: 20px 22px;
            flex: 1;
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 10px;
            background: #fafafa;
            font-size: 13.5px;
            color: #374151;
            line-height: 1.5;
        }

        .checklist-item .ci-icon {
            font-size: 18px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .notice-box {
            border-radius: 12px;
            padding: 13px 16px;
            margin-bottom: 10px;
            font-size: 13px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .notice-box .nb-icon { font-size: 17px; flex-shrink: 0; margin-top: 1px; }

        .notice-wa {
            background: #eff8ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .notice-pending {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .notice-security {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        /* Payment Info Card */
        .payment-info-box {
            background: linear-gradient(135deg, #0d4f3c 0%, #0e6b50 100%);
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 12px;
            color: #fff;
        }

        .pib-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .pib-icon { font-size: 18px; }

        .pib-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.85;
        }

        .pib-amount {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            line-height: 1;
        }

        .pib-desc {
            font-size: 12.5px;
            opacity: 0.8;
            line-height: 1.55;
        }

        .pib-desc strong {
            opacity: 1;
            color: #6ee7b7;
        }

        .pib-format {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.15);
            font-size: 12px;
            opacity: 0.78;
            line-height: 1.5;
        }

        .pib-format strong {
            opacity: 1;
            color: #a7f3d0;
        }



        .modal-footer {
            padding: 16px 22px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            gap: 10px;
            flex-shrink: 0;
            background: #fff;
        }

        .btn-modal-cancel {
            flex: 0 0 auto;
            padding: 12px 22px;
            background: transparent;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-cancel:hover { border-color: #9ca3af; color: #374151; }

        .btn-modal-proceed {
            flex: 1;
            padding: 12px 22px;
            background: linear-gradient(135deg, #133a5e 0%, #1a5276 100%);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-modal-proceed:hover {
            background: linear-gradient(135deg, #0d2240 0%, #133a5e 100%);
            box-shadow: 0 4px 14px rgba(13,34,64,0.35);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .split-left { display: none; }
            .split-right { padding: 20px; }
            .auth-card { padding: 30px 24px; }
        }

        @media (max-width: 480px) {
            .auth-card { padding: 24px 18px; border-radius: 14px; }
            .auth-header { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body class="split-auth-page">

    <!-- ========== LEFT PANEL ========== -->
    <div class="split-left">
        <div class="brand-area">
            <div class="brand-icon">🏫</div>
            <div>
                <div class="brand-text-sub">Portal Siswa</div>
                <div class="brand-text-name"><?php echo APP_NAME; ?></div>
            </div>
        </div>

        <div class="portal-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Portal Siswa Terintegrasi
        </div>

        <h1 class="hero-title">
            Satu akun.<br>
            <span>Semua proses<br>terhubung.</span>
        </h1>

        <p class="hero-desc">
            Portal terintegrasi untuk pendaftaran, pembelajaran, simulasi CAT, nilai, dan monitoring perkembangan siswa.
        </p>

        <ul class="feature-list">
            <li>
                <span class="feat-icon">🏦</span>
                <span>Satu portal untuk seluruh lembaga yang terhubung.</span>
            </li>
            <li>
                <span class="feat-icon">🖥️</span>
                <span>CAT, nilai, materi, dan perkembangan siswa terintegrasi.</span>
            </li>
            <li>
                <span class="feat-icon">👥</span>
                <span>Pendaftaran langsung terhubung ke admin lembaga tujuan.</span>
            </li>
        </ul>

        <div class="left-footer">
            Berani Jujur Berhasil
        </div>
    </div>

    <!-- ========== RIGHT PANEL ========== -->
    <div class="split-right">
        <div class="auth-card">

            <!-- Tombol Kembali ke Landing Page -->
            <a href="/" class="btn-back-home">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Beranda
            </a>

            <!-- Header -->
            <div class="auth-header">
                <div class="auth-header-title">
                    <h1>Selamat datang di <?php echo APP_NAME; ?></h1>
                    <p>Masuk ke akun Anda atau daftar sebagai calon siswa pada lembaga tujuan.</p>
                </div>
                <div class="secure-badge">
                    🔒 Akses Aman
                </div>
            </div>

            <!-- Alert logout -->
            <?php if (isset($_GET['logout'])): ?>
            <div class="alert-logout">
                ✅ Anda sudah berhasil keluar dari aplikasi.
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert-danger-new">
                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="auth-tabs" role="tablist">
                <button class="auth-tab active" id="tab-login" role="tab" aria-selected="true" onclick="switchTab('login')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login
                </button>
                <button class="auth-tab" id="tab-register" role="tab" aria-selected="false" onclick="openPreRegModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Daftar Calon Siswa
                </button>
            </div>

            <!-- Login Form -->
            <div id="panel-login">
                <form method="POST" action="/api/login" id="loginForm">
                    <div class="form-group-new">
                        <label class="form-label" for="email">Email akun</label>
                        <input class="form-input" type="email" id="email" name="email" required
                               placeholder="nama@email.com" autocomplete="email">
                    </div>

                    <div class="form-group-new">
                        <label class="form-label" for="password">Kata sandi</label>
                        <div class="password-wrap">
                            <input class="form-input" type="password" id="password" name="password" required
                                   placeholder="••••••••" autocomplete="current-password">
                            <button type="button" class="toggle-eye" id="togglePwd" data-target="password" aria-label="Lihat password">
                                <svg id="eyeIconPwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3.2 3.2 0 0 0 13.4 13.4"/><path d="M9.1 5.3A12.8 12.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-4.4 5.3"/><path d="M6.2 6.2A18.7 18.7 0 0 0 2 12s3.5 7 10 7a12.3 12.3 0 0 0 5.1-1.2"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" id="remember_me" name="remember_me" value="1" checked>
                        <label for="remember_me">Biarkan saya tetap masuk</label>
                    </div>

                    <button type="submit" class="btn-login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Masuk ke <?php echo APP_NAME; ?>
                    </button>
                </form>

                <p class="login-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Pendaftar baru dapat login setelah pendaftaran dan bukti pembayaran diverifikasi oleh Superadmin.
                </p>
            </div>

        </div>
    </div>

    <!-- ========== MODAL SIAPKAN SEBELUM MENDAFTAR ========== -->
    <div class="modal-overlay" id="preRegModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-left">
                    <span class="modal-icon">📋</span>
                    <h2 id="modalTitle">Siapkan sebelum mendaftar</h2>
                </div>
                <button class="modal-close" onclick="closePreRegModal()" aria-label="Tutup">&times;</button>
            </div>

            <div class="modal-body">

                <div class="checklist-item">
                    <span class="ci-icon">💬</span>
                    <span>Nomor WhatsApp siswa yang aktif.</span>
                </div>

                <div class="checklist-item">
                    <span class="ci-icon">📧</span>
                    <span>Email aktif dan password minimal 8 karakter.</span>
                </div>

                <!-- Payment Info -->
                <div class="payment-info-box">
                    <div class="pib-header">
                        <span class="pib-icon">💳</span>
                        <span class="pib-title">Biaya Pendaftaran</span>
                    </div>
                    <div class="pib-amount">Rp 100.000</div>
                    <div class="pib-desc">Pembayaran dilakukan via <strong>QRIS</strong> yang akan ditampilkan pada halaman pendaftaran. Setelah transfer, unggah bukti pembayaran sebagai syarat verifikasi.</div>
                    <div class="pib-format">📎 Format bukti: <strong>JPG, PNG, atau WEBP</strong> — maks. <strong>2 MB per file</strong></div>
                </div>

                <div class="notice-box notice-wa">
                    <span class="nb-icon">💬</span>
                    <span>Setelah seluruh data dan bukti pembayaran lengkap, proses pendaftaran akan dilanjutkan dengan <strong>konfirmasi WhatsApp ke Superadmin</strong>.</span>
                </div>

                <div class="notice-box notice-pending">
                    <span class="nb-icon">👤</span>
                    <span><strong>Akun belum langsung aktif.</strong> Pendaftaran dan bukti pembayaran akan diperiksa serta diverifikasi oleh <strong>Superadmin</strong> terlebih dahulu.</span>
                </div>

                <div class="notice-box notice-security">
                    <span class="nb-icon">🛡️</span>
                    <span>Bukti pembayaran hanya digunakan untuk proses verifikasi Superadmin dan tidak ditampilkan kepada siswa lain.</span>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closePreRegModal()">Batal</button>
                <button class="btn-modal-proceed" onclick="goToRegister()">
                    ✅ Saya Sudah Siap — Lanjut Daftar
                </button>
            </div>
        </div>
    </div>

    <script>
        /* ---- Tab switching ---- */
        function switchTab(tab) {
            document.querySelectorAll('.auth-tab').forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            document.getElementById('tab-' + tab).classList.add('active');
            document.getElementById('tab-' + tab).setAttribute('aria-selected', 'true');
        }

        /* ---- Pre-registration modal ---- */
        function openPreRegModal() {
            document.getElementById('preRegModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closePreRegModal() {
            document.getElementById('preRegModal').classList.remove('open');
            document.body.style.overflow = '';
            // Reset tab selection back to login
            switchTab('login');
        }

        function goToRegister() {
            window.location.href = '/register';
        }

        // Close modal on overlay click
        document.getElementById('preRegModal').addEventListener('click', function(e) {
            if (e.target === this) closePreRegModal();
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePreRegModal();
        });

        /* ---- Password toggle ---- */
        const eyeOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3.2"/></svg>';
        const eyeClosed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3.2 3.2 0 0 0 13.4 13.4"/><path d="M9.1 5.3A12.8 12.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-4.4 5.3"/><path d="M6.2 6.2A18.7 18.7 0 0 0 2 12s3.5 7 10 7a12.3 12.3 0 0 0 5.1-1.2"/></svg>';

        document.getElementById('togglePwd').addEventListener('click', function() {
            const input = document.getElementById('password');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.innerHTML = isHidden ? eyeOpen : eyeClosed;
            this.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Lihat password');
        });

        /* ---- Show logout alert if ?logout param ---- */
        <?php if (isset($_GET['logout'])): ?>
        // already handled in PHP above
        <?php endif; ?>
    </script>
</body>
</html>
