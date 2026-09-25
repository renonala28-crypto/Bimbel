<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Ambil semua daftar siswa
$allStudents = $pdo->query("SELECT id, name, nickname, email, whatsapp, program_tujuan, sekolah_asal, status, created_at FROM users WHERE role = 'siswa' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Tentukan siswa yang sedang dipilih
$selectedStudentId = isset($_GET['siswa_id']) ? (int)$_GET['siswa_id'] : 0;
if ($selectedStudentId <= 0 && !empty($allStudents)) {
    $selectedStudentId = (int)$allStudents[0]['id'];
}

$currentStudent = null;
foreach ($allStudents as $s) {
    if ((int)$s['id'] === $selectedStudentId) {
        $currentStudent = $s;
        break;
    }
}

// Ambil data riwayat nilai bulanan siswa terpilih
$studentScores = [];
if ($selectedStudentId > 0) {
    $stmtScores = $pdo->prepare("
        SELECT * FROM student_monthly_scores 
        WHERE user_id = ? 
        ORDER BY period_year ASC, period_month ASC
    ");
    $stmtScores->execute([$selectedStudentId]);
    $studentScores = $stmtScores->fetchAll(PDO::FETCH_ASSOC);
}

// Hitung statistik ringkasan
$totalPeriods = count($studentScores);
$avgTotal = 0;
$avgCat = 0;
$avgSamapta = 0;
$highestTotal = 0;
$latestScore = !empty($studentScores) ? end($studentScores) : null;

if ($totalPeriods > 0) {
    $sumTotal = array_sum(array_column($studentScores, 'nilai_total'));
    $sumCat = array_sum(array_column($studentScores, 'nilai_cat'));
    $sumSamapta = array_sum(array_column($studentScores, 'total_samapta'));
    
    $avgTotal = $sumTotal / $totalPeriods;
    $avgCat = $sumCat / $totalPeriods;
    $avgSamapta = $sumSamapta / $totalPeriods;
    $highestTotal = max(array_column($studentScores, 'nilai_total'));
}

// Format chart data
$chartLabels = [];
$chartTotalData = [];
$chartCatData = [];
$chartSamaptaData = [];

foreach ($studentScores as $sc) {
    $mName = $months[(int)$sc['period_month']] ?? 'Bulan ' . $sc['period_month'];
    $chartLabels[] = substr($mName, 0, 3) . ' ' . $sc['period_year'];
    $chartTotalData[] = (float)$sc['nilai_total'];
    $chartCatData[] = (float)$sc['nilai_cat'];
    $chartSamaptaData[] = (float)$sc['total_samapta'];
}

$todayDay = 13;
$todayMonth = 9;
$todayYear = 2026;
$todayFormattedLong = $todayDay . ' ' . ($months[$todayMonth] ?? 'September') . ' ' . $todayYear;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan Perkembangan - Admin Bimbel Alahaido</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #334155;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .content-area {
            padding: 28px 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Filter & Selector Card */
        .student-selector-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .selector-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .selector-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #475569;
        }

        .student-dropdown-select {
            padding: 9px 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            background: #f8fafc;
            min-width: 260px;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s;
        }

        .student-dropdown-select:focus {
            border-color: var(--primary-blue);
            background: #ffffff;
        }

        /* Student Profile Header Card */
        .profile-hero-card {
            background: linear-gradient(135deg, #0b1a2e 0%, #1e3a5f 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            box-shadow: 0 10px 25px -5px rgba(11, 26, 46, 0.25);
        }

        .hero-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .hero-avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .hero-info h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .hero-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 8px;
            font-size: 13.5px;
            color: #94a3b8;
            flex-wrap: wrap;
        }

        .hero-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Metrics Summary Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .metric-card.blue::before { background: #3b82f6; }
        .metric-card.indigo::before { background: #6366f1; }
        .metric-card.emerald::before { background: #10b981; }
        .metric-card.amber::before { background: #f59e0b; }

        .metric-title {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .metric-value {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .metric-subtext {
            font-size: 12.5px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Charts Layout */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .charts-container { grid-template-columns: 1fr; }
        }

        .chart-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .chart-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* History Table Card */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .scores-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .scores-table th {
            background: #f8fafc;
            padding: 14px 18px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .scores-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .scores-table tr:hover {
            background: #f8fafc;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pass {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warn {
            background: #fef3c7;
            color: #b45309;
        }

        .empty-state {
            padding: 48px;
            text-align: center;
            color: #64748b;
        }

        @media print {
            .sidebar, .topbar-actions, .student-selector-card, .btn-sidebar-logout {
                display: none !important;
            }
            .main-content {
                padding: 0;
            }
            .dashboard-container {
                display: block;
            }
            .content-area {
                padding: 0;
            }
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
                        <a href="/admin/detail-laporan" class="nav-sub-item active">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Detail Laporan Perkembangan</span>
                        </a>
                        <a class="nav-sub-item" href="/admin/verifikasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="">Verifikasi Akun</span>
                        </a>
                        <a href="/admin/ganti-password" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Ganti Password</span>
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
                    <h1>Detail Laporan Perkembangan Siswa</h1>
                    <div class="topbar-subtitle">Analisis Riwayat Pembelajaran &amp; Evaluasi Fisik Individual · <?php echo htmlspecialchars($todayFormattedLong); ?></div>
                </div>
                <div class="topbar-actions">
                    <button type="button" class="btn-print" onclick="window.print()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                        <span>Cetak Laporan</span>
                    </button>
                </div>
            </header>

            <div class="content-area">
                <!-- PILIH SISWA -->
                <div class="student-selector-card">
                    <div class="selector-left">
                        <span class="selector-label">Pilih Siswa:</span>
                        <form method="GET" action="/admin/detail-laporan" id="studentSelectForm">
                            <select name="siswa_id" class="student-dropdown-select" onchange="this.form.submit()">
                                <?php if (empty($allStudents)): ?>
                                    <option value="">Belum ada siswa terdaftar</option>
                                <?php else: ?>
                                    <?php foreach ($allStudents as $st): ?>
                                        <option value="<?php echo (int)$st['id']; ?>" <?php echo (int)$st['id'] === $selectedStudentId ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($st['name']); ?> (<?php echo htmlspecialchars($st['email']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if ($currentStudent): ?>
                    <!-- PROFILE HERO CARD -->
                    <div class="profile-hero-card">
                        <div class="hero-left">
                            <div class="hero-avatar">
                                <?php echo strtoupper(substr($currentStudent['name'], 0, 1)); ?>
                            </div>
                            <div class="hero-info">
                                <h2><?php echo htmlspecialchars($currentStudent['name']); ?></h2>
                                <div class="hero-meta">
                                    <span>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <?php echo htmlspecialchars($currentStudent['email']); ?>
                                    </span>
                                    <span>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <?php echo htmlspecialchars($currentStudent['whatsapp'] ?: '-'); ?>
                                    </span>
                                    <?php if (!empty($currentStudent['program_tujuan'])): ?>
                                        <span>🎯 Tujuan: <?php echo htmlspecialchars($currentStudent['program_tujuan']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="badge-status-active">
                                <?php echo htmlspecialchars($currentStudent['status'] ?? 'Active'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- METRICS GRID -->
                    <div class="metrics-grid">
                        <div class="metric-card blue">
                            <span class="metric-title">Rata-Rata Nilai Total</span>
                            <span class="metric-value"><?php echo number_format($avgTotal, 1, ',', '.'); ?></span>
                            <span class="metric-subtext">Skor Tertinggi: <?php echo number_format($highestTotal, 1, ',', '.'); ?></span>
                        </div>
                        <div class="metric-card indigo">
                            <span class="metric-title">Rata-Rata Nilai CAT</span>
                            <span class="metric-value"><?php echo number_format($avgCat, 1, ',', '.'); ?></span>
                            <span class="metric-subtext">Passing Grade Standar: 311</span>
                        </div>
                        <div class="metric-card emerald">
                            <span class="metric-title">Rata-Rata Samapta</span>
                            <span class="metric-value"><?php echo number_format($avgSamapta, 1, ',', '.'); ?></span>
                            <span class="metric-subtext">Kebugaran Jasmani Siswa</span>
                        </div>
                        <div class="metric-card amber">
                            <span class="metric-title">Total Periode Terdata</span>
                            <span class="metric-value"><?php echo $totalPeriods; ?> Bulan</span>
                            <span class="metric-subtext">Riwayat Evaluasi Bulanan</span>
                        </div>
                    </div>

                    <!-- CHARTS -->
                    <?php if ($totalPeriods > 0): ?>
                        <div class="charts-container">
                            <div class="chart-card">
                                <div class="chart-header">
                                    <div>
                                        <h3 class="chart-title">Tren Perkembangan Nilai Siswa</h3>
                                        <div class="chart-subtitle">Progres perbandingan Nilai Total, CAT, dan Samapta tiap bulan</div>
                                    </div>
                                </div>
                                <div style="position:relative; height:280px; width:100%;">
                                    <canvas id="trendChart"></canvas>
                                </div>
                            </div>

                            <div class="chart-card">
                                <div class="chart-header">
                                    <div>
                                        <h3 class="chart-title">Komposisi Terakhir</h3>
                                        <div class="chart-subtitle">Detail materi SKD periode terkini</div>
                                    </div>
                                </div>
                                <div style="position:relative; height:280px; width:100%;">
                                    <canvas id="radarCatChart"></canvas>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- TABLE RIWAYAT BULANAN -->
                    <div class="table-card">
                        <div class="table-header">
                            <h3 class="table-title">Riwayat Evaluasi Bulanan Lengkap</h3>
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="scores-table">
                                <thead>
                                    <tr>
                                        <th>Bulan / Periode</th>
                                        <th>TWK</th>
                                        <th>TIU</th>
                                        <th>TKP</th>
                                        <th>Nilai CAT</th>
                                        <th>Lari (m)</th>
                                        <th>Push Up</th>
                                        <th>Sit Up</th>
                                        <th>Pull Up</th>
                                        <th>Total Samapta</th>
                                        <th>Nilai Total</th>
                                        <th>Status SKD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($studentScores)): ?>
                                        <tr>
                                            <td colspan="12" class="empty-state">
                                                Belum ada catatan nilai untuk siswa ini.<br>
                                                <a href="/admin/nilai" style="color:var(--primary-blue); font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+ Tambah Nilai di Menu Nilai</a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($studentScores as $sc): ?>
                                            <?php 
                                                $isPassed = ($sc['twk'] >= 65 && $sc['tiu'] >= 80 && $sc['tkp'] >= 166); 
                                                $monthTitle = ($months[(int)$sc['period_month']] ?? 'Bulan ' . $sc['period_month']) . ' ' . $sc['period_year'];
                                            ?>
                                            <tr>
                                                <td style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($monthTitle); ?></td>
                                                <td><?php echo (int)$sc['twk']; ?></td>
                                                <td><?php echo (int)$sc['tiu']; ?></td>
                                                <td><?php echo (int)$sc['tkp']; ?></td>
                                                <td style="font-weight:700; color:#1d4ed8;"><?php echo number_format($sc['nilai_cat'], 1, ',', '.'); ?></td>
                                                <td><?php echo (int)$sc['lari_meters']; ?> m</td>
                                                <td><?php echo (int)$sc['push_up']; ?></td>
                                                <td><?php echo (int)$sc['sit_up']; ?></td>
                                                <td><?php echo (int)$sc['pull_up']; ?></td>
                                                <td style="font-weight:700; color:#0284c7;"><?php echo number_format($sc['total_samapta'], 1, ',', '.'); ?></td>
                                                <td style="font-weight:800; color:#15803d; font-size:14.5px;"><?php echo number_format($sc['nilai_total'], 1, ',', '.'); ?></td>
                                                <td>
                                                    <?php if ($isPassed): ?>
                                                        <span class="badge-pill badge-pass">✓ Lulus PG</span>
                                                    <?php else: ?>
                                                        <span class="badge-pill badge-warn">Perlu Peningkatan</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-card">
                        <div class="empty-state">
                            <h3>Tidak ada siswa yang dipilih</h3>
                            <p>Silakan daftarkan atau verifikasi akun siswa terlebih dahulu.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleNavGroup(headerEl) {
            const group = headerEl.closest('.nav-group');
            if (!group) return;
            group.classList.toggle('open');
        }


        <?php if (!empty($studentScores)): ?>
        // Line Chart Perkembangan
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [
                    {
                        label: 'Nilai Total',
                        data: <?php echo json_encode($chartTotalData); ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.35,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5
                    },
                    {
                        label: 'Nilai CAT',
                        data: <?php echo json_encode($chartCatData); ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4
                    },
                    {
                        label: 'Total Samapta',
                        data: <?php echo json_encode($chartSamaptaData); ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // Bar Chart Komponen CAT Terakhir
        <?php 
            $lastTWK = $latestScore ? (float)$latestScore['twk'] : 0;
            $lastTIU = $latestScore ? (float)$latestScore['tiu'] : 0;
            $lastTKP = $latestScore ? (float)$latestScore['tkp'] : 0;
        ?>
        const ctxRadar = document.getElementById('radarCatChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'bar',
            data: {
                labels: ['TWK (Min 65)', 'TIU (Min 80)', 'TKP (Min 166)'],
                datasets: [{
                    label: 'Skor Siswa',
                    data: [<?php echo $lastTWK; ?>, <?php echo $lastTIU; ?>, <?php echo $lastTKP; ?>],
                    backgroundColor: ['#3b82f6', '#6366f1', '#10b981'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
        <?php endif; ?>
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
