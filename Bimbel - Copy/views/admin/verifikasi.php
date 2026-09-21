<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();
$summary = [
    'pending' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'pending'")->fetchColumn(),
    'active' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'active'")->fetchColumn(),
    'total' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn(),
];
$pendingUsers = $pdo->query("SELECT u.*, p.proof_file, p.amount FROM users u LEFT JOIN payments p ON p.user_id = u.id WHERE u.role = 'siswa' AND u.status = 'pending' ORDER BY u.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - Admin</title>
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
            max-width: 1200px;
            width: 100%;
        }

        /* ===== DASHBOARD NEW LAYOUT ===== */
        .db-profile-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 18px;
        }
        .db-profile-icon {
            width: 52px; height: 52px;
            background: #eff6ff;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .db-profile-icon svg { width:26px; height:26px; stroke:#1d4ed8; }
        .db-profile-meta { font-size: 12px; color: #64748b; font-weight:500; margin-bottom: 4px; }
        .db-profile-title {
            font-size: 20px; font-weight: 800; color: #0f172a;
            display: flex; align-items: center; gap: 10px; margin-bottom: 4px;
        }
        .db-profile-badge {
            background: #dbeafe; color: #1d4ed8;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 6px;
            letter-spacing: 0.3px;
        }
        .db-profile-desc { font-size: 13px; color: #64748b; margin: 0; }

        /* Share link banner */
        .db-link-banner {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .db-link-left { display:flex; align-items:flex-start; gap:12px; }
        .db-link-left svg { width:20px; height:20px; stroke:#0f172a; flex-shrink:0; margin-top:2px; }
        .db-link-title { font-size:14px; font-weight:700; color:#0f172a; margin:0 0 2px; }
        .db-link-desc { font-size:12.5px; color:#64748b; margin:0; }
        .db-link-desc a { color:#1d4ed8; }
        .btn-share-link {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px;
            background: #0f172a; color: #fff;
            border: none; border-radius: 9px;
            font-size: 13.5px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            white-space: nowrap;
            transition: background 0.2s;
        }
        .btn-share-link:hover { background: #1e293b; }
        .btn-share-link svg { width:16px; height:16px; stroke:#fff; }

        /* Stat boxes */
        .db-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .db-stat-box {
            padding: 22px 24px;
            border-right: 1px solid #e2e8f0;
        }
        .db-stat-box:last-child { border-right: none; }
        .db-stat-icon { margin-bottom: 8px; }
        .db-stat-icon svg { width: 24px; height: 24px; stroke: #334155; stroke-width: 1.8; }
        .db-stat-label { font-size: 13px; color: #64748b; margin: 0 0 4px; font-weight: 500; }
        .db-stat-value { font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; }

        /* Quick action buttons */
        .db-actions-row { display:flex; gap:10px; margin-bottom: 20px; }
        .btn-action-outline {
            padding: 8px 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: #0f172a;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: all 0.15s;
        }
        .btn-action-outline:hover { border-color: #1d4ed8; color: #1d4ed8; background: #eff6ff; }

        /* Data Siswa table card */
        .db-table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
        }
        .db-table-header {
            padding: 20px 24px 0;
        }
        .db-table-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 16px; font-weight: 800; color: #0f172a;
            margin: 0 0 14px;
        }
        .db-table-title svg { width:20px; height:20px; stroke:#0f172a; }
        .db-search-row {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
            margin-bottom: 16px;
        }
        .db-search-label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 5px; }
        .db-search-wrap {
            display: flex; align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            overflow: hidden;
            background: #fff;
            width: 280px;
        }
        .db-search-wrap svg { width:15px; height:15px; stroke:#94a3b8; margin-left:12px; flex-shrink:0; }
        .db-search-input {
            border: none; outline: none;
            padding: 9px 12px;
            font-size: 13.5px; color: #0f172a;
            width: 100%;
            background: transparent;
        }
        .db-count-badge {
            font-size: 12px; font-weight: 600; color: #475569;
            background: #f1f5f9; padding: 5px 12px; border-radius: 8px;
        }
        .db-siswa-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .db-siswa-table th {
            padding: 10px 14px;
            font-size: 11px; font-weight: 700;
            color: #64748b; letter-spacing: 0.5px;
            text-transform: uppercase;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            text-align: left;
        }
        .db-siswa-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .db-siswa-table tbody tr:hover { background: #f8fafc; }
        .db-siswa-table .name-bold { font-weight: 700; color: #0f172a; }
        .badge-aktif {
            background: #16a34a; color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 6px;
            white-space: nowrap;
        }
        .badge-pending {
            background: #f59e0b; color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 6px;
            white-space: nowrap;
        }
        .badge-inactive {
            background: #94a3b8; color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 6px;
            white-space: nowrap;
        }
        .db-siswa-table .aksi-cell { color: #94a3b8; font-size: 14px; }
        .db-siswa-table .aksi-cell a { color: #1d4ed8; text-decoration: none; font-size: 12px; font-weight: 600; margin-right: 6px; }
        .db-table-empty { text-align:center; padding:40px; color:#94a3b8; font-size:14px; }

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
                        <a class="nav-sub-item  active" href="/admin/verifikasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="">Verifikasi Akun</span>
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


                <!-- Group Operasional (Expanded) -->
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
                        <!-- <a href="/admin/verifikasi" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <span>Pembayaran</span>
                        </a> -->
                        <a href="/admin/nilai" class="nav-sub-item ">
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
                        <!-- <a href="/admin/verifikasi" class="nav-sub-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <span>Pembayaran</span>
                        </a> -->
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
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Verifikasi Akun</h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

            <div class="content-area">
                <?php if (isset($_GET['success'])): ?>
                    <div class="card" style="background:#ecfdf5; border-left:4px solid #16a34a; color:#166534;">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="card" style="background:#fef2f2; border-left:4px solid #dc2626; color:#991b1b;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>Menunggu Verifikasi</h3>
                        <div class="big"><?php echo $summary['pending']; ?></div>
                    </div>
                    <div class="stat-box">
                        <h3>Sudah Aktif</h3>
                        <div class="big" style="color:#16a34a;"><?php echo $summary['active']; ?></div>
                    </div>
                    <div class="stat-box">
                        <h3>Total Siswa</h3>
                        <div class="big"><?php echo $summary['total']; ?></div>
                    </div>
                </div>

                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
                        <h2 style="margin:0;">Daftar Siswa Baru</h2>
                        <span class="badge badge-warning">Review</span>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Email</th>
                                <th>Bukti Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingUsers)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; color:#6b7280; padding:30px;">Belum ada siswa yang mendaftar untuk diverifikasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingUsers as $user): ?>
                                    <?php $proof = trim((string)($user['proof_file'] ?? '')); ?>
                                    <?php
                                        $proofUrl = '/assets/images/default-payment.png';
                                        if ($proof !== '') {
                                            $proofPath = $proof;
                                            if (strpos($proofPath, 'public/') === 0) {
                                                $proofPath = substr($proofPath, strlen('public/'));
                                            }
                                            if (strpos($proofPath, 'uploads/') === 0) {
                                                $proofUrl = '/' . $proofPath;
                                            } else {
                                                $proofUrl = '/' . ltrim($proofPath, '/');
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name'] ?? '-'); ?><br><small style="color:#6b7280;"><?php echo htmlspecialchars($user['nickname'] ?? ''); ?></small></td>
                                        <td><?php echo htmlspecialchars($user['whatsapp'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($proof !== ''): ?>
                                                <div class="proof-container" style="display:flex; flex-direction:column; gap:6px; align-items:flex-start;">
                                                    <div class="proof-wrapper" style="position:relative; display:inline-block; cursor:zoom-in; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#f8fafc;"
                                                         data-img="<?php echo htmlspecialchars($proofUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                                         data-name="<?php echo htmlspecialchars($user['name'] ?? 'Siswa', ENT_QUOTES, 'UTF-8'); ?>"
                                                         data-info="<?php echo htmlspecialchars(($user['nickname'] ? $user['nickname'] . ' • ' : '') . ($user['whatsapp'] ?? '') . ($user['email'] ? ' • ' . $user['email'] : ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                         data-amount="<?php echo number_format($user['amount'] ?? 100000, 0, ',', '.'); ?>"
                                                         onclick="handleProofClick(this)"
                                                         title="Klik untuk memperbesar bukti pembayaran">
                                                        <img class="proof-thumb" src="<?php echo htmlspecialchars($proofUrl); ?>" alt="Bukti transfer <?php echo htmlspecialchars($user['name'] ?? ''); ?>" style="display:block; width:120px; height:80px; object-fit:cover;">
                                                        <div class="proof-hover-hint" style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; background:rgba(13,34,64,0.72); color:#fff; opacity:0; transition:opacity 0.2s ease; font-size:11px; font-weight:600;">
                                                            <span>Perbesar</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-preview-link" style="display:inline-flex; align-items:center; gap:6px; padding:5px 8px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; color:#1d4ed8; font-size:11px; font-weight:600; cursor:pointer;"
                                                            data-img="<?php echo htmlspecialchars($proofUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-name="<?php echo htmlspecialchars($user['name'] ?? 'Siswa', ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-info="<?php echo htmlspecialchars(($user['nickname'] ? $user['nickname'] . ' • ' : '') . ($user['whatsapp'] ?? '') . ($user['email'] ? ' • ' . $user['email'] : ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-amount="<?php echo number_format($user['amount'] ?? 100000, 0, ',', '.'); ?>"
                                                            onclick="handleProofClick(this)">Lihat Bukti</button>
                                                </div>
                                            <?php else: ?>
                                                <span style="color:#6b7280;">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>
                                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                <form method="POST" action="/api/admin/verify">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-success">Setujui</button>
                                                </form>
                                                <form method="POST" action="/api/admin/verify" class="confirm-delete-form">
                                                    <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data siswa ini? Tindakan ini tidak bisa dibatalkan.')">Hapus</button>
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
        </main>
    </div>

   

    <div id="proofLightboxModal" class="proof-modal" onclick="handleBackdropClick(event)" style="position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.75); display:flex; align-items:center; justify-content:center; opacity:0; visibility:hidden; pointer-events:none; transition:all 0.2s ease;">
        <div class="proof-modal-card" onclick="event.stopPropagation()" style="background:#fff; width:min(920px, 92vw); border-radius:18px; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,0.35);">
            <div class="proof-modal-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; background:#0d2240; color:#fff;">
                <div>
                    <div style="font-weight:700;"> <span id="modalStudentName">Bukti Pembayaran</span></div>
                    <div id="modalStudentInfo" style="color:#bfdbfe; font-size:12px; margin-top:4px;">Calon Siswa</div>
                </div>
                <button type="button" class="proof-modal-close" onclick="closeProofModal()" style="background:transparent; border:none; color:#fff; font-size:28px; cursor:pointer;">&times;</button>
            </div>
            <div class="proof-modal-body" style="padding:18px; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
                <img id="modalProofImg" src="" alt="Bukti Pembayaran" style="max-width:100%; max-height:70vh; border-radius:12px; border:1px solid #e5e7eb; background:#fff;">
            </div>
            <div class="proof-modal-footer" style="padding:14px 20px; display:flex; justify-content:space-between; align-items:center; gap:12px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap;">
                <span id="modalAmountBadge" class="modal-amount-badge" style="display:inline-block; padding:6px 12px; border-radius:999px; background:#ecfdf5; color:#047857; font-weight:700; font-size:12px;">Biaya: Rp 100.000</span>
                <div style="display:flex; align-items:center; gap:10px;">
                    <a id="modalOpenTabBtn" href="#" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:8px; border:1px solid #cbd5e1; color:#0f172a; background:#fff; text-decoration:none; font-size:12px; font-weight:600;">Buka Ukuran Asli</a>
                    <button type="button" class="btn-modal-close" onclick="closeProofModal()" style="padding:8px 12px; border-radius:8px; border:none; background:#0d2240; color:#fff; font-weight:600; cursor:pointer;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }
        updateTime();
        setInterval(updateTime, 1000);

        document.querySelectorAll('.confirm-delete-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                const confirmed = confirm('Yakin ingin menghapus data siswa ini? Semua data pendaftaran dan bukti pembayaran akan dihapus.');
                if (!confirmed) {
                    event.preventDefault();
                }
            });
        });

        function handleProofClick(el) {
            const img = el.getAttribute('data-img');
            const name = el.getAttribute('data-name');
            const info = el.getAttribute('data-info');
            const amount = el.getAttribute('data-amount');
            openProofModal(img, name, info, amount);
        }

        function openProofModal(imageUrl, studentName, studentInfo, amount) {
            const modal = document.getElementById('proofLightboxModal');
            const img = document.getElementById('modalProofImg');
            const nameEl = document.getElementById('modalStudentName');
            const infoEl = document.getElementById('modalStudentInfo');
            const badgeEl = document.getElementById('modalAmountBadge');
            const openTabBtn = document.getElementById('modalOpenTabBtn');

            img.src = imageUrl;
            nameEl.textContent = 'Bukti Pembayaran: ' + (studentName || 'Siswa');
            infoEl.textContent = studentInfo ? studentInfo : 'Calon Siswa';
            badgeEl.textContent = amount ? 'Biaya: Rp ' + amount : 'Biaya: Tidak ada data';
            openTabBtn.href = imageUrl;

            modal.style.opacity = '1';
            modal.style.visibility = 'visible';
            modal.style.pointerEvents = 'auto';
            document.body.style.overflow = 'hidden';
        }

        function closeProofModal() {
            const modal = document.getElementById('proofLightboxModal');
            if (modal) {
                modal.style.opacity = '0';
                modal.style.visibility = 'hidden';
                modal.style.pointerEvents = 'none';
            }
        }
        
        function getGroupKey(group) {
            const label = group.querySelector('.nav-group-header .nav-item-left span');
            return label ? 'navgroup_' + label.textContent.trim() : null;
        }

        function toggleNavGroup(headerEl) {
            const group = headerEl.closest('.nav-group');
            if (!group) return;
            group.classList.toggle('open');
            // Persist state to localStorage
            const key = getGroupKey(group);
            if (key) {
                if (group.classList.contains('open')) {
                    localStorage.setItem(key, '1');
                } else {
                    localStorage.removeItem(key);
                }
            }
        }

        // Restore sidebar open state on page load
        document.addEventListener('DOMContentLoaded', function () {
            const currentPath = window.location.pathname;

            document.querySelectorAll('.nav-group').forEach(function (group) {
                const key = getGroupKey(group);

                // Auto-open group if any sub-link matches current URL
                const subLinks = group.querySelectorAll('.nav-group-items a[href]');
                let hasActive = false;
                subLinks.forEach(function (link) {
                    const href = link.getAttribute('href');
                    if (href && href !== '#' && currentPath.startsWith(href)) {
                        link.classList.add('active');
                        hasActive = true;
                    }
                });

                // Open if active page is inside, or if user previously opened it
                if (hasActive || (key && localStorage.getItem(key) === '1')) {
                    group.classList.add('open');
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProofModal();
            }
        });
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
