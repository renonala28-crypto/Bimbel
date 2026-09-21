<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$selectedMonth = isset($_GET['month']) ? (int) $_GET['month'] : 9;
$selectedYear = isset($_GET['year']) ? (int) $_GET['year'] : 2026;
if ($selectedMonth < 1 || $selectedMonth > 12) $selectedMonth = 9;
if ($selectedYear < 2020 || $selectedYear > 2030) $selectedYear = 2026;

$monthName = $months[$selectedMonth] ?? 'September';

$allStudents = $pdo->query("SELECT id, name, nickname FROM users WHERE role = 'siswa' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$stmtScores = $pdo->prepare("
    SELECT s.*, u.name AS student_name, u.nickname AS student_nickname
    FROM student_monthly_scores s
    JOIN users u ON s.user_id = u.id
    WHERE s.period_year = ? AND s.period_month = ?
    ORDER BY s.nilai_total DESC, s.nilai_cat DESC, s.total_samapta DESC
");
$stmtScores->execute([$selectedYear, $selectedMonth]);
$scores = $stmtScores->fetchAll(PDO::FETCH_ASSOC);

$todayDay = 13;
$todayMonth = 9;
$todayYear = 2026;
$todayFormattedLong = $todayDay . ' ' . ($months[$todayMonth] ?? 'September') . ' ' . $todayYear;
$todayFormattedSlash = sprintf('%02d/%02d/%04d', $todayDay, $todayMonth, $todayYear);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perkembangan Siswa - Admin Bimbel Alahaido</title>
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
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            color: #0b1a2e;
            letter-spacing: -0.5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .sidebar-brand-text h2 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .sidebar-brand-text span {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
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

        /* Group Operasional */
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
            transition: all 0.15s;
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

        .nav-sub-item svg {
            width: 16px;
            height: 16px;
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
            background: #f8fafc;
        }

        .topbar {
            background: #ffffff;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .topbar-title h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .topbar-subtitle {
            margin-top: 3px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-badge {
            background: var(--primary-blue);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 999px;
            letter-spacing: 0.2px;
        }

        .topbar-avatar {
            width: 36px;
            height: 36px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .topbar-logout-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #ffffff;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .topbar-logout-btn:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }

        .content-area {
            padding: 24px 32px 60px;
            max-width: 1440px;
        }

        /* ===== ALERT BANNERS ===== */
        .alert-banner {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* ===== CARD 1: NILAI HARIAN CAT ===== */
        .daily-cat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            margin-bottom: 24px;
        }

        .daily-cat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .daily-date-label {
            font-weight: 700;
            font-size: 14px;
            color: #1e293b;
        }

        .badge-tryout-count {
            background: var(--primary-blue);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 999px;
        }

        .daily-cat-empty-box {
            background: #e0f7fa;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 16px 20px;
            color: #0284c7;
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ===== SECTION 2: REKAP NILAI BULANAN ===== */
        .monthly-recap-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .monthly-title {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .monthly-subtitle {
            margin: 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn-add-nilai {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--primary-blue);
            color: #ffffff;
            border-radius: 8px;
            border: none;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
            transition: all 0.2s ease;
        }

        .btn-add-nilai:hover {
            background: var(--primary-blue-hover);
            transform: translateY(-1px);
        }

        /* Filter Box */
        .filter-container {
            margin-bottom: 24px;
        }

        .filter-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .month-select-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .month-select-input {
            padding: 9px 36px 9px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            font-size: 13.5px;
            font-weight: 500;
            color: #1e293b;
            min-width: 170px;
            outline: none;
            appearance: none;
            cursor: pointer;
        }

        .month-select-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .calendar-icon-indicator {
            position: absolute;
            right: 12px;
            pointer-events: none;
            color: #64748b;
        }

        .year-select-input {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            font-size: 13.5px;
            font-weight: 500;
            color: #1e293b;
            outline: none;
            cursor: pointer;
        }

        .btn-filter-submit {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: var(--primary-blue);
            color: #ffffff;
            border-radius: 8px;
            border: none;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-filter-submit:hover {
            background: var(--primary-blue-hover);
        }

        /* ===== TABLE CARD ===== */
        .table-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 24px;
            overflow: hidden;
        }

        .table-title {
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.6px;
            color: var(--text-dark);
            margin: 6px 0 24px;
            text-transform: uppercase;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .scores-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            white-space: nowrap;
        }

        .scores-table th {
            padding: 12px 10px;
            color: #64748b;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
        }

        .scores-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .scores-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Badges & Values */
        .rank-badge {
            background: var(--primary-blue);
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
            text-align: center;
            min-width: 28px;
        }

        .student-name-cell {
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .score-cat-bold {
            font-weight: 700;
            color: #0f172a;
        }

        .score-samapta-blue {
            font-weight: 700;
            color: #2563eb;
        }

        .score-total-green {
            font-weight: 700;
            color: #15803d;
        }

        .action-cell {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit-score {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-delete-score {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .empty-scores-row td {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            font-size: 14px;
        }

        /* ===== MODAL TAMBAH / EDIT NILAI ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(11, 26, 46, 0.8);
            backdrop-filter: blur(6px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 18px;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
            display: flex;
            flex-direction: column;
            transform: scale(0.95);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-header {
            padding: 18px 24px;
            background: #0d2240;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .modal-close-btn {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 24px;
            flex: 1;
        }

        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 10px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            color: #1e293b;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            padding: 9px 16px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
        }

        .btn-submit {
            padding: 9px 20px;
            border-radius: 8px;
            background: var(--primary-blue);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
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
                        <a href="/admin/ganti-password" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Ganti Password</span>
                        </a>
                        <a href="/laporan-perkembangan" class="nav-sub-item active">
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
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-title">
                    <h1>Laporan Perkembangan Siswa</h1>
                    <div class="topbar-subtitle"><?php echo htmlspecialchars($todayFormattedLong); ?></div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-badge">Bimbel Alahaido</div>
                    <div class="topbar-avatar" title="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>">O</div>
                    <a href="/logout" class="topbar-logout-btn" title="Keluar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </header>

            <div class="content-area">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert-banner alert-success">
                        <span>✓ <?php echo htmlspecialchars($_GET['success']); ?></span>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert-banner alert-error">
                        <span>⚠️ <?php echo htmlspecialchars($_GET['error']); ?></span>
                    </div>
                <?php endif; ?>

                <!-- CARD 1: NILAI HARIAN CAT -->
                <div class="daily-cat-card">
                    <div class="daily-cat-header">
                        <span class="daily-date-label"><?php echo $todayFormattedSlash; ?></span>
                        <span class="badge-tryout-count">0 materi / tryout</span>
                    </div>
                    <div class="daily-cat-empty-box">
                        Belum ada nilai CAT resmi pada tanggal ini.
                    </div>
                </div>

                <!-- SECTION 2: REKAP NILAI BULANAN & FILTER -->
                <div class="monthly-recap-header">
                    <div>
                        <h2 class="monthly-title">Rekap Nilai Bulanan</h2>
                        <p class="monthly-subtitle">Default selalu menampilkan bulan berjalan · Bimbel Alahaido</p>
                    </div>
                    <div>
                        <button type="button" class="btn-add-nilai" onclick="openAddScoreModal()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Tambah Nilai</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER FORM -->
                <div class="filter-container">
                    <label class="filter-label">Pilih bulan dan tahun</label>
                    <form method="GET" action="/admin/nilai" class="filter-form">
                        <div class="month-select-wrapper">
                            <select name="month" class="month-select-input" onchange="this.form.submit()">
                                <?php foreach ($months as $num => $name): ?>
                                    <option value="<?php echo $num; ?>" <?php echo $num === $selectedMonth ? 'selected' : ''; ?>>
                                        <?php echo $name . ' ' . $selectedYear; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="calendar-icon-indicator" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                        <button type="submit" class="btn-filter-submit">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            <span>Tampilkan</span>
                        </button>
                    </form>
                </div>

                <!-- CARD 3: DAFTAR NILAI SISWA TABLE CARD -->
                <div class="table-card">
                    <div class="table-title">
                        DAFTAR NILAI SISWA PADA BULAN <?php echo strtoupper($monthName . ' ' . $selectedYear); ?>
                    </div>

                    <div class="table-responsive">
                        <table class="scores-table">
                            <thead>
                                <tr>
                                    <th>PERINGKAT</th>
                                    <th>SISWA</th>
                                    <th>TWK</th>
                                    <th>TIU</th>
                                    <th>TKP</th>
                                    <th>NILAI<br>CAT</th>
                                    <th>LARI</th>
                                    <th>PUSH-<br>UP</th>
                                    <th>SIT-<br>UP</th>
                                    <th>PULL-<br>UP/CHINNING</th>
                                    <th>SHUTTLE</th>
                                    <th>RENANG</th>
                                    <th>TOTAL<br>SAMAPTA</th>
                                    <th>NILAI<br>TOTAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($scores)): ?>
                                    <tr class="empty-scores-row">
                                        <td colspan="15">
                                            Belum ada data nilai siswa pada bulan <?php echo htmlspecialchars($monthName . ' ' . $selectedYear); ?>.<br>
                                            <button type="button" class="btn-add-nilai" style="margin-top:12px;" onclick="openAddScoreModal()">+ Tambah Nilai Siswa</button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $rank = 1; foreach ($scores as $row): ?>
                                        <tr>
                                            <td>
                                                <span class="rank-badge">#<?php echo $rank++; ?></span>
                                            </td>
                                            <td class="student-name-cell">
                                                <?php echo htmlspecialchars($row['student_name']); ?>
                                            </td>
                                            <td><?php echo (int) $row['twk']; ?></td>
                                            <td><?php echo (int) $row['tiu']; ?></td>
                                            <td><?php echo (int) $row['tkp']; ?></td>
                                            <td class="score-cat-bold">
                                                <?php echo number_format($row['nilai_cat'], 2, ',', '.'); ?>
                                            </td>
                                            <td><?php echo (int) $row['lari_meters']; ?> m</td>
                                            <td><?php echo (int) $row['push_up']; ?></td>
                                            <td><?php echo (int) $row['sit_up']; ?></td>
                                            <td><?php echo (int) $row['pull_up']; ?></td>
                                            <td><?php echo number_format($row['shuttle_seconds'], 2, '.', ''); ?> dtk</td>
                                            <td>
                                                <?php echo number_format($row['renang_seconds'], 2, '.', ''); ?> dtk / <?php echo (int) ($row['renang_distance'] ?: 25); ?> m
                                            </td>
                                            <td class="score-samapta-blue">
                                                <?php echo number_format($row['total_samapta'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="score-total-green">
                                                <?php 
                                                    // Jika desimal 0, tampilkan 1 digit desimal seperti referensi '34,0'
                                                    echo number_format($row['nilai_total'], 1, ',', '.'); 
                                                ?>
                                            </td>
                                            <td>
                                                <div class="action-cell">
                                                    <button type="button" class="btn-edit-score" onclick='editScore(<?php echo json_encode($row); ?>)'>Edit</button>
                                                    <form method="POST" action="/api/admin/nilai/delete" onsubmit="return confirm('Hapus nilai siswa ini?')">
                                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                                        <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
                                                        <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                                                        <button type="submit" class="btn-delete-score">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TAMBAH / EDIT NILAI -->
    <div id="scoreModal" class="modal-overlay" onclick="handleModalBackdrop(event)">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Nilai Siswa</h3>
                <button type="button" class="modal-close-btn" onclick="closeScoreModal()">&times;</button>
            </div>
            <form method="POST" action="/api/admin/nilai/save" id="scoreForm">
                <input type="hidden" name="period_month" value="<?php echo $selectedMonth; ?>">
                <input type="hidden" name="period_year" value="<?php echo $selectedYear; ?>">
                
                <div class="modal-body">
                    <!-- PILIH SISWA -->
                    <div class="form-group">
                        <label>Pilih Siswa</label>
                        <select name="user_id" id="modalStudentSelect" class="form-control" onchange="toggleNewStudentInput(this.value)">
                            <option value="">-- Pilih dari Siswa Terdaftar --</option>
                            <?php foreach ($allStudents as $st): ?>
                                <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['name']); ?></option>
                            <?php endforeach; ?>
                            <option value="0">+ Input Siswa Baru Manual</option>
                        </select>
                    </div>

                    <div class="form-group" id="newStudentGroup" style="display:none;">
                        <label>Nama Siswa Baru</label>
                        <input type="text" name="student_name_new" id="student_name_new" class="form-control" placeholder="Contoh: AHMAD FAUZI">
                    </div>

                    <!-- NILAI CAT -->
                    <div class="form-section-title">Nilai Ujian CAT</div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>TWK</label>
                            <input type="number" step="any" name="twk" id="input_twk" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>TIU</label>
                            <input type="number" step="any" name="tiu" id="input_tiu" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>TKP</label>
                            <input type="number" step="any" name="tkp" id="input_tkp" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nilai CAT (Otomatis)</label>
                        <input type="number" step="any" name="nilai_cat" id="input_nilai_cat" class="form-control" value="0" readonly style="background:#f1f5f9; font-weight:700;">
                    </div>

                    <!-- NILAI SAMAPTA -->
                    <div class="form-section-title">Nilai Samapta / Fisik</div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Lari (meter)</label>
                            <input type="number" name="lari_meters" id="input_lari" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>Push-up (kali)</label>
                            <input type="number" name="push_up" id="input_push_up" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>Sit-up (kali)</label>
                            <input type="number" name="sit_up" id="input_sit_up" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Pull-up (kali)</label>
                            <input type="number" name="pull_up" id="input_pull_up" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>Shuttle Run (detik)</label>
                            <input type="number" step="any" name="shuttle_seconds" id="input_shuttle" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                        <div class="form-group">
                            <label>Renang (detik / 25m)</label>
                            <input type="number" step="any" name="renang_seconds" id="input_renang" class="form-control" value="0" oninput="recalcTotals()">
                        </div>
                    </div>

                    <!-- TOTAL NILAI -->
                    <div class="form-section-title">Akumulasi Skor</div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Total Samapta</label>
                            <input type="number" step="any" name="total_samapta" id="input_total_samapta" class="form-control" value="0" oninput="recalcFinalTotal()">
                        </div>
                        <div class="form-group">
                            <label>Nilai Total Akhir</label>
                            <input type="number" step="any" name="nilai_total" id="input_nilai_total" class="form-control" value="0" style="font-weight:800; color:#15803d;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeScoreModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddScoreModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Nilai Siswa (Bulan <?php echo $monthName . ' ' . $selectedYear; ?>)';
            document.getElementById('scoreForm').reset();
            document.getElementById('modalStudentSelect').value = '';
            document.getElementById('newStudentGroup').style.display = 'none';
            document.getElementById('scoreModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function editScore(row) {
            document.getElementById('modalTitle').textContent = 'Edit Nilai: ' + row.student_name;
            document.getElementById('modalStudentSelect').value = row.user_id;
            document.getElementById('newStudentGroup').style.display = 'none';

            document.getElementById('input_twk').value = row.twk;
            document.getElementById('input_tiu').value = row.tiu;
            document.getElementById('input_tkp').value = row.tkp;
            document.getElementById('input_nilai_cat').value = row.nilai_cat;

            document.getElementById('input_lari').value = row.lari_meters;
            document.getElementById('input_push_up').value = row.push_up;
            document.getElementById('input_sit_up').value = row.sit_up;
            document.getElementById('input_pull_up').value = row.pull_up;
            document.getElementById('input_shuttle').value = row.shuttle_seconds;
            document.getElementById('input_renang').value = row.renang_seconds;

            document.getElementById('input_total_samapta').value = row.total_samapta;
            document.getElementById('input_nilai_total').value = row.nilai_total;

            document.getElementById('scoreModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeScoreModal() {
            document.getElementById('scoreModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function handleModalBackdrop(e) {
            if (e.target.id === 'scoreModal') {
                closeScoreModal();
            }
        }

        function toggleNewStudentInput(val) {
            const group = document.getElementById('newStudentGroup');
            if (val === '0') {
                group.style.display = 'block';
                document.getElementById('student_name_new').required = true;
            } else {
                group.style.display = 'none';
                document.getElementById('student_name_new').required = false;
            }
        }

        function recalcTotals() {
            const twk = parseFloat(document.getElementById('input_twk').value) || 0;
            const tiu = parseFloat(document.getElementById('input_tiu').value) || 0;
            const tkp = parseFloat(document.getElementById('input_tkp').value) || 0;
            const cat = twk + tiu + tkp;
            document.getElementById('input_nilai_cat').value = cat.toFixed(2);

            // Auto-calculate samapta score if not manually entered
            const pushUp = parseInt(document.getElementById('input_push_up').value) || 0;
            const sitUp = parseInt(document.getElementById('input_sit_up').value) || 0;
            const pullUp = parseInt(document.getElementById('input_pull_up').value) || 0;

            let currentSamapta = parseFloat(document.getElementById('input_total_samapta').value) || 0;
            if (pushUp > 0 || sitUp > 0 || pullUp > 0) {
                // simple sample calculation if 0
                if (currentSamapta === 0) {
                    currentSamapta = ((pushUp + sitUp + pullUp) / 6).toFixed(2);
                    document.getElementById('input_total_samapta').value = currentSamapta;
                }
            }

            recalcFinalTotal();
        }

        function recalcFinalTotal() {
            const cat = parseFloat(document.getElementById('input_nilai_cat').value) || 0;
            const samapta = parseFloat(document.getElementById('input_total_samapta').value) || 0;

            let finalTotal = parseFloat(document.getElementById('input_nilai_total').value) || 0;
            if (cat > 0 || samapta > 0) {
                if (samapta > 0) {
                    finalTotal = ((cat * 0.5) + (samapta * 0.5)).toFixed(1);
                } else {
                    finalTotal = cat.toFixed(1);
                }
                document.getElementById('input_nilai_total').value = finalTotal;
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeScoreModal();
        });

        function toggleNavGroup(headerEl) {
            const group = headerEl.closest('.nav-group');
            if (!group) return;
            group.classList.toggle('open');
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>