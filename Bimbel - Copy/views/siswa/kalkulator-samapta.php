<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Capaian Samapta - Bimbel Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .calc-header {
            margin-bottom: 24px;
        }

        /* Filter Section */
        .filter-card {
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 16px;
            padding: 22px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .custom-select {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: #0f172a;
            font-size: 14.5px;
            font-weight: 600;
            font-family: inherit;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 42px;
        }

        .custom-select:focus {
            border-color: #1e3a8a;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
        }

        .info-banner {
            margin-top: 18px;
            padding: 14px 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .info-banner-icon {
            width: 36px;
            height: 36px;
            background: #1e3a8a;
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .info-banner-text h4 {
            margin: 0 0 2px 0;
            font-size: 14px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .info-banner-text p {
            margin: 0;
            font-size: 13px;
            color: #475569;
            line-height: 1.4;
        }

        /* Movement Table / Cards */
        .table-card {
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }

        .calc-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .calc-table thead {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .calc-table th {
            padding: 14px 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }

        .calc-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }

        .calc-table tbody tr:last-child {
            border-bottom: none;
        }

        .calc-table tbody tr:hover {
            background: #f8fafc;
        }

        .calc-table td {
            padding: 16px 20px;
            vertical-align: middle;
        }

        .movement-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .movement-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .movement-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .movement-unit {
            font-size: 12px;
            color: #64748b;
        }

        .target-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            border: 1px solid #e2e8f0;
        }

        .input-wrap {
            position: relative;
            max-width: 170px;
            display: flex;
            align-items: center;
        }

        .input-calc {
            width: 100%;
            padding: 10px 42px 10px 14px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
            text-align: right;
        }

        .input-calc:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
        }

        .input-suffix {
            position: absolute;
            right: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            pointer-events: none;
        }

        .score-progress-wrap {
            min-width: 180px;
        }

        .score-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .score-percent {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }

        .score-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .badge-none { background: #f1f5f9; color: #64748b; }
        .badge-excellent { background: #dcfce7; color: #15803d; }
        .badge-good { background: #dbeafe; color: #1d4ed8; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }

        .progress-bar-bg {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            transition: width 0.4s ease, background-color 0.4s ease;
            background: #94a3b8;
        }

        .fill-excellent { background: #10b981; }
        .fill-good { background: #3b82f6; }
        .fill-warning { background: #f59e0b; }
        .fill-danger { background: #ef4444; }

        /* Action Buttons */
        .action-row {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .btn-calc-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-family: inherit;
        }

        .btn-calc-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        .btn-calc-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .btn-calc-secondary {
            background: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
        }

        .btn-calc-secondary:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Result Section */
        .result-card {
            background: linear-gradient(135deg, #0a1626 0%, #16293f 100%);
            border-radius: 18px;
            padding: 28px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .result-grid {
            display: grid;
            grid-template-columns: 240px 1fr 220px;
            gap: 24px;
            align-items: center;
        }

        @media (max-width: 992px) {
            .result-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }

        .result-score-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        .result-score-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .result-score-val {
            font-size: 46px;
            font-weight: 800;
            line-height: 1.1;
            background: linear-gradient(135deg, #38bdf8, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }

        .result-status-tag {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .result-content h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
        }

        .result-content p {
            margin: 0 0 14px 0;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
        }

        .result-tips {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #93c5fd;
            border-left: 3px solid #3b82f6;
        }

        .result-gap-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        .result-gap-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .result-gap-val {
            font-size: 38px;
            font-weight: 800;
            color: #f59e0b;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .result-gap-sub {
            font-size: 12px;
            color: #94a3b8;
        }

        /* Disclaimer */
        .disclaimer-card {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
        }

        .disclaimer-card svg {
            width: 20px;
            height: 20px;
            stroke: #d97706;
            flex-shrink: 0;
            margin-top: 1px;
        }

        @media (max-width: 768px) {
            .calc-table th:nth-child(2),
            .calc-table td:nth-child(2) {
                display: none;
            }
            .input-wrap {
                max-width: 130px;
            }
            .score-progress-wrap {
                min-width: 120px;
            }
        }
    </style>
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <!-- SIDEBAR -->
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
                <a href="/kalkulator-samapta" class="nav-item active">Kalkulator Samapta</a>
                <a href="/leaderboard" class="nav-item">Leaderboard</a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Siswa'); ?></p>
                    <p class="user-role">Siswa Aktif</p>
                </div>
                <a href="/logout" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Kalkulator Capaian Samapta ⏱️</h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

            <div class="content-area">
                <!-- HEADER OVERVIEW CARD -->
                <div class="card calc-header">
                    <span class="page-kicker">Physical Readiness &bull; Uji Kesiapan Jasmani</span>
                    <h2 style="margin: 0 0 6px 0;">Hitung Kesiapan & Capaian Tes Samapta</h2>
                    <p style="margin: 0; color: #64748b;">
                        Simulasikan capaian nilai latihan fisik jasmani Anda terhadap standar nilai 100 seleksi POLRI, TNI, dan Kedinasan (AKMIL/AKPOL).
                    </p>
                </div>

                <!-- FILTER SELEKSI & GENDER -->
                <div class="filter-card">
                    <div class="filter-grid">
                        <div>
                            <label class="form-label" for="selectTarget">Target Seleksi</label>
                            <select class="custom-select" id="selectTarget" onchange="updateInfo()">
                                <option value="polri">POLRI (Kepolisian Negara Republik Indonesia)</option>
                                <option value="tni_ad">TNI AD (Angkatan Darat)</option>
                                <option value="tni_al">TNI AL (Angkatan Laut)</option>
                                <option value="tni_au">TNI AU (Angkatan Udara)</option>
                                <option value="akmil">AKMIL / AKPOL (Akademi Militer & Kepolisian)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="selectGender">Jenis Kelamin</label>
                            <select class="custom-select" id="selectGender" onchange="updateInfo()">
                                <option value="pria">Pria (Laki-laki)</option>
                                <option value="wanita">Wanita (Perempuan)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Info Banner -->
                    <div class="info-banner" id="infoBanner">
                        <div class="info-banner-icon">📋</div>
                        <div class="info-banner-text">
                            <h4 id="infoTitle">POLRI &ndash; Pria</h4>
                            <p id="infoDesc">Standar penilaian tes kesamaptaan jasmani Polri untuk peserta pria. Capai target optimal pada setiap gerakan.</p>
                        </div>
                    </div>
                </div>

                <!-- TABEL GERAKAN SAMAPTA -->
                <div class="table-card">
                    <table class="calc-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Item Gerakan</th>
                                <th style="width: 18%;">Target Nilai 100</th>
                                <th style="width: 24%;">Hasil Latihan Anda</th>
                                <th style="width: 28%;">Capaian & Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- LARI 12 MENIT -->
                            <tr id="row_lari">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">🏃</div>
                                        <div>
                                            <div class="movement-name">Lari 12 Menit</div>
                                            <div class="movement-unit">Samapta A &bull; Satuan: meter</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_lari">3466</strong> m</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" class="input-calc" id="val_lari" placeholder="0" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">meter</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_lari">0%</span>
                                            <span class="score-badge badge-none" id="badge_lari">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_lari"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- PULL-UP / CHINNING -->
                            <tr id="row_pullup">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">🏋️</div>
                                        <div>
                                            <div class="movement-name" id="name_pullup">Pull-up / Chinning (1 Menit)</div>
                                            <div class="movement-unit">Samapta B &bull; Satuan: kali (rep)</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_pullup">17</strong> kali</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" class="input-calc" id="val_pullup" placeholder="0" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">kali</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_pullup">0%</span>
                                            <span class="score-badge badge-none" id="badge_pullup">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_pullup"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- SIT-UP -->
                            <tr id="row_situp">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">🧘</div>
                                        <div>
                                            <div class="movement-name">Sit-up (1 Menit)</div>
                                            <div class="movement-unit">Samapta B &bull; Satuan: kali (rep)</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_situp">40</strong> kali</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" class="input-calc" id="val_situp" placeholder="0" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">kali</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_situp">0%</span>
                                            <span class="score-badge badge-none" id="badge_situp">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_situp"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- PUSH-UP -->
                            <tr id="row_pushup">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">💪</div>
                                        <div>
                                            <div class="movement-name">Push-up (1 Menit)</div>
                                            <div class="movement-unit">Samapta B &bull; Satuan: kali (rep)</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_pushup">43</strong> kali</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" class="input-calc" id="val_pushup" placeholder="0" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">kali</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_pushup">0%</span>
                                            <span class="score-badge badge-none" id="badge_pushup">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_pushup"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- SHUTTLE RUN -->
                            <tr id="row_shuttle">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">⚡</div>
                                        <div>
                                            <div class="movement-name">Shuttle Run (Lari Angka 8)</div>
                                            <div class="movement-unit">Samapta B &bull; Satuan: detik (makin cepat makin baik)</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_shuttle">16.2</strong> s</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" step="0.01" class="input-calc" id="val_shuttle" placeholder="0.00" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">detik</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_shuttle">0%</span>
                                            <span class="score-badge badge-none" id="badge_shuttle">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_shuttle"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- RENANG -->
                            <tr id="row_renang">
                                <td>
                                    <div class="movement-header">
                                        <div class="movement-icon">🏊</div>
                                        <div>
                                            <div class="movement-name">Renang 25 Meter</div>
                                            <div class="movement-unit">Ketangkasan &bull; Satuan: detik (makin cepat makin baik)</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="target-pill"><strong id="tgt_renang">14</strong> s</span>
                                </td>
                                <td>
                                    <div class="input-wrap">
                                        <input type="number" step="0.01" class="input-calc" id="val_renang" placeholder="0.00" min="0" oninput="hitungSemua()">
                                        <span class="input-suffix">detik</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-progress-wrap">
                                        <div class="score-meta">
                                            <span class="score-percent" id="pct_renang">0%</span>
                                            <span class="score-badge badge-none" id="badge_renang">Belum diisi</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" id="bar_renang"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="action-row">
                    <button type="button" class="btn-calc-action btn-calc-primary" onclick="hitungSemua()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Hitung Ulang Capaian
                    </button>
                    <button type="button" class="btn-calc-action btn-calc-secondary" onclick="resetForm()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        Reset Nilai
                    </button>
                </div>

                <!-- RESULTS SUMMARY CARD -->
                <div class="result-card">
                    <div class="result-grid">
                        <div class="result-score-box">
                            <div class="result-score-label">Rata-Rata Capaian</div>
                            <div class="result-score-val" id="totalPctVal">0.0%</div>
                            <span class="result-status-tag" id="totalBadge">Belum Ada Input</span>
                        </div>

                        <div class="result-content">
                            <h3 id="evalTitle">Siap Mengukur Kesiapan Fisik Anda?</h3>
                            <p id="evalMessage">
                                Masukkan catatan waktu atau repetisi latihan Anda pada tabel di atas. Sistem akan menghitung persentase capaian per gerakan dan memberikan rekomendasi latihan.
                            </p>
                            <div class="result-tips" id="evalTips">
                                💡 <strong>Tips Latihan:</strong> Lakukan pemanasan minimal 10-15 menit sebelum mengukur hasil latihan samapta.
                            </div>
                        </div>

                        <div class="result-gap-box">
                            <div class="result-gap-label">Kekurangan Target</div>
                            <div class="result-gap-val" id="totalGapVal">100%</div>
                            <div class="result-gap-sub">Menuju Standar Nilai 100</div>
                        </div>
                    </div>
                </div>

                <!-- DISCLAIMER CARD -->
                <div class="disclaimer-card">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <strong>Catatan Resmi:</strong> Hasil kalkulasi ini adalah simulasi capaian latihan mandiri dan bukan pengumuman kelulusan resmi. Standar tes dan sistem penilaian dapat disesuaikan sewaktu-waktu sesuai ketentuan panitia penerimaan resmi POLRI / Mabes TNI / Kedinasan.
                    </div>
                </div>

            </div><!-- end content-area -->
        </main>
    </div>

    <script>
        // Real-time clock in topbar
        function updateTime() {
            const now = new Date();
            const el = document.getElementById('currentTime');
            if (el) {
                el.textContent = now.toLocaleTimeString('id-ID');
            }
        }
        updateTime();
        setInterval(updateTime, 1000);

        // Benchmark Data Targets for 100% Score
        const DATA_TARGETS = {
            polri: {
                pria: {
                    lari: 3466, pullup: 17, situp: 40, pushup: 43, shuttle: 16.2, renang: 14,
                    label: 'POLRI – Pria',
                    desc: 'Standar kesamaptaan Polri untuk peserta pria. Nilai 100 diraih jika seluruh target gerakan tercapai.'
                },
                wanita: {
                    lari: 2800, pullup: 0, situp: 35, pushup: 30, shuttle: 18.5, renang: 20,
                    label: 'POLRI – Wanita (Polwan)',
                    desc: 'Standar kesamaptaan Polri untuk peserta wanita (Chinning/Pull-up disesuaikan).'
                }
            },
            tni_ad: {
                pria: {
                    lari: 3500, pullup: 18, situp: 45, pushup: 45, shuttle: 15.8, renang: 13,
                    label: 'TNI AD – Pria',
                    desc: 'Standar samapta TNI Angkatan Darat (Pria) untuk kategori Taruna, Bintara, dan Tamtama.'
                },
                wanita: {
                    lari: 2700, pullup: 0, situp: 32, pushup: 28, shuttle: 19.0, renang: 22,
                    label: 'TNI AD – Wanita (Kowad)',
                    desc: 'Standar kesamaptaan jasmani TNI AD untuk Korps Wanita Angkatan Darat.'
                }
            },
            tni_al: {
                pria: {
                    lari: 3400, pullup: 16, situp: 42, pushup: 42, shuttle: 16.5, renang: 15,
                    label: 'TNI AL – Pria',
                    desc: 'Standar samapta TNI Angkatan Laut (Pria) dengan fokus daya tahan dan ketangkasan renang.'
                },
                wanita: {
                    lari: 2750, pullup: 0, situp: 34, pushup: 29, shuttle: 18.8, renang: 21,
                    label: 'TNI AL – Wanita (Kowal)',
                    desc: 'Standar kesamaptaan jasmani TNI AL untuk Korps Wanita Angkatan Laut.'
                }
            },
            tni_au: {
                pria: {
                    lari: 3450, pullup: 16, situp: 42, pushup: 42, shuttle: 16.3, renang: 14,
                    label: 'TNI AU – Pria',
                    desc: 'Standar samapta TNI Angkatan Udara (Pria) untuk calon perwira, bintara, dan tamtama.'
                },
                wanita: {
                    lari: 2720, pullup: 0, situp: 33, pushup: 28, shuttle: 18.9, renang: 21,
                    label: 'TNI AU – Wanita (WARA)',
                    desc: 'Standar kesamaptaan jasmani TNI AU untuk Wanita Angkatan Udara.'
                }
            },
            akmil: {
                pria: {
                    lari: 3600, pullup: 20, situp: 50, pushup: 50, shuttle: 15.5, renang: 12,
                    label: 'AKMIL / AKPOL – Pria',
                    desc: 'Standar tertinggi Akademi Militer & Akademi Kepolisian (Pria) dengan target fisik optimal.'
                },
                wanita: {
                    lari: 2900, pullup: 0, situp: 38, pushup: 32, shuttle: 18.2, renang: 20,
                    label: 'AKMIL / AKPOL – Wanita',
                    desc: 'Standar Akademi Militer & Akademi Kepolisian untuk calon Taruni.'
                }
            }
        };

        const fields = [
            { id: 'lari', name: 'Lari 12 Menit', unit: 'm', inverse: false },
            { id: 'pullup', name: 'Pull-up / Chinning', unit: 'kali', inverse: false },
            { id: 'situp', name: 'Sit-up', unit: 'kali', inverse: false },
            { id: 'pushup', name: 'Push-up', unit: 'kali', inverse: false },
            { id: 'shuttle', name: 'Shuttle Run', unit: 's', inverse: true },
            { id: 'renang', name: 'Renang 25m', unit: 's', inverse: true }
        ];

        function getTarget() {
            const seleksi = document.getElementById('selectTarget').value;
            const gender = document.getElementById('selectGender').value;
            return DATA_TARGETS[seleksi][gender];
        }

        function updateInfo() {
            const t = getTarget();
            document.getElementById('infoTitle').textContent = t.label;
            document.getElementById('infoDesc').textContent = t.desc;

            // Update Target Numbers in table
            document.getElementById('tgt_lari').textContent = t.lari;
            document.getElementById('tgt_pullup').textContent = t.pullup;
            document.getElementById('tgt_situp').textContent = t.situp;
            document.getElementById('tgt_pushup').textContent = t.pushup;
            document.getElementById('tgt_shuttle').textContent = t.shuttle;
            document.getElementById('tgt_renang').textContent = t.renang;

            // Handle Pullup for wanita
            const pullupRow = document.getElementById('row_pullup');
            const pullupInput = document.getElementById('val_pullup');
            if (t.pullup === 0) {
                pullupRow.style.opacity = '0.35';
                pullupInput.disabled = true;
                pullupInput.value = '';
                document.getElementById('pct_pullup').textContent = 'N/A';
                document.getElementById('badge_pullup').textContent = 'Tidak Diuji';
                document.getElementById('badge_pullup').className = 'score-badge badge-none';
                document.getElementById('bar_pullup').style.width = '0%';
            } else {
                pullupRow.style.opacity = '1';
                pullupInput.disabled = false;
            }

            hitungSemua();
        }

        function hitungSemua() {
            const t = getTarget();
            let totalPct = 0;
            let count = 0;
            let lowestScore = 999;
            let lowestItem = null;

            fields.forEach(item => {
                const f = item.id;
                const inputEl = document.getElementById('val_' + f);
                const pctEl = document.getElementById('pct_' + f);
                const badgeEl = document.getElementById('badge_' + f);
                const barEl = document.getElementById('bar_' + f);

                const targetVal = t[f];

                if (targetVal === 0) {
                    return; // Skip non-applicable movement
                }

                const rawVal = inputEl.value.trim();
                const val = parseFloat(rawVal);

                if (rawVal === '' || isNaN(val) || val <= 0) {
                    pctEl.textContent = '0%';
                    badgeEl.textContent = 'Belum diisi';
                    badgeEl.className = 'score-badge badge-none';
                    barEl.style.width = '0%';
                    barEl.className = 'progress-bar-fill';
                    return;
                }

                let pct = 0;
                if (item.inverse) {
                    // Lower time is better
                    pct = (targetVal / val) * 100;
                } else {
                    // Higher reps/distance is better
                    pct = (val / targetVal) * 100;
                }

                if (pct > 100) pct = 100;
                if (pct < 0) pct = 0;

                pctEl.textContent = pct.toFixed(1) + '%';
                barEl.style.width = pct + '%';

                // Status Badge & Color Class
                if (pct >= 90) {
                    badgeEl.textContent = 'Sangat Baik';
                    badgeEl.className = 'score-badge badge-excellent';
                    barEl.className = 'progress-bar-fill fill-excellent';
                } else if (pct >= 75) {
                    badgeEl.textContent = 'Baik';
                    badgeEl.className = 'score-badge badge-good';
                    barEl.className = 'progress-bar-fill fill-good';
                } else if (pct >= 55) {
                    badgeEl.textContent = 'Cukup';
                    badgeEl.className = 'score-badge badge-warning';
                    barEl.className = 'progress-bar-fill fill-warning';
                } else {
                    badgeEl.textContent = 'Kurang';
                    badgeEl.className = 'score-badge badge-danger';
                    barEl.className = 'progress-bar-fill fill-danger';
                }

                if (pct < lowestScore) {
                    lowestScore = pct;
                    lowestItem = item.name;
                }

                totalPct += pct;
                count++;
            });

            const totalScoreEl = document.getElementById('totalPctVal');
            const totalBadgeEl = document.getElementById('totalBadge');
            const totalGapEl = document.getElementById('totalGapVal');
            const evalTitleEl = document.getElementById('evalTitle');
            const evalMsgEl = document.getElementById('evalMessage');
            const evalTipsEl = document.getElementById('evalTips');

            if (count === 0) {
                totalScoreEl.textContent = '0.0%';
                totalBadgeEl.textContent = 'Belum Ada Input';
                totalGapEl.textContent = '100%';
                evalTitleEl.textContent = 'Siap Mengukur Kesiapan Fisik Anda?';
                evalMsgEl.textContent = 'Masukkan catatan waktu atau repetisi latihan Anda pada tabel di atas. Sistem akan menghitung persentase capaian per gerakan dan memberikan rekomendasi latihan.';
                evalTipsEl.innerHTML = '💡 <strong>Tips Latihan:</strong> Lakukan pemanasan minimal 10-15 menit sebelum mengukur hasil latihan samapta.';
                return;
            }

            const avg = totalPct / count;
            const gap = Math.max(0, 100 - avg);

            totalScoreEl.textContent = avg.toFixed(1) + '%';
            totalGapEl.textContent = gap.toFixed(1) + '%';

            if (avg >= 90) {
                totalBadgeEl.textContent = '🌟 Sangat Siap (Kategori A)';
                evalTitleEl.textContent = 'Performa Sangat Luar Biasa! 🔥';
                evalMsgEl.textContent = `Hebat! Rata-rata capaian fisik Anda berada di angka ${avg.toFixed(1)}%. Kondisi fisik Anda sudah sangat mendekati atau memenuhi standar 100 poin seleksi.`;
                evalTipsEl.innerHTML = '🎯 <strong>Rekomendasi:</strong> Pertahankan ritme latihan, jaga nutrisi dan istirahat agar tidak cedera menjelang tes sesungguhnya.';
            } else if (avg >= 75) {
                totalBadgeEl.textContent = '👍 Siap Seleksi (Kategori B)';
                evalTitleEl.textContent = 'Kondisi Fisik Sudah Bagus! 💪';
                evalMsgEl.textContent = `Bagus! Anda telah mencapai rata-rata ${avg.toFixed(1)}%. Sedikit peningkatan lagi untuk mengunci nilai maksimal di setiap materi tes.`;
                evalTipsEl.innerHTML = lowestItem ? `🎯 <strong>Fokus Peningkatan:</strong> Prioritaskan latihan untuk <strong>${lowestItem}</strong> agar poin keseluruhan terangkat maksimal.` : '🎯 Tingkatkan intensitas latihan secara bertahap.';
            } else if (avg >= 55) {
                totalBadgeEl.textContent = '⚡ Cukup Baik (Kategori C)';
                evalTitleEl.textContent = 'Masih Perlu Tambahan Latihan 🏃';
                evalMsgEl.textContent = `Rata-rata capaian Anda saat ini ${avg.toFixed(1)}%. Anda masih memiliki selisih kekurangan target sebesar ${gap.toFixed(1)}% dari batas standar maksimal.`;
                evalTipsEl.innerHTML = lowestItem ? `🎯 <strong>Fokus Utama:</strong> Evaluasi teknik gerakan pada <strong>${lowestItem}</strong> dan tambah frekuensi latihan kardio.` : '🎯 Konsisten berlatih 4-5 kali seminggu.';
            } else {
                totalBadgeEl.textContent = '⚠️ Perlu Ditingkatkan (Kategori D)';
                evalTitleEl.textContent = 'Ayo Tingkatkan Disiplin Latihan! ⏱️';
                evalMsgEl.textContent = `Capaian latihan Anda masih di bawah standar (${avg.toFixed(1)}%). Jangan berkecil hati, dengan program latihan terstruktur capaian fisik akan meningkat pesat.`;
                evalTipsEl.innerHTML = '🎯 <strong>Saran:</strong> Konsultasikan jadwal pembinaan fisik jasmani harian dengan instruktur atau mentor bimbel Anda.';
            }
        }

        function resetForm() {
            fields.forEach(item => {
                const inputEl = document.getElementById('val_' + item.id);
                if (inputEl) inputEl.value = '';
            });
            hitungSemua();
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            updateInfo();
        });
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
