<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();
$stats = [
    'total_siswa' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn(),
    'pending'     => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'pending'")->fetchColumn(),
    'active'      => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'active'")->fetchColumn(),
    'bank_soal'   => (int) $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn(),
    'materi'      => (int) $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn(),
    'staf'        => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' OR role = 'staf'")->fetchColumn(),
];

// Ambil data siswa aktif untuk tabel
$searchQuery = isset($_GET['cari']) ? trim($_GET['cari']) : '';
if ($searchQuery !== '') {
    $stmt = $pdo->prepare("
        SELECT id, name, email, status, whatsapp, whatsapp_ortu, sekolah_asal, program_tujuan, tempat_lahir, tanggal_lahir, nama_paket, harga_paket, tanggal_mulai, alamat
        FROM users
        WHERE role = 'siswa' AND status = 'active'
          AND (name LIKE ? OR email LIKE ? OR whatsapp LIKE ?)
        ORDER BY id DESC
    ");
    $like = '%' . $searchQuery . '%';
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT id, name, email, status, whatsapp, whatsapp_ortu, sekolah_asal, program_tujuan, tempat_lahir, tanggal_lahir, nama_paket, harga_paket, tanggal_mulai, alamat
        FROM users
        WHERE role = 'siswa' AND status = 'active'
        ORDER BY id DESC
    ");
}
$siswas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bimbel Portal</title>
    <!-- <link rel="stylesheet" href="/assets/css/style.css">    -->
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

        /* ===== ALERTS & BUTTONS ===== */
        .alert-banner {
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-action-edit:hover {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-action-deactivate {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-action-deactivate:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* MODAL DETAIL / EDIT DATA SISWA */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
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
            border-radius: 14px;
            width: 100%;
            max-width: 780px;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 20px 45px rgba(0,0,0,0.18);
            display: flex;
            flex-direction: column;
            transform: scale(0.96);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 26px 16px;
            background: #ffffff;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.2px;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: #64748b;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .modal-close-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .modal-body {
            padding: 22px 26px;
            flex: 1;
        }

        .edit-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 20px;
            row-gap: 16px;
        }

        .edit-form-group {
            display: flex;
            flex-direction: column;
        }

        .edit-form-group.full-width {
            grid-column: 1 / -1;
        }

        .edit-form-label {
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 7px;
        }

        .edit-form-input,
        .edit-form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            background: #ffffff;
            box-sizing: border-box;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .edit-form-input:focus,
        .edit-form-textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .edit-form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modal-footer {
            padding: 16px 26px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        .btn-modal-cancel {
            padding: 9px 18px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-modal-cancel:hover {
            background: #f1f5f9;
        }

        .btn-modal-submit {
            padding: 9px 22px;
            border-radius: 8px;
            background: #1d4ed8;
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-modal-submit:hover {
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
                <a href="/dashboard" class="nav-item active">
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
                    <h1>Dashboard Admin</h1>
                </div>
                <div class="topbar-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </div>

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

                <!-- Profil Sistem -->
                <div class="db-profile-card">
                    <div class="db-profile-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="db-profile-meta">Profil Sistem Lembaga</div>
                        <div class="db-profile-title">
                            Bimbel Alahaido
                            <span class="db-profile-badge">SELEKSI_TNI_POLRI</span>
                        </div>
                        <p class="db-profile-desc">Lembaga ini menggunakan sistem Seleksi TNI/POLRI. Program seleksi tetap menggunakan pola yang sekarang.</p>
                    </div>
                </div>

                <!-- Link Pendaftaran -->
                <div class="db-link-banner">
                    <div class="db-link-left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                        <div>
                            <p class="db-link-title">Link Pendaftaran</p>
                            <p class="db-link-desc">Bagikan link resmi pendaftaran <a href="/register">Bimbel Alahaido</a> kepada calon siswa.</p>
                        </div>
                    </div>
                    <a href="/register" class="btn-share-link" id="btn-bagikan-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        Bagikan Link Pendaftaran
                    </a>
                </div>

                <!-- Stat Boxes -->
                <div class="db-stats-row">
                    <div class="db-stat-box">
                        <div class="db-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Siswa Aktif</p>
                        <div class="db-stat-value"><?php echo $stats['active']; ?></div>
                    </div>
                    <div class="db-stat-box">
                        <div class="db-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="12" cy="10" r="3"/>
                                <path d="M7 21v-1a5 5 0 0 1 10 0v1"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Staf Aktif</p>
                        <div class="db-stat-value"><?php echo $stats['staf']; ?></div>
                    </div>
                    <div class="db-stat-box">
                        <div class="db-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <p class="db-stat-label">Kelas Aktif</p>
                        <div class="db-stat-value"><?php echo $stats['materi']; ?></div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="db-actions-row">
                    <a href="/admin/verifikasi" class="btn-action-outline" id="btn-tambah-staf">Tambah Staf</a>
                    <a href="#" class="btn-action-outline" id="btn-buat-kelas">Buat Kelas</a>
                </div>

                <!-- Data Siswa Table -->
                <div class="db-table-card">
                    <div class="db-table-header">
                        <h2 class="db-table-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Data Siswa
                        </h2>
                        <form method="GET" action="/dashboard" class="db-search-row">
                            <div>
                                <div class="db-search-label">Pencarian</div>
                                <div class="db-search-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input
                                        type="text"
                                        name="cari"
                                        class="db-search-input"
                                        placeholder="Cari siswa..."
                                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                                        id="input-cari-siswa"
                                    >
                                </div>
                            </div>
                            <span class="db-count-badge"><?php echo count($siswas); ?> siswa</span>
                        </form>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="db-siswa-table">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NOMOR</th>
                                    <th>NAMA</th>
                                    <th>EMAIL LOGIN</th>
                                    <th>WA</th>
                                    <th>STATUS</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($siswas)): ?>
                                <tr>
                                    <td colspan="7" class="db-table-empty">Tidak ada data siswa ditemukan.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($siswas as $i => $siswa): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td style="font-weight:600; color:#0f172a;">CP<?php echo str_pad($siswa['id'], 10, '0', STR_PAD_LEFT); ?></td>
                                    <td class="name-bold"><?php echo htmlspecialchars(strtoupper($siswa['name'])); ?></td>
                                    <td><?php echo htmlspecialchars($siswa['email']); ?></td>
                                    <td><?php echo htmlspecialchars($siswa['whatsapp'] ?? '-'); ?></td>
                                    <td>
                                        <?php if ($siswa['status'] === 'active'): ?>
                                            <span class="badge-aktif">Aktif</span>
                                        <?php elseif ($siswa['status'] === 'pending'): ?>
                                            <span class="badge-pending">Pending</span>
                                        <?php else: ?>
                                            <span class="badge-inactive"><?php echo htmlspecialchars($siswa['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="aksi-cell">
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <button type="button" class="btn-action-edit" onclick='openEditModal(<?php echo json_encode($siswa, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                                                Edit
                                            </button>
                                            <button type="button" class="btn-action-deactivate" onclick="confirmDeactivate(<?php echo $siswa['id']; ?>, '<?php echo addslashes(htmlspecialchars($siswa['name'])); ?>')">
                                                Nonaktifkan
                                            </button>
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

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }
        updateTime();
        setInterval(updateTime, 1000);

        function getGroupKey(group) {
            // Use the label text of the group header as a stable key
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

        // Edit & Deactivate Student Handlers
        function openEditModal(siswa) {
            document.getElementById('edit_student_id').value = siswa.id || '';
            document.getElementById('edit_name').value = siswa.name || '';
            document.getElementById('edit_whatsapp').value = siswa.whatsapp || '';
            document.getElementById('edit_whatsapp_ortu').value = siswa.whatsapp_ortu || '';
            document.getElementById('edit_sekolah_asal').value = siswa.sekolah_asal || '';
            document.getElementById('edit_program_tujuan').value = siswa.program_tujuan || '';
            document.getElementById('edit_tempat_lahir').value = siswa.tempat_lahir || '';
            document.getElementById('edit_tanggal_lahir').value = siswa.tanggal_lahir || '';
            document.getElementById('edit_nama_paket').value = siswa.nama_paket || '';
            document.getElementById('edit_harga_paket').value = (siswa.harga_paket !== null && siswa.harga_paket !== undefined && siswa.harga_paket !== '') ? siswa.harga_paket : '0.00';
            document.getElementById('edit_tanggal_mulai').value = siswa.tanggal_mulai || '';
            document.getElementById('edit_alamat').value = siswa.alamat || '';

            document.getElementById('editStudentModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editStudentModal').classList.remove('active');
        }

        function confirmDeactivate(id, name) {
            if (confirm('Apakah Anda yakin ingin menonaktifkan siswa "' + name + '"?\nSiswa yang dinonaktifkan tidak akan muncul lagi di tabel siswa aktif.')) {
                document.getElementById('deactivate_student_id').value = id;
                document.getElementById('formDeactivate').submit();
            }
        }
    </script>

    <!-- HIDDEN FORM FOR DEACTIVATE -->
    <form id="formDeactivate" method="POST" action="/api/admin/siswa/deactivate" style="display: none;">
        <input type="hidden" name="id" id="deactivate_student_id">
    </form>

    <!-- MODAL DETAIL / EDIT DATA SISWA -->
    <div class="modal-overlay" id="editStudentModal" onclick="if(event.target === this) closeEditModal()">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Detail / Edit Data Siswa</h3>
                <button type="button" class="modal-close-btn" onclick="closeEditModal()" title="Tutup">&times;</button>
            </div>
            <form method="POST" action="/api/admin/siswa/update" id="editStudentForm">
                <input type="hidden" name="id" id="edit_student_id">
                <div class="modal-body">
                    <div class="edit-form-grid">
                        <!-- Column 1 / Row 1 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Nama</label>
                            <input type="text" name="name" id="edit_name" class="edit-form-input" required>
                        </div>

                        <!-- Column 2 / Row 1 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">WhatsApp siswa</label>
                            <input type="text" name="whatsapp" id="edit_whatsapp" class="edit-form-input" required>
                        </div>

                        <!-- Column 1 / Row 2 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">WhatsApp orang tua</label>
                            <input type="text" name="whatsapp_ortu" id="edit_whatsapp_ortu" class="edit-form-input">
                        </div>

                        <!-- Column 2 / Row 2 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Sekolah asal</label>
                            <input type="text" name="sekolah_asal" id="edit_sekolah_asal" class="edit-form-input">
                        </div>

                        <!-- Column 1 / Row 3 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Program tujuan</label>
                            <input type="text" name="program_tujuan" id="edit_program_tujuan" class="edit-form-input">
                        </div>

                        <!-- Column 2 / Row 3 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Tempat lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="edit-form-input">
                        </div>

                        <!-- Column 1 / Row 4 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Tanggal lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="edit-form-input">
                        </div>

                        <!-- Column 2 / Row 4 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Nama paket</label>
                            <input type="text" name="nama_paket" id="edit_nama_paket" class="edit-form-input">
                        </div>

                        <!-- Column 1 / Row 5 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Harga paket</label>
                            <input type="text" name="harga_paket" id="edit_harga_paket" class="edit-form-input" placeholder="0.00">
                        </div>

                        <!-- Column 2 / Row 5 -->
                        <div class="edit-form-group">
                            <label class="edit-form-label">Tanggal mulai</label>
                            <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="edit-form-input">
                        </div>

                        <!-- Full Width / Row 6 -->
                        <div class="edit-form-group full-width">
                            <label class="edit-form-label">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="edit-form-textarea" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>