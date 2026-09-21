<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - <?php echo APP_NAME; ?></title>
    <meta name="description" content="Daftarkan akun baru sebagai calon siswa di <?php echo APP_NAME; ?>.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        * { font-family: 'Inter', -apple-system, sans-serif; box-sizing: border-box; margin: 0; padding: 0; }

        body {
            display: flex;
            min-height: 100vh;
            background: #f0f2f5;
        }

        /* ===== LEFT PANEL (sama persis dengan login) ===== */
        .split-left {
            width: 280px;
            min-width: 280px;
            background: linear-gradient(160deg, #0d2240 0%, #133a5e 60%, #1a5276 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 36px 26px 28px;
            position: relative;
            overflow: hidden;
        }

        .split-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .split-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 190px; height: 190px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 21px;
            border: 1px solid rgba(255,255,255,0.15);
            flex-shrink: 0;
        }

        .brand-text-sub {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .brand-text-name {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 6px 13px;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            margin-bottom: 26px;
            width: fit-content;
        }

        .hero-title {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 14px;
            color: #fff;
        }

        .hero-title span { color: #f0c040; }

        .hero-desc {
            font-size: 12.5px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        /* Progress steps */
        .reg-steps {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: auto;
        }

        .reg-step {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-num.done {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }

        .step-num.active {
            background: #f0c040;
            color: #0d2240;
        }

        .step-num.next {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.35);
            border: 1px solid rgba(255,255,255,0.12);
        }

        .step-label {
            font-size: 12.5px;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        .step-label.active-label {
            color: #fff;
            font-weight: 700;
        }

        .left-footer {
            margin-top: 32px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 10px;
            color: rgba(255,255,255,0.3);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* ===== RIGHT PANEL ===== */
        .split-right {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 36px 28px;
            overflow-y: auto;
        }

        .reg-card {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border-radius: 20px;
            padding: 40px 44px 36px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.08);
            animation: slideUpFade 0.45s ease-out both;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header card */
        .reg-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 12px;
        }

        .reg-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #0d2240;
            letter-spacing: -0.4px;
            margin-bottom: 5px;
        }

        .reg-header p {
            font-size: 13px;
            color: #6b7280;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1.5px solid #e5e7eb;
            white-space: nowrap;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .back-link:hover {
            color: #0d2240;
            border-color: #0d2240;
            background: #f0f4ff;
        }

        /* Alert */
        .alert-reg-error {
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

        /* Section header */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #9ca3af;
            margin-bottom: 14px;
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f3f4f6;
        }

        .section-title:first-of-type { margin-top: 0; }

        /* Grid form */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Form elements */
        .fg {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 0;
        }

        .fg label {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
        }

        .fg input[type=text],
        .fg input[type=email],
        .fg input[type=tel],
        .fg input[type=password] {
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13.5px;
            color: #111827;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            width: 100%;
        }

        .fg input:focus {
            border-color: #1a5276;
            box-shadow: 0 0 0 3px rgba(26,82,118,0.09);
            background: #fff;
        }

        .fg input::placeholder { color: #d1d5db; }

        /* Password wrap */
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 44px; }
        .pw-toggle {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex; align-items: center;
            padding: 4px;
            transition: color 0.2s;
        }
        .pw-toggle:hover { color: #374151; }
        .pw-toggle svg { width: 17px; height: 17px; }

        /* Divider */
        .form-divider {
            height: 1px;
            background: #f3f4f6;
            margin: 22px 0;
        }

        /* Payment card */
        .payment-card {
            background: linear-gradient(135deg, #0d4f3c 0%, #0e6b50 100%);
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .payment-card-left { flex: 1; }

        .pc-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            opacity: 0.7;
            margin-bottom: 4px;
        }

        .pc-amount {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .pc-note {
            font-size: 11.5px;
            opacity: 0.7;
            line-height: 1.5;
        }

        .pc-format {
            font-size: 11px;
            opacity: 0.65;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .pc-format strong { color: #a7f3d0; opacity: 1; }

        /* QRIS box */
        .qris-section {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
        }

        .qris-section p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .qris-section img {
            max-width: 160px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }

        .qris-nominal {
            display: inline-block;
            background: #0d2240;
            color: #fff;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        /* Upload area */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
            position: relative;
        }

        .upload-area:hover {
            border-color: #1a5276;
            background: #eff6ff;
        }

        .upload-area.drag-over {
            border-color: #1a5276;
            background: #dbeafe;
        }

        .upload-area input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-icon { font-size: 28px; margin-bottom: 8px; }

        .upload-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .upload-note {
            font-size: 12px;
            color: #9ca3af;
        }

        .upload-filename {
            margin-top: 10px;
            font-size: 12.5px;
            color: #1a5276;
            font-weight: 600;
            display: none;
        }

        /* Submit button */
        .btn-register {
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
            margin-top: 24px;
            transition: all 0.2s;
            letter-spacing: -0.2px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #0d2240 0%, #133a5e 100%);
            box-shadow: 0 6px 20px rgba(13,34,64,0.3);
            transform: translateY(-1px);
        }

        .btn-register:active { transform: translateY(0); box-shadow: none; }
        .btn-register svg { width: 18px; height: 18px; }

        .reg-footer-note {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

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

        /* Responsive */
        @media (max-width: 768px) {
            .split-left { display: none; }
            .split-right { padding: 20px; }
            .reg-card { padding: 28px 20px; }
            .form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ===== LEFT PANEL ===== -->
    <div class="split-left">
        <div class="brand-area">
            <div class="brand-icon">🏫</div>
            <div>
                <div class="brand-text-sub">Portal Siswa</div>
                <div class="brand-text-name"><?php echo APP_NAME; ?></div>
            </div>
        </div>

        <div class="step-badge">
            📝 Pendaftaran Baru
        </div>

        <h1 class="hero-title">
            Bergabung<br>bersama<br><span>kami.</span>
        </h1>

        <p class="hero-desc">
            Isi data diri dan lakukan pembayaran untuk mendaftarkan akun baru Anda.
        </p>

        <!-- Progress Steps -->
        <div class="reg-steps">
            <div class="reg-step">
                <div class="step-num done">✓</div>
                <span class="step-label">Baca panduan persiapan</span>
            </div>
            <div class="reg-step">
                <div class="step-num active">2</div>
                <span class="step-label active-label">Isi formulir pendaftaran</span>
            </div>
            <div class="reg-step">
                <div class="step-num next">3</div>
                <span class="step-label">Unggah bukti pembayaran</span>
            </div>
            <div class="reg-step">
                <div class="step-num next">4</div>
                <span class="step-label">Tunggu verifikasi Superadmin</span>
            </div>
        </div>

        <div class="left-footer">Berani Jujur Berhasil</div>
    </div>

    <!-- ===== RIGHT PANEL ===== -->
    <div class="split-right">
        <div class="reg-card">

            <!-- Tombol Kembali ke Landing Page -->
            <a href="/" class="btn-back-home">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Beranda
            </a>

            <!-- Header -->
            <div class="reg-header">
                <div>
                    <h1>Formulir Pendaftaran</h1>
                    <p>Lengkapi semua data di bawah ini dengan benar.</p>
                </div>
                <a href="/login" class="back-link">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali
                </a>
            </div>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert-reg-error">
                ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/api/register" enctype="multipart/form-data" id="regForm">

                <!-- SECTION: Data Diri -->
                <div class="section-title">Data Diri</div>

                <div class="form-grid-2" style="margin-bottom:14px;">
                    <div class="fg">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required placeholder="Nama lengkap Anda" autocomplete="name">
                    </div>
                    <div class="fg">
                        <label for="nickname">Nama Panggilan</label>
                        <input type="text" id="nickname" name="nickname" required placeholder="Nama panggilan">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="fg">
                        <label for="whatsapp">No. WhatsApp</label>
                        <input type="tel" id="whatsapp" name="whatsapp" required placeholder="0812345678" autocomplete="tel">
                    </div>
                    <div class="fg">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="nama@email.com" autocomplete="email">
                    </div>
                </div>

                <div class="form-divider"></div>

                <!-- SECTION: Password -->
                <div class="section-title">Keamanan Akun</div>

                <div class="form-grid-2">
                    <div class="fg">
                        <label for="password">Password</label>
                        <div class="pw-wrap">
                            <input type="password" id="password" name="password" required placeholder="Min. 8 karakter" autocomplete="new-password">
                            <button type="button" class="pw-toggle" data-target="password" aria-label="Lihat password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3.2 3.2 0 0 0 13.4 13.4"/><path d="M9.1 5.3A12.8 12.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-4.4 5.3"/><path d="M6.2 6.2A18.7 18.7 0 0 0 2 12s3.5 7 10 7a12.3 12.3 0 0 0 5.1-1.2"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="fg">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <div class="pw-wrap">
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password" autocomplete="new-password">
                            <button type="button" class="pw-toggle" data-target="confirm_password" aria-label="Lihat konfirmasi password">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3.2 3.2 0 0 0 13.4 13.4"/><path d="M9.1 5.3A12.8 12.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-4.4 5.3"/><path d="M6.2 6.2A18.7 18.7 0 0 0 2 12s3.5 7 10 7a12.3 12.3 0 0 0 5.1-1.2"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-divider"></div>

                <!-- SECTION: Pembayaran -->
                <div class="section-title">Pembayaran Pendaftaran</div>

                <!-- Biaya card -->
                <div class="payment-card">
                    <div class="payment-card-left">
                        <div class="pc-label">💳 Biaya Pendaftaran</div>
                        <div class="pc-amount">Rp 100.000</div>
                        <div class="pc-note">Scan QRIS di bawah ini lalu unggah bukti transfer untuk melanjutkan proses verifikasi.</div>
                        <div class="pc-format">📎 Format bukti: <strong>JPG, PNG, WEBP</strong> — maks. <strong>2 MB</strong></div>
                    </div>
                </div>

                <!-- QRIS -->
                <div class="qris-section">
                    <p>Scan QRIS berikut untuk melakukan pembayaran:</p>
                    <img src="/qris bimbel.jpeg" alt="QRIS Bimbel Alahaido">
                    <br>
                    <span class="qris-nominal">Nominal: Rp 100.000</span>
                </div>

                <!-- Upload bukti -->
                <div class="section-title" style="margin-top:18px;">Unggah Bukti Transfer</div>

                <div class="upload-area" id="uploadArea">
                    <input type="file" id="proof_file" name="proof_file" accept=".jpg,.jpeg,.png,.webp" required>
                    <div class="upload-icon">📂</div>
                    <div class="upload-title">Klik atau seret file ke sini</div>
                    <div class="upload-note">JPG, PNG, WEBP — Maksimal 2 MB</div>
                    <div class="upload-filename" id="uploadFilename"></div>
                </div>

                <button type="submit" class="btn-register">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Daftar &amp; Kirim Bukti
                </button>

            </form>

            <p class="reg-footer-note">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Akun aktif setelah pendaftaran diverifikasi oleh Superadmin.
            </p>

        </div>
    </div>

    <script>
        /* Password toggle */
        const eyeOpen  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3.2"/></svg>';
        const eyeClosed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 10.6A3.2 3.2 0 0 0 13.4 13.4"/><path d="M9.1 5.3A12.8 12.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-4.4 5.3"/><path d="M6.2 6.2A18.7 18.7 0 0 0 2 12s3.5 7 10 7a12.3 12.3 0 0 0 5.1-1.2"/></svg>';

        document.querySelectorAll('.pw-toggle').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.target);
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                this.innerHTML = show ? eyeOpen : eyeClosed;
                this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Lihat password');
            });
        });

        /* Upload filename display */
        document.getElementById('proof_file').addEventListener('change', function () {
            const el = document.getElementById('uploadFilename');
            if (this.files[0]) {
                el.textContent = '✅ ' + this.files[0].name;
                el.style.display = 'block';
                document.querySelector('.upload-icon').textContent = '🖼️';
                document.querySelector('.upload-title').textContent = 'File dipilih';
            }
        });

        /* Drag & drop feedback */
        const ua = document.getElementById('uploadArea');
        ua.addEventListener('dragover', e => { e.preventDefault(); ua.classList.add('drag-over'); });
        ua.addEventListener('dragleave', () => ua.classList.remove('drag-over'));
        ua.addEventListener('drop', () => ua.classList.remove('drag-over'));

        /* Form validation */
        document.getElementById('regForm').addEventListener('submit', function (e) {
            const pwd  = document.getElementById('password').value;
            const cpwd = document.getElementById('confirm_password').value;

            if (pwd !== cpwd) {
                e.preventDefault();
                alert('❌ Password tidak cocok! Silakan periksa kembali.');
                return;
            }

            const file = document.getElementById('proof_file').files[0];
            if (file && file.size > 2097152) {
                e.preventDefault();
                alert('❌ Ukuran file terlalu besar! Maksimal 2 MB.');
            }
        });
    </script>
</body>
</html>
