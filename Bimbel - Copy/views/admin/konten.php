<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

// Auto seed default sample if exam_sessions is empty
$checkCat = $pdo->query("SELECT COUNT(*) FROM exam_sessions")->fetchColumn();
if ((int) $checkCat === 0) {
    try {
        $pdo->exec("INSERT INTO exam_sessions (exam_name, category, description, duration_minutes, total_questions, passing_score, show_discussion, status) 
                    VALUES ('Analogi verbal', 'Psikotes', 'Simulasi tes analogi verbal untuk psikotes', 60, 0, 70, 1, 'nonaktif')");
    } catch (\Throwable $e) {
        // ignore if fails
    }
}

// Get all students
$allStudents = $pdo->query("SELECT id, name, nickname, created_at FROM users WHERE role = 'siswa' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Helper for CP student code
function formatStudentCode($user) {
    $dt = !empty($user['created_at']) ? date('ymd', strtotime($user['created_at'])) : '260908';
    $seed = ($user['id'] * 179 + 6795) % 10000;
    return 'CP' . $dt . str_pad($seed, 4, '0', STR_PAD_LEFT);
}

// Get materials
$materials = $pdo->query("SELECT * FROM materials ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get CAT simulations
$cats = $pdo->query("SELECT * FROM exam_sessions ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Active Tab
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'cat' ? 'cat' : 'materi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konten - Admin Bimbel Alahaido</title>
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
            --card-border: #e2e8f0;
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
            min-width: 0;
            overflow-x: hidden;
        }

        .content-container {
            padding: 28px 36px 60px;
            max-width: 1320px;
            width: 100%;
        }

        /* Top Page Header */
        .page-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin: 0;
            font-weight: 400;
        }

        .btn-izin-konten {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ffffff;
            color: #1d4ed8;
            border: 1.5px solid #2563eb;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            white-space: nowrap;
        }

        .btn-izin-konten:hover {
            background: #eff6ff;
            border-color: #1d4ed8;
            color: #1e40af;
        }

        .btn-izin-konten svg {
            width: 16px;
            height: 16px;
        }

        /* ===== TABS NAVIGATION ===== */
        .tabs-nav-bar {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 8px;
            gap: 4px;
            margin-bottom: 24px;
        }

        .tab-btn {
            border: none;
            background: transparent;
            padding: 7px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: #64748b;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-block;
        }

        .tab-btn:hover {
            color: #0f172a;
        }

        .tab-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* ===== 2-COLUMN GRID LAYOUT ===== */
        .konten-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
            align-items: flex-start;
        }

        @media (max-width: 1080px) {
            .konten-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .k-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            padding: 22px 24px;
        }

        .k-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 20px 0;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 9px 12px;
            font-size: 13.5px;
            color: #1e293b;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            font-family: inherit;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 18px 0 20px;
            cursor: pointer;
        }

        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .form-check label {
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .btn-primary-action {
            display: block;
            width: 100%;
            padding: 10px 16px;
            background: #1d4ed8;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-primary-action:hover {
            background: #1e40af;
        }

        /* Student Selection Dropdown / Box */
        .student-select-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            position: relative;
        }

        .student-select-header {
            padding: 9px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 13.5px;
            color: #64748b;
            user-select: none;
        }

        .student-select-header svg {
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
        }

        .student-select-box.open .student-select-header svg {
            transform: rotate(180deg);
        }

        .student-dropdown-list {
            border-top: 1px solid #e2e8f0;
            max-height: 220px;
            overflow-y: auto;
            padding: 8px 12px;
            background: #ffffff;
            display: none;
        }

        .student-select-box.open .student-dropdown-list {
            display: block;
        }

        .student-search-input {
            width: 100%;
            padding: 6px 10px;
            font-size: 12.5px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 8px;
            outline: none;
        }

        .student-checkbox-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 5px 4px;
            font-size: 12.5px;
            color: #334155;
            cursor: pointer;
            border-radius: 4px;
        }

        .student-checkbox-item:hover {
            background: #f8fafc;
        }

        .student-checkbox-item input {
            accent-color: #2563eb;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        /* ===== TABLE STYLES ===== */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .k-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .k-table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            background: transparent;
        }

        .k-table td {
            padding: 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .k-table tbody tr:hover {
            background: #f8fafc;
        }

        .item-title-bold {
            font-weight: 700;
            color: #0f172a;
            font-size: 13.5px;
            margin-bottom: 3px;
        }

        .item-subtext {
            font-size: 12px;
            color: #64748b;
        }

        /* Badges */
        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 600;
            text-align: center;
        }

        .badge-aktif {
            background: #16a34a;
            color: #ffffff;
        }

        .badge-nonaktif {
            background: #334155;
            color: #ffffff;
        }

        /* Action Buttons in Table */
        .action-btns-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .btn-table-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 11px;
            background: #ffffff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-table-action:hover {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .btn-table-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 11px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-table-toggle:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-table-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 8px;
            background: #ffffff;
            border: 1px solid #fecaca;
            color: #ef4444;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-table-delete:hover {
            background: #fef2f2;
            border-color: #f87171;
        }

        .empty-table-state {
            text-align: center;
            padding: 48px 20px;
            color: #64748b;
            font-size: 13.5px;
        }

        /* ===== ALERT BANNERS ===== */
        .alert-banner {
            padding: 12px 18px;
            border-radius: 8px;
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

        /* ===== MODAL STYLES ===== */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 14px;
            width: 100%;
            max-width: 580px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
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
            background: transparent;
            border: none;
            font-size: 20px;
            color: #94a3b8;
            cursor: pointer;
            line-height: 1;
        }

        .modal-close-btn:hover {
            color: #0f172a;
        }

        .modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 14px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }

        .btn-cancel {
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-save {
            padding: 8px 18px;
            background: #1d4ed8;
            border: none;
            color: #ffffff;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-save:hover {
            background: #1e40af;
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

                <!-- Group Materi (Open & Active) -->
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
                        <a href="/admin/izin-konten" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                            <span>Izin Konten</span>
                        </a>
                        <a href="/admin/konten" class="nav-sub-item active">
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
                        <h1 class="page-title">Konten</h1>
                        <p class="page-subtitle">Materi dan CAT di halaman ini hanya milik lembaga Anda. Lembaga lain tidak dapat mengelolanya.</p>
                    </div>
                    <div>
                        <a href="/admin/izin-konten" class="btn-izin-konten">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>Izin Konten</span>
                        </a>
                    </div>
                </div>

                <!-- Tabs Navigation Bar -->
                <div class="tabs-nav-bar">
                    <a href="?tab=materi" class="tab-btn <?php echo $activeTab === 'materi' ? 'active' : ''; ?>" onclick="switchTab('materi', event)">Materi Pelajaran</a>
                    <a href="?tab=cat" class="tab-btn <?php echo $activeTab === 'cat' ? 'active' : ''; ?>" onclick="switchTab('cat', event)">Simulasi CAT</a>
                </div>

                <!-- ==================== TAB 1: MATERI PELAJARAN ==================== -->
                <div id="tabContentMateri" style="display: <?php echo $activeTab === 'materi' ? 'block' : 'none'; ?>;">
                    <div class="konten-grid">
                        <!-- Left: Tambah Materi Form -->
                        <div class="k-card">
                            <h2 class="k-card-title">Tambah Materi</h2>
                            <form action="/api/admin/materi/save" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                
                                <div class="form-group">
                                    <label>Judul</label>
                                    <input type="text" name="title" class="form-control" placeholder="Judul materi" required>
                                </div>

                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="category" class="form-select">
                                        <option value="TWK" selected>TWK</option>
                                        <option value="TIU">TIU</option>
                                        <option value="TKP">TKP</option>
                                        <option value="Psikotes">Psikotes</option>
                                        <option value="Fisik">Fisik</option>
                                        <option value="Akademik">Akademik</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Keterangan singkat materi"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Jenis Materi</label>
                                    <select name="type" class="form-select" id="materiTypeSelect" onchange="toggleMateriType(this.value)">
                                        <option value="teks" selected>Teks</option>
                                        <option value="video">Video / Link</option>
                                        <option value="pdf">Dokumen / PDF</option>
                                    </select>
                                </div>

                                <div class="form-group" id="materiContentGroup">
                                    <label id="materiContentLabel">Isi Materi</label>
                                    <textarea name="content" class="form-control" rows="5" placeholder="Tuliskan isi materi di sini..."></textarea>
                                </div>

                                <div class="form-group" id="materiFileGroup" style="display:none;">
                                    <label>Upload File (PDF / Dokumen)</label>
                                    <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
                                </div>

                                <div class="form-group">
                                    <label>Izin Siswa</label>
                                    <div class="student-select-box" id="studentSelectBoxMateri">
                                        <div class="student-select-header" onclick="toggleStudentSelect('studentSelectBoxMateri')">
                                            <span id="selectedCountMateri">Pilih Siswa</span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 12 15 18 9"/></svg>
                                        </div>
                                        <div class="student-dropdown-list">
                                            <input type="text" class="student-search-input" placeholder="Cari nama siswa..." onkeyup="filterStudentList(this, 'studentListMateri')">
                                            <label class="student-checkbox-item" style="font-weight:600; border-bottom:1px solid #f1f5f9; padding-bottom:6px; margin-bottom:4px;">
                                                <input type="checkbox" onchange="toggleAllStudents(this, 'studentListMateri', 'selectedCountMateri')"> Pilih Semua
                                            </label>
                                            <div id="studentListMateri">
                                                <?php foreach ($allStudents as $st): ?>
                                                    <label class="student-checkbox-item" data-name="<?php echo strtolower($st['name']); ?>">
                                                        <input type="checkbox" name="allowed_students[]" value="<?php echo $st['id']; ?>" onchange="updateSelectedCount('studentListMateri', 'selectedCountMateri')">
                                                        <span><?php echo htmlspecialchars($st['name'] . ' ' . formatStudentCode($st)); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary-action">Tambah Materi</button>
                            </form>
                        </div>

                        <!-- Right: Tabel Materi -->
                        <div class="k-card">
                            <h2 class="k-card-title">Materi</h2>
                            <div class="table-responsive">
                                <table class="k-table">
                                    <thead>
                                        <tr>
                                            <th>MATERI</th>
                                            <th>IZIN</th>
                                            <th>STATUS</th>
                                            <th style="text-align:right;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($materials)): ?>
                                            <tr>
                                                <td colspan="4">
                                                    <div class="empty-table-state">
                                                        Belum ada materi buatan lembaga.
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($materials as $m): 
                                                $allowedCount = !empty($m['allowed_students']) ? count(explode(',', $m['allowed_students'])) : 0;
                                                $izinText = $allowedCount === 0 ? 'Semua siswa' : ($allowedCount . ' siswa');
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="item-title-bold"><?php echo htmlspecialchars($m['title']); ?></div>
                                                        <div class="item-subtext"><?php echo htmlspecialchars($m['category'] ?? 'TWK'); ?> • <?php echo ucfirst($m['type'] ?? 'Teks'); ?></div>
                                                    </td>
                                                    <td><?php echo $izinText; ?></td>
                                                    <td>
                                                        <span class="badge-status <?php echo ($m['status'] ?? 'aktif') === 'aktif' ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                                            <?php echo ucfirst($m['status'] ?? 'Aktif'); ?>
                                                        </span>
                                                    </td>
                                                    <td style="text-align:right;">
                                                        <div class="action-btns-group" style="justify-content: flex-end;">
                                                            <button type="button" class="btn-table-action" onclick='openPreviewModal(<?php echo json_encode($m); ?>, "materi")'>Preview</button>
                                                            <button type="button" class="btn-table-action" onclick='openEditMateriModal(<?php echo json_encode($m); ?>)'>Edit</button>
                                                            <button type="button" class="btn-table-action" onclick='openIzinModal(<?php echo $m['id']; ?>, "materi", <?php echo json_encode(!empty($m['allowed_students']) ? explode(',', $m['allowed_students']) : []); ?>)'>Izin</button>
                                                            <form action="/api/admin/materi/toggle" method="POST" style="display:inline;">
                                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                                <button type="submit" class="btn-table-toggle">Aktif/Nonaktif</button>
                                                            </form>
                                                            <form action="/api/admin/materi/delete" method="POST" style="display:inline;" onsubmit="return confirm('Hapus materi ini?');">
                                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                                                <button type="submit" class="btn-table-delete" title="Hapus">&times;</button>
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
                </div>

                <!-- ==================== TAB 2: SIMULASI CAT ==================== -->
                <div id="tabContentCat" style="display: <?php echo $activeTab === 'cat' ? 'block' : 'none'; ?>;">
                    <div class="konten-grid">
                        <!-- Left: Buat Simulasi CAT Form -->
                        <div class="k-card">
                            <h2 class="k-card-title">Buat Simulasi CAT</h2>
                            <form action="/api/admin/cat/save" method="POST">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                
                                <div class="form-group">
                                    <label>Judul CAT</label>
                                    <input type="text" name="exam_name" class="form-control" placeholder="Judul simulasi CAT" required>
                                </div>

                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="category" class="form-select">
                                        <option value="Umum" selected>Umum</option>
                                        <option value="Psikotes">Psikotes</option>
                                        <option value="TWK">TWK</option>
                                        <option value="TIU">TIU</option>
                                        <option value="TKP">TKP</option>
                                        <option value="Akademik">Akademik</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Keterangan simulasi CAT"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Durasi (menit)</label>
                                    <input type="number" name="duration_minutes" class="form-control" value="60" min="1" required>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="show_discussion" id="showDiscussionCheck" value="1" checked>
                                    <label for="showDiscussionCheck">Tampilkan pembahasan setelah selesai</label>
                                </div>

                                <button type="submit" class="btn-primary-action">Buat CAT</button>
                            </form>
                        </div>

                        <!-- Right: Tabel Simulasi CAT -->
                        <div class="k-card">
                            <h2 class="k-card-title">Simulasi CAT</h2>
                            <div class="table-responsive">
                                <table class="k-table">
                                    <thead>
                                        <tr>
                                            <th>CAT</th>
                                            <th>SOAL</th>
                                            <th>IZIN</th>
                                            <th>STATUS</th>
                                            <th style="text-align:right;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($cats)): ?>
                                            <tr>
                                                <td colspan="5">
                                                    <div class="empty-table-state">
                                                        Belum ada simulasi CAT buatan lembaga.
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($cats as $c): 
                                                $allowedCount = !empty($c['allowed_students']) ? count(explode(',', $c['allowed_students'])) : 0;
                                                $catStatus = $c['status'] ?? 'nonaktif';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="item-title-bold"><?php echo htmlspecialchars($c['exam_name']); ?></div>
                                                        <div class="item-subtext"><?php echo htmlspecialchars($c['category'] ?? 'Umum'); ?> • <?php echo (int)($c['duration_minutes'] ?? 60); ?> menit</div>
                                                    </td>
                                                    <td><?php echo (int)($c['total_questions'] ?? 0); ?></td>
                                                    <td><?php echo $allowedCount; ?> siswa</td>
                                                    <td>
                                                        <span class="badge-status <?php echo $catStatus === 'aktif' ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                                            <?php echo ucfirst($catStatus); ?>
                                                        </span>
                                                    </td>
                                                    <td style="text-align:right;">
                                                        <div class="action-btns-group" style="justify-content: flex-end;">
                                                            <button type="button" class="btn-table-action" onclick='openPreviewModal(<?php echo json_encode($c); ?>, "cat")'>Preview</button>
                                                            <a href="/admin/soal" class="btn-table-action">Kelola Soal</a>
                                                            <button type="button" class="btn-table-action" onclick='openEditCatModal(<?php echo json_encode($c); ?>)'>Edit</button>
                                                            <button type="button" class="btn-table-action" onclick='openIzinModal(<?php echo $c['id']; ?>, "cat", <?php echo json_encode(!empty($c['allowed_students']) ? explode(',', $c['allowed_students']) : []); ?>)'>Izin</button>
                                                            <form action="/api/admin/cat/toggle" method="POST" style="display:inline;">
                                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                                <button type="submit" class="btn-table-toggle">Aktif/Nonaktif</button>
                                                            </form>
                                                            <form action="/api/admin/cat/delete" method="POST" style="display:inline;" onsubmit="return confirm('Hapus simulasi CAT ini?');">
                                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                                <button type="submit" class="btn-table-delete" title="Hapus">&times;</button>
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
                </div>

            </div>
        </main>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Modal Preview -->
    <div class="modal-backdrop" id="previewModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="previewModalTitle">Preview Konten</h3>
                <button class="modal-close-btn" onclick="closeModal('previewModal')">&times;</button>
            </div>
            <div class="modal-body" id="previewModalBody">
                <!-- Content will be injected -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('previewModal')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Materi -->
    <div class="modal-backdrop" id="editMateriModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Materi</h3>
                <button class="modal-close-btn" onclick="closeModal('editMateriModal')">&times;</button>
            </div>
            <form action="/api/admin/materi/update" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="id" id="editMateriId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="editMateriTitle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" id="editMateriCategory" class="form-select">
                            <option value="TWK">TWK</option>
                            <option value="TIU">TIU</option>
                            <option value="TKP">TKP</option>
                            <option value="Psikotes">Psikotes</option>
                            <option value="Fisik">Fisik</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Umum">Umum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" id="editMateriDesc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Isi Materi</label>
                        <textarea name="content" id="editMateriContent" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editMateriModal')">Batal</button>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit CAT -->
    <div class="modal-backdrop" id="editCatModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Simulasi CAT</h3>
                <button class="modal-close-btn" onclick="closeModal('editCatModal')">&times;</button>
            </div>
            <form action="/api/admin/cat/update" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="id" id="editCatId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul CAT</label>
                        <input type="text" name="exam_name" id="editCatName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" id="editCatCategory" class="form-select">
                            <option value="Umum">Umum</option>
                            <option value="Psikotes">Psikotes</option>
                            <option value="TWK">TWK</option>
                            <option value="TIU">TIU</option>
                            <option value="TKP">TKP</option>
                            <option value="Akademik">Akademik</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" id="editCatDesc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Durasi (menit)</label>
                        <input type="number" name="duration_minutes" id="editCatDuration" class="form-control" required min="1">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="show_discussion" id="editCatShowDiscussion" value="1">
                        <label for="editCatShowDiscussion">Tampilkan pembahasan setelah selesai</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editCatModal')">Batal</button>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Izin Siswa -->
    <div class="modal-backdrop" id="izinModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="izinModalTitle">Kelola Izin Siswa</h3>
                <button class="modal-close-btn" onclick="closeModal('izinModal')">&times;</button>
            </div>
            <form action="/api/admin/konten/permissions" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="type" id="izinModalType">
                <input type="hidden" name="id" id="izinModalId">
                <input type="hidden" name="redirect_to" value="/admin/konten">
                <div class="modal-body">
                    <p style="font-size:13px; color:#64748b; margin-top:0;">Centang siswa yang diizinkan mengakses konten ini:</p>
                    <input type="text" class="student-search-input" placeholder="Cari nama siswa..." onkeyup="filterStudentList(this, 'izinStudentList')">
                    <label class="student-checkbox-item" style="font-weight:600; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-bottom:8px;">
                        <input type="checkbox" onchange="toggleAllModalStudents(this)"> Pilih Semua Siswa
                    </label>
                    <div id="izinStudentList" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($allStudents as $st): ?>
                            <label class="student-checkbox-item" data-name="<?php echo strtolower($st['name']); ?>">
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

        // Materi Type Switcher
        function toggleMateriType(val) {
            const contentGroup = document.getElementById('materiContentGroup');
            const fileGroup = document.getElementById('materiFileGroup');
            const contentLabel = document.getElementById('materiContentLabel');

            if (val === 'pdf') {
                contentGroup.style.display = 'none';
                fileGroup.style.display = 'block';
            } else if (val === 'video') {
                contentGroup.style.display = 'block';
                fileGroup.style.display = 'none';
                contentLabel.textContent = 'Link Video (YouTube / Google Drive)';
            } else {
                contentGroup.style.display = 'block';
                fileGroup.style.display = 'none';
                contentLabel.textContent = 'Isi Materi';
            }
        }

        // Student Select Dropdown Box
        function toggleStudentSelect(boxId) {
            const box = document.getElementById(boxId);
            box.classList.toggle('open');
        }

        function filterStudentList(input, listId) {
            const term = input.value.toLowerCase();
            const items = document.querySelectorAll('#' + listId + ' .student-checkbox-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name') || item.textContent.toLowerCase();
                if (name.includes(term)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function toggleAllStudents(master, listId, countLabelId) {
            const checkboxes = document.querySelectorAll('#' + listId + ' input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = master.checked);
            updateSelectedCount(listId, countLabelId);
        }

        function updateSelectedCount(listId, countLabelId) {
            const checked = document.querySelectorAll('#' + listId + ' input[type="checkbox"]:checked').length;
            const label = document.getElementById(countLabelId);
            if (label) {
                label.textContent = checked === 0 ? 'Pilih Siswa' : (checked + ' Siswa Dipilih');
            }
        }

        // Modal Handlers
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        function openPreviewModal(data, type) {
            const titleEl = document.getElementById('previewModalTitle');
            const bodyEl = document.getElementById('previewModalBody');

            if (type === 'materi') {
                titleEl.textContent = 'Preview Materi: ' + (data.title || '');
                bodyEl.innerHTML = `
                    <div style="margin-bottom:14px;">
                        <span class="badge-status badge-aktif">${data.category || 'TWK'}</span>
                        <span style="font-size:12px; color:#64748b; margin-left:8px;">${data.type || 'Teks'}</span>
                    </div>
                    <div style="font-size:14px; font-weight:600; color:#0f172a; margin-bottom:8px;">Keterangan:</div>
                    <p style="font-size:13.5px; color:#475569; margin-top:0;">${data.description || 'Tidak ada keterangan.'}</p>
                    <div style="font-size:14px; font-weight:600; color:#0f172a; margin-bottom:8px;">Isi Materi:</div>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px; font-size:13.5px; white-space:pre-wrap; color:#334155;">${data.content || 'Belum ada konten teks.'}</div>
                `;
            } else {
                titleEl.textContent = 'Preview CAT: ' + (data.exam_name || '');
                bodyEl.innerHTML = `
                    <div style="margin-bottom:14px;">
                        <span class="badge-status badge-aktif">${data.category || 'Umum'}</span>
                        <span style="font-size:12px; color:#64748b; margin-left:8px;">Durasi: ${data.duration_minutes || 60} Menit</span>
                    </div>
                    <div style="font-size:14px; font-weight:600; color:#0f172a; margin-bottom:8px;">Keterangan:</div>
                    <p style="font-size:13.5px; color:#475569; margin-top:0;">${data.description || 'Tidak ada keterangan.'}</p>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px; font-size:13.5px; color:#334155;">
                        <p style="margin:4px 0;"><strong>Jumlah Soal:</strong> ${data.total_questions || 0} Soal</p>
                        <p style="margin:4px 0;"><strong>Tampilkan Pembahasan:</strong> ${data.show_discussion ? 'Ya' : 'Tidak'}</p>
                        <p style="margin:4px 0;"><strong>Status:</strong> ${data.status || 'Nonaktif'}</p>
                    </div>
                `;
            }

            document.getElementById('previewModal').classList.add('open');
        }

        function openEditMateriModal(data) {
            document.getElementById('editMateriId').value = data.id;
            document.getElementById('editMateriTitle').value = data.title || '';
            document.getElementById('editMateriCategory').value = data.category || 'TWK';
            document.getElementById('editMateriDesc').value = data.description || '';
            document.getElementById('editMateriContent').value = data.content || '';
            document.getElementById('editMateriModal').classList.add('open');
        }

        function openEditCatModal(data) {
            document.getElementById('editCatId').value = data.id;
            document.getElementById('editCatName').value = data.exam_name || '';
            document.getElementById('editCatCategory').value = data.category || 'Umum';
            document.getElementById('editCatDesc').value = data.description || '';
            document.getElementById('editCatDuration').value = data.duration_minutes || 60;
            document.getElementById('editCatShowDiscussion').checked = data.show_discussion == 1;
            document.getElementById('editCatModal').classList.add('open');
        }

        function openIzinModal(id, type, allowedArray) {
            document.getElementById('izinModalId').value = id;
            document.getElementById('izinModalType').value = type;
            document.getElementById('izinModalTitle').textContent = type === 'materi' ? 'Kelola Izin Siswa Materi' : 'Kelola Izin Siswa Simulasi CAT';

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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const box = document.getElementById('studentSelectBoxMateri');
            if (box && !box.contains(e.target)) {
                box.classList.remove('open');
            }
        });
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
