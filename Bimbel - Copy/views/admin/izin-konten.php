<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

// Get all active students
$allStudents = $pdo->query("SELECT id, name, nickname, created_at FROM users WHERE role = 'siswa' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Helper for CP student code
function formatStudentCode($user) {
    $dt = !empty($user['created_at']) ? date('ymd', strtotime($user['created_at'])) : '260908';
    $seed = ($user['id'] * 179 + 6795) % 10000;
    return 'CP' . $dt . str_pad($seed, 4, '0', STR_PAD_LEFT);
}

// Student lookup map
$studentMap = [];
foreach ($allStudents as $st) {
    $studentMap[$st['id']] = $st['name'] . ' (' . formatStudentCode($st) . ')';
}

// Get materials
$materials = $pdo->query("SELECT * FROM materials ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get CAT simulations
$cats = $pdo->query("SELECT * FROM exam_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Active Tab
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'cat' ? 'cat' : 'materi';

// Metrics calculation
$totalMateri = count($materials);
$totalCat = count($cats);
$totalStudents = count($allStudents);

$customMateriCount = 0;
foreach ($materials as $m) {
    if (!empty(trim($m['allowed_students'] ?? ''))) $customMateriCount++;
}
$customCatCount = 0;
foreach ($cats as $c) {
    if (!empty(trim($c['allowed_students'] ?? ''))) $customCatCount++;
}

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$todayDay = (int)date('d');
$todayMonth = (int)date('m');
$todayYear = (int)date('Y');
$todayFormattedLong = $todayDay . ' ' . ($months[$todayMonth] ?? 'September') . ' ' . $todayYear;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izin Konten - Admin Bimbel Alahaido</title>
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

        .nav-item.active {
            background: var(--primary-blue);
            color: #ffffff;
            font-weight: 600;
        }

        .nav-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item svg, .nav-sub-item svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .nav-chevron {
            color: #64748b;
            font-size: 11px;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        /* Group Navigation */
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
            border-radius: 8px;
            transition: background 0.15s;
        }

        .nav-group-header:hover {
            background: var(--sidebar-hover-bg);
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
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-title h1 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        .topbar-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-badge {
            font-size: 12px;
            font-weight: 600;
            color: #1d4ed8;
            background: #eff6ff;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid #dbeafe;
        }

        .topbar-avatar {
            width: 36px;
            height: 36px;
            background: #1d4ed8;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .topbar-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
        }

        .topbar-logout-btn:hover {
            background: #f1f5f9;
            color: #ef4444;
        }

        .content-container {
            padding: 32px;
            max-width: 1360px;
            margin: 0 auto;
            width: 100%;
        }

        /* ===== STATS ROW ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon-blue { background: #eff6ff; color: #1d4ed8; }
        .stat-icon-indigo { background: #e0e7ff; color: #4338ca; }
        .stat-icon-amber { background: #fef3c7; color: #d97706; }
        .stat-icon-emerald { background: #d1fae5; color: #059669; }

        .stat-info h4 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .stat-info .stat-number {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 4px 0 0;
        }

        /* ===== ALERTS ===== */
        .alert-banner {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* ===== PAGE HEADER ===== */
        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin: 4px 0 0;
        }

        .btn-to-konten {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #ffffff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-to-konten:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        /* ===== TABS ===== */
        .tabs-nav-bar {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .tab-btn {
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: #0f172a;
        }

        .tab-btn.active {
            color: #1d4ed8;
            border-bottom-color: #1d4ed8;
        }

        .tab-badge {
            font-size: 11px;
            padding: 2px 8px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 12px;
        }

        .tab-btn.active .tab-badge {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* ===== TABLE CARD ===== */
        .k-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 24px;
        }

        .table-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-box-wrapper {
            position: relative;
            flex: 1;
            max-width: 320px;
        }

        .search-box-wrapper input {
            width: 100%;
            padding: 9px 12px 9px 36px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            outline: none;
        }

        .search-box-wrapper input:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .search-box-wrapper svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #94a3b8;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .k-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .k-table th {
            text-align: left;
            padding: 12px 16px;
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .k-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .k-table tr:hover td {
            background: #f8fafc;
        }

        .item-title-bold {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }

        .item-subtext {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-aktif { background: #dcfce7; color: #15803d; }
        .badge-nonaktif { background: #f1f5f9; color: #64748b; }

        .badge-access-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #dbeafe;
        }

        .badge-access-custom {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef3c7;
            color: #b45309;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #fde68a;
        }

        .btn-kelola-izin {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #1d4ed8;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-kelola-izin:hover {
            background: #1e40af;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
        }

        .empty-table-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
            font-size: 14px;
        }

        /* ===== MODAL ===== */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 540px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 22px;
            color: #64748b;
            cursor: pointer;
            line-height: 1;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .student-search-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 12px;
            outline: none;
        }

        .student-search-input:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .student-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 8px;
            border-radius: 6px;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            transition: background 0.15s;
        }

        .student-checkbox-item:hover {
            background: #f1f5f9;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }

        .btn-cancel {
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
        }

        .btn-save {
            padding: 8px 18px;
            background: #1d4ed8;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            cursor: pointer;
        }

        .btn-save:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
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
                <a href="/admin/dashboard" class="nav-item">
                    <div class="nav-item-left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Group Laporan -->
                <div class="nav-group">
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
                        <a href="/admin/verifikasi" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>Verifikasi Akun</span>
                        </a>
                        <a href="/admin/ganti-password" class="nav-sub-item">
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

                <!-- Group Konten (Open & Active on Izin Konten) -->
                <div class="nav-group open">
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
                        <a href="/admin/izin-konten" class="nav-sub-item active">
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

        <!-- MAIN CONTENT AREA -->
        <main class="main-content">
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-title">
                    <h1>Izin Konten</h1>
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

            <div class="content-container">
                <!-- Status Alerts -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert-banner alert-success">
                        <span>✓ <?php echo htmlspecialchars($_GET['success']); ?></span>
                        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-weight:bold;">&times;</button>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert-banner alert-error">
                        <span>⚠️ <?php echo htmlspecialchars($_GET['error']); ?></span>
                        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-weight:bold;">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="page-header-row">
                    <div>
                        <h1 class="page-title">Izin Konten Siswa</h1>
                        <p class="page-subtitle">Atur hak akses materi pelajaran dan simulasi CAT untuk setiap siswa yang terdaftar.</p>
                    </div>
                    <div>
                        <a href="/admin/konten" class="btn-to-konten">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                            <span>Kelola Konten &amp; CAT</span>
                        </a>
                    </div>
                </div>

                <!-- STATS ROW -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon-wrapper stat-icon-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Total Materi</h4>
                            <div class="stat-number"><?php echo $totalMateri; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper stat-icon-indigo">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Simulasi CAT</h4>
                            <div class="stat-number"><?php echo $totalCat; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper stat-icon-emerald">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Total Siswa</h4>
                            <div class="stat-number"><?php echo $totalStudents; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon-wrapper stat-icon-amber">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div class="stat-info">
                            <h4>Izin Khusus</h4>
                            <div class="stat-number"><?php echo $customMateriCount + $customCatCount; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation Bar -->
                <div class="tabs-nav-bar">
                    <a href="?tab=materi" class="tab-btn <?php echo $activeTab === 'materi' ? 'active' : ''; ?>" onclick="switchTab('materi', event)">
                        <span>Izin Materi Pelajaran</span>
                        <span class="tab-badge"><?php echo $totalMateri; ?></span>
                    </a>
                    <a href="?tab=cat" class="tab-btn <?php echo $activeTab === 'cat' ? 'active' : ''; ?>" onclick="switchTab('cat', event)">
                        <span>Izin Simulasi CAT</span>
                        <span class="tab-badge"><?php echo $totalCat; ?></span>
                    </a>
                </div>

                <!-- ==================== TAB 1: IZIN MATERI ==================== -->
                <div id="tabContentMateri" style="display: <?php echo $activeTab === 'materi' ? 'block' : 'none'; ?>;">
                    <div class="k-card">
                        <div class="table-filter-bar">
                            <div class="search-box-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" placeholder="Cari materi / kategori..." onkeyup="filterTableRows(this, 'materiTableBody')">
                            </div>
                            <div style="font-size:13px; color:#64748b;">
                                Menampilkan <strong><?php echo count($materials); ?></strong> materi
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="k-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>JUDUL MATERI</th>
                                        <th>KATEGORI &amp; TIPE</th>
                                        <th>STATUS</th>
                                        <th>HAK AKSES SISWA</th>
                                        <th style="text-align:right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="materiTableBody">
                                    <?php if (empty($materials)): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-table-state">
                                                    Belum ada materi pelajaran. Buat materi baru di menu <a href="/admin/konten" style="color:#1d4ed8; font-weight:600;">Konten</a>.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($materials as $m): 
                                            $allowedArr = !empty(trim($m['allowed_students'] ?? '')) ? explode(',', $m['allowed_students']) : [];
                                            $allowedCount = count($allowedArr);
                                        ?>
                                            <tr data-search="<?php echo strtolower(htmlspecialchars($m['title'] . ' ' . ($m['category'] ?? '') . ' ' . ($m['type'] ?? ''))); ?>">
                                                <td><?php echo $no++; ?></td>
                                                <td>
                                                    <div class="item-title-bold"><?php echo htmlspecialchars($m['title']); ?></div>
                                                    <div class="item-subtext"><?php echo htmlspecialchars($m['description'] ?? '-'); ?></div>
                                                </td>
                                                <td>
                                                    <span style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($m['category'] ?? 'TWK'); ?></span>
                                                    <span style="color:#64748b; font-size:12px;"> • <?php echo ucfirst($m['type'] ?? 'Teks'); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge-status <?php echo ($m['status'] ?? 'aktif') === 'aktif' ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                                        <?php echo ucfirst($m['status'] ?? 'Aktif'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($allowedCount === 0): ?>
                                                        <span class="badge-access-all">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                            Semua Siswa Terdaftar (<?php echo $totalStudents; ?>)
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge-access-custom" title="Diizinkan untuk <?php echo $allowedCount; ?> siswa terpilih">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                            <?php echo $allowedCount; ?> Siswa Khusus
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <button type="button" class="btn-kelola-izin" onclick='openIzinModal(<?php echo $m['id']; ?>, "materi", <?php echo json_encode($allowedArr); ?>, <?php echo json_encode($m['title']); ?>)'>
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        <span>Kelola Izin</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== TAB 2: IZIN SIMULASI CAT ==================== -->
                <div id="tabContentCat" style="display: <?php echo $activeTab === 'cat' ? 'block' : 'none'; ?>;">
                    <div class="k-card">
                        <div class="table-filter-bar">
                            <div class="search-box-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <input type="text" placeholder="Cari simulasi CAT..." onkeyup="filterTableRows(this, 'catTableBody')">
                            </div>
                            <div style="font-size:13px; color:#64748b;">
                                Menampilkan <strong><?php echo count($cats); ?></strong> simulasi CAT
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="k-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NAMA SIMULASI CAT</th>
                                        <th>KATEGORI &amp; SOAL</th>
                                        <th>STATUS</th>
                                        <th>HAK AKSES SISWA</th>
                                        <th style="text-align:right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="catTableBody">
                                    <?php if (empty($cats)): ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-table-state">
                                                    Belum ada simulasi CAT. Buat CAT baru di menu <a href="/admin/konten?tab=cat" style="color:#1d4ed8; font-weight:600;">Konten</a>.
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($cats as $c): 
                                            $allowedArr = !empty(trim($c['allowed_students'] ?? '')) ? explode(',', $c['allowed_students']) : [];
                                            $allowedCount = count($allowedArr);
                                            $catStatus = $c['status'] ?? 'nonaktif';
                                        ?>
                                            <tr data-search="<?php echo strtolower(htmlspecialchars($c['exam_name'] . ' ' . ($c['category'] ?? ''))); ?>">
                                                <td><?php echo $no++; ?></td>
                                                <td>
                                                    <div class="item-title-bold"><?php echo htmlspecialchars($c['exam_name']); ?></div>
                                                    <div class="item-subtext">Durasi: <?php echo (int)($c['duration_minutes'] ?? 60); ?> menit</div>
                                                </td>
                                                <td>
                                                    <span style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($c['category'] ?? 'Umum'); ?></span>
                                                    <span style="color:#64748b; font-size:12px;"> • <?php echo (int)($c['total_questions'] ?? 0); ?> Soal</span>
                                                </td>
                                                <td>
                                                    <span class="badge-status <?php echo $catStatus === 'aktif' ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                                        <?php echo ucfirst($catStatus); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($allowedCount === 0): ?>
                                                        <span class="badge-access-all">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                            Semua Siswa Terdaftar (<?php echo $totalStudents; ?>)
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge-access-custom" title="Diizinkan untuk <?php echo $allowedCount; ?> siswa terpilih">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                            <?php echo $allowedCount; ?> Siswa Khusus
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <button type="button" class="btn-kelola-izin" onclick='openIzinModal(<?php echo $c['id']; ?>, "cat", <?php echo json_encode($allowedArr); ?>, <?php echo json_encode($c['exam_name']); ?>)'>
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        <span>Kelola Izin</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ==================== MODAL KELOLA IZIN SISWA ==================== -->
    <div class="modal-backdrop" id="izinModal">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h3 id="izinModalTitle">Kelola Izin Siswa</h3>
                    <div id="izinModalSubtitle" style="font-size:12px; color:#64748b; margin-top:2px;"></div>
                </div>
                <button class="modal-close-btn" onclick="closeModal('izinModal')">&times;</button>
            </div>
            <form action="/api/admin/konten/permissions" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="type" id="izinModalType">
                <input type="hidden" name="id" id="izinModalId">
                <input type="hidden" name="redirect_to" value="/admin/izin-konten">
                <div class="modal-body">
                    <p style="font-size:13px; color:#64748b; margin-top:0; line-height:1.5;">
                        Centang siswa yang diizinkan mengakses konten ini. <em>(Jika tidak ada siswa yang dicentang, maka otomatis dapat diakses oleh <strong>Semua Siswa</strong>)</em>.
                    </p>
                    <input type="text" class="student-search-input" placeholder="Cari nama siswa atau kode CP..." onkeyup="filterModalStudentList(this)">
                    <label class="student-checkbox-item" style="font-weight:700; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-bottom:8px;">
                        <input type="checkbox" onchange="toggleAllModalStudents(this)"> <span>Pilih Semua Siswa</span>
                    </label>
                    <div id="izinStudentList" style="max-height: 260px; overflow-y: auto;">
                        <?php foreach ($allStudents as $st): ?>
                            <label class="student-checkbox-item" data-name="<?php echo strtolower($st['name'] . ' ' . formatStudentCode($st)); ?>">
                                <input type="checkbox" name="allowed_students[]" value="<?php echo $st['id']; ?>" class="modal-student-checkbox">
                                <span><?php echo htmlspecialchars($st['name'] . ' ' . formatStudentCode($st)); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('izinModal')">Batal</button>
                    <button type="submit" class="btn-save">Simpan Izin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle Sidebar Group
        function toggleNavGroup(el) {
            const group = el.closest('.nav-group');
            group.classList.toggle('open');
        }

        // Tab Switching
        function switchTab(tab, e) {
            if (e) e.preventDefault();
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if (tab === 'materi') {
                document.querySelectorAll('.tab-btn')[0].classList.add('active');
                document.getElementById('tabContentMateri').style.display = 'block';
                document.getElementById('tabContentCat').style.display = 'none';
            } else {
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
                document.getElementById('tabContentMateri').style.display = 'none';
                document.getElementById('tabContentCat').style.display = 'block';
            }
            // Update URL without refresh
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        // Filter Table Rows
        function filterTableRows(input, bodyId) {
            const query = input.value.toLowerCase();
            const rows = document.querySelectorAll('#' + bodyId + ' tr[data-search]');
            rows.forEach(r => {
                const text = r.getAttribute('data-search') || '';
                if (text.includes(query)) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // Modal Handlers
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        function openIzinModal(id, type, allowedArray, title) {
            document.getElementById('izinModalId').value = id;
            document.getElementById('izinModalType').value = type;
            document.getElementById('izinModalTitle').textContent = type === 'materi' ? 'Kelola Izin Siswa: Materi' : 'Kelola Izin Siswa: Simulasi CAT';
            document.getElementById('izinModalSubtitle').textContent = title || '';

            const checkboxes = document.querySelectorAll('#izinStudentList input[type="checkbox"]');
            checkboxes.forEach(cb => {
                cb.checked = allowedArray.includes(String(cb.value)) || allowedArray.includes(Number(cb.value));
            });

            document.getElementById('izinModal').classList.add('open');
        }

        function toggleAllModalStudents(master) {
            const checkboxes = document.querySelectorAll('#izinStudentList input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = master.checked);
        }

        function filterModalStudentList(input) {
            const term = input.value.toLowerCase();
            const items = document.querySelectorAll('#izinStudentList .student-checkbox-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name') || item.textContent.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>