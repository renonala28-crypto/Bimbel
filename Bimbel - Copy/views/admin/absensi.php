<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();

// Get active students with their program
$students = $pdo->query("SELECT id, name, nickname, program_tujuan FROM users WHERE role = 'siswa' AND status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get today's date info
$today = date('Y-m-d');
$todayFormatted = date('d/m/Y');

// Handle form submission
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_attendance') {
        $date = $_POST['attendance_date'] ?? $today;
        $kegiatan = $_POST['kegiatan'] ?? 'Kelas';
        $statuses = $_POST['status'] ?? [];
        
        $savedCount = 0;
        foreach ($statuses as $userId => $status) {
            if ($status === '') {
                // If set to "Belum dipilih", remove existing record for that day
                $stmtDel = $pdo->prepare("DELETE FROM attendance WHERE user_id = ? AND attendance_date = ?");
                $stmtDel->execute([$userId, $date]);
                continue;
            }
            
            // Upsert attendance
            $stmt = $pdo->prepare("INSERT INTO attendance (user_id, attendance_date, status, created_at) 
                VALUES (?, ?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE status = VALUES(status)");
            $stmt->execute([$userId, $date, $status]);
            $savedCount++;
        }
        
        $successMsg = "Absensi berhasil disimpan ($savedCount siswa diperbarui)!";
    }
    
    if ($_POST['action'] === 'delete_attendance') {
        $date = $_POST['delete_date'] ?? '';
        if ($date) {
            $stmt = $pdo->prepare("DELETE FROM attendance WHERE attendance_date = ?");
            $stmt->execute([$date]);
            $successMsg = "Data absensi tanggal " . htmlspecialchars($date) . " berhasil dihapus!";
        }
    }
}

// Filter date for viewing
$filterDate = $_GET['date'] ?? $today;
$filterKegiatan = $_GET['kegiatan'] ?? 'Kelas';

// Get attendance for selected date
$stmtAtt = $pdo->prepare("SELECT user_id, status FROM attendance WHERE attendance_date = ?");
$stmtAtt->execute([$filterDate]);
$attendanceData = [];
while ($row = $stmtAtt->fetch(PDO::FETCH_ASSOC)) {
    $attendanceData[$row['user_id']] = $row['status'];
}

// Get attendance history (grouped by date)
$historyStmt = $pdo->query("
    SELECT 
        attendance_date,
        COUNT(*) as total_entries,
        SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir_count
    FROM attendance 
    GROUP BY attendance_date 
    ORDER BY attendance_date DESC 
    LIMIT 30
");
$history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

// Count stats for current filter
$hadirCount = 0;
$izinCount = 0;
$sakitCount = 0;
$alpaCount = 0;
$belumCount = 0;

foreach ($students as $s) {
    $st = $attendanceData[$s['id']] ?? '';
    if ($st === 'hadir') $hadirCount++;
    elseif ($st === 'izin') $izinCount++;
    elseif ($st === 'sakit') $sakitCount++;
    elseif ($st === 'absen') $alpaCount++;
    else $belumCount++;
}

$totalStudents = count($students);

// Indonesian date formatting
$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$filterDateObj = new DateTime($filterDate);
$formattedFilterDate = $filterDateObj->format('Y-m-d');
$formattedDisplayDate = $filterDateObj->format('d') . ' ' . $months[(int)$filterDateObj->format('m')] . ' ' . $filterDateObj->format('Y');

$bimbelName = 'Bimbel Alahaido';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Siswa - Admin <?= $bimbelName ?></title>
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
            --success-green: #15803d;
            --success-green-hover: #166534;
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
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
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
            font-size: 24px;
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
            gap: 10px;
        }

        .alert-info-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #334155;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .alert-info-icon {
            color: #0284c7;
            flex-shrink: 0;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* ===== TOP FILTER BAR (Tanggal & Kegiatan) ===== */
        .top-filter-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .filter-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 220px;
        }

        .filter-col label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .form-input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-box input,
        .form-input-box select {
            width: 100%;
            padding: 9px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: #1e293b;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input-box input:focus,
        .form-input-box select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .btn-tampilkan-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 24px;
            background: #ffffff;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            height: 40px;
            white-space: nowrap;
        }

        .btn-tampilkan-edit:hover {
            background: #eff6ff;
            border-color: var(--primary-blue);
        }

        /* ===== MAIN CARD (KELAS) ===== */
        .main-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 30px;
        }

        .card-top-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-main-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .card-main-subtitle {
            font-size: 13px;
            color: var(--text-muted);
        }

        .pill-active-students {
            background: #1d4ed8;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 999px;
            white-space: nowrap;
        }

        /* Controls Row */
        .controls-row {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .control-group label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .search-field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-field-wrapper svg {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
        }

        .search-field-wrapper input {
            padding: 9px 14px 9px 38px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            color: #1e293b;
            min-width: 260px;
            outline: none;
            font-family: inherit;
        }

        .search-field-wrapper input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .search-field-wrapper input::placeholder {
            color: #94a3b8;
        }

        .select-program {
            padding: 9px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            color: #1e293b;
            min-width: 200px;
            outline: none;
            background: #ffffff;
            cursor: pointer;
            font-family: inherit;
        }

        .select-program:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }

        .btn-semua-hadir {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            background: var(--success-green);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            height: 40px;
        }

        .btn-semua-hadir:hover {
            background: var(--success-green-hover);
        }

        /* Status Count Filter Pills */
        .status-pill-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            color: #ffffff;
        }

        .pill-tampil { background: #334155; }
        .pill-hadir  { background: #15803d; }
        .pill-izin   { background: #0284c7; }
        .pill-sakit  { background: #eab308; color: #1e293b; }
        .pill-alpa   { background: #dc2626; }
        .pill-belum  { background: #0f172a; }

        .status-pill-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Table */
        .table-wrap {
            overflow-x: auto;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .student-table thead {
            background: #ffffff;
            border-bottom: 1.5px solid #e2e8f0;
        }

        .student-table th {
            padding: 12px 16px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
        }

        .student-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .student-table tbody tr:hover {
            background: #f8fafc;
        }

        .cell-no {
            font-weight: 600;
            color: #64748b;
            width: 45px;
        }

        .cell-name {
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .cell-program {
            font-weight: 600;
            color: #475569;
        }

        .select-status-dropdown {
            width: 100%;
            max-width: 260px;
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            background: #ffffff;
            outline: none;
            cursor: pointer;
            font-family: inherit;
        }

        .select-status-dropdown:focus {
            border-color: var(--primary-blue);
        }

        .select-status-dropdown.st-hadir {
            border-color: #86efac;
            color: #15803d;
            font-weight: 600;
        }

        .select-status-dropdown.st-izin {
            border-color: #7dd3fc;
            color: #0369a1;
            font-weight: 600;
        }

        .select-status-dropdown.st-sakit {
            border-color: #fde047;
            color: #854d0e;
            font-weight: 600;
        }

        .select-status-dropdown.st-absen {
            border-color: #fca5a5;
            color: #b91c1c;
            font-weight: 600;
        }

        .card-bottom-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .bottom-hint-text {
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-simpan-absensi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: #1d4ed8;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
        }

        .btn-simpan-absensi:hover {
            background: var(--primary-blue-hover);
        }

        /* ===== RIWAYAT ABSENSI SECTION ===== */
        .riwayat-section-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .riwayat-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .riwayat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border: 1px solid #f59e0b;
            color: #d97706;
            background: #fffbeb;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-action-edit:hover {
            background: #fef3c7;
        }

        .btn-action-hapus {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border: 1px solid #f87171;
            color: #dc2626;
            background: #fef2f2;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
        }

        .btn-action-hapus:hover {
            background: #fee2e2;
        }

        @media (max-width: 1024px) {
            .sidebar { width: 220px; }
            .content-area { padding: 20px 20px 40px; }
        }

        @media (max-width: 768px) {
            .top-filter-bar { flex-direction: column; align-items: stretch; }
            .controls-row { flex-direction: column; align-items: stretch; }
            .btn-semua-hadir { margin-left: 0; }
            .search-field-wrapper input { min-width: 100%; }
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
                            <span>Laporan</span>
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

                <!-- Group Operasional (Expanded by default for Absensi) -->
                <div class="nav-group open">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <div class="nav-item-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            <span>Operasional</span>
                        </div>
                        <span class="nav-chevron">▼</span>
                    </div>
                    <div class="nav-group-items">
                        <a href="/admin/absensi" class="nav-sub-item active">
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
                    <h1>Absensi Siswa</h1>
                    <div class="topbar-subtitle"><?= $bimbelName ?> — <?= $formattedDisplayDate ?></div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-badge"><?= $bimbelName ?></div>
                    <div class="topbar-avatar" title="Admin">A</div>
                    <a href="/logout" class="topbar-logout-btn" title="Keluar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </header>

            <div class="content-area">
                <!-- NOTIFICATION BANNER -->
                <?php if (!empty($successMsg)): ?>
                    <div class="alert-banner alert-success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <?= htmlspecialchars($successMsg) ?>
                    </div>
                <?php endif; ?>

                <!-- INFO BANNER -->
                <div class="alert-info-box">
                    <svg class="alert-info-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Hanya siswa aktif dan riwayat absensi milik <strong><?= $bimbelName ?></strong> yang ditampilkan.</span>
                </div>

                <!-- TOP FILTER BAR: TANGGAL & KEGIATAN -->
                <form method="GET" action="/admin/absensi" class="top-filter-bar">
                    <div class="filter-col">
                        <label>Tanggal</label>
                        <div class="form-input-box">
                            <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
                        </div>
                    </div>
                    <div class="filter-col">
                        <label>Kegiatan</label>
                        <div class="form-input-box">
                            <input type="text" name="kegiatan" value="<?= htmlspecialchars($filterKegiatan) ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn-tampilkan-edit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Tampilkan / Edit
                    </button>
                </form>

                <!-- MAIN ATTENDANCE CARD -->
                <form method="POST" action="/admin/absensi?date=<?= urlencode($filterDate) ?>&kegiatan=<?= urlencode($filterKegiatan) ?>">
                    <input type="hidden" name="action" value="save_attendance">
                    <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($filterDate) ?>">
                    <input type="hidden" name="kegiatan" value="<?= htmlspecialchars($filterKegiatan) ?>">

                    <div class="main-card">
                        <div class="card-top-title-row">
                            <div>
                                <h2 class="card-main-title"><?= htmlspecialchars($filterKegiatan) ?></h2>
                                <p class="card-main-subtitle"><?= $formattedFilterDate ?> — cari siswa lalu pilih status kehadiran.</p>
                            </div>
                            <span class="pill-active-students"><?= $totalStudents ?> siswa aktif</span>
                        </div>

                        <!-- Controls: Cari Siswa, Filter Program, Semua Hadir -->
                        <div class="controls-row">
                            <div class="control-group">
                                <label>Cari siswa</label>
                                <div class="search-field-wrapper">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    <input type="text" placeholder="Ketik nama siswa..." id="searchStudent" oninput="applyFilters()">
                                </div>
                            </div>
                            <div class="control-group">
                                <label>Filter program</label>
                                <select class="select-program" id="filterProgram" onchange="applyFilters()">
                                    <option value="">Semua program</option>
                                    <option value="TNI AD">TNI AD</option>
                                    <option value="TNI AL">TNI AL</option>
                                    <option value="TNI AU">TNI AU</option>
                                    <option value="POLRI">POLRI</option>
                                    <option value="AKPOL">AKPOL</option>
                                    <option value="AKMIL">AKMIL</option>
                                </select>
                            </div>
                            <button type="button" class="btn-semua-hadir" onclick="markAllVisibleHadir()">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                Semua Hadir
                            </button>
                        </div>

                        <!-- Status Filter Badges -->
                        <div class="status-pill-group">
                            <button type="button" class="status-pill-btn pill-tampil" onclick="setStatusFilter('')">
                                Tampil: <span id="countTampil"><?= $totalStudents ?></span>
                            </button>
                            <button type="button" class="status-pill-btn pill-hadir" onclick="setStatusFilter('hadir')">
                                Hadir: <span id="countHadir"><?= $hadirCount ?></span>
                            </button>
                            <button type="button" class="status-pill-btn pill-izin" onclick="setStatusFilter('izin')">
                                Izin: <span id="countIzin"><?= $izinCount ?></span>
                            </button>
                            <button type="button" class="status-pill-btn pill-sakit" onclick="setStatusFilter('sakit')">
                                Sakit: <span id="countSakit"><?= $sakitCount ?></span>
                            </button>
                            <button type="button" class="status-pill-btn pill-alpa" onclick="setStatusFilter('absen')">
                                Alpa: <span id="countAlpa"><?= $alpaCount ?></span>
                            </button>
                            <button type="button" class="status-pill-btn pill-belum" onclick="setStatusFilter('belum')">
                                Belum dipilih: <span id="countBelum"><?= $belumCount ?></span>
                            </button>
                        </div>

                        <!-- Students Table -->
                        <div class="table-wrap">
                            <table class="student-table" id="studentTable">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">NO</th>
                                        <th>SISWA</th>
                                        <th>PROGRAM</th>
                                        <th style="width: 260px;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($students as $s): 
                                        $status = $attendanceData[$s['id']] ?? '';
                                        $program = $s['program_tujuan'] ?? '-';
                                        $statusClass = $status ? 'st-' . $status : '';
                                    ?>
                                    <tr class="student-row-item" data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>" data-program="<?= htmlspecialchars($program) ?>" data-status="<?= $status ?>">
                                        <td class="cell-no"><?= $no++ ?></td>
                                        <td class="cell-name"><?= htmlspecialchars($s['name']) ?></td>
                                        <td class="cell-program"><?= htmlspecialchars($program) ?></td>
                                        <td>
                                            <select name="status[<?= $s['id'] ?>]" class="select-status-dropdown <?= $statusClass ?>" onchange="handleStatusChange(this)">
                                                <option value="" <?= $status === '' ? 'selected' : '' ?>>Belum dipilih</option>
                                                <option value="hadir" <?= $status === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                                <option value="izin" <?= $status === 'izin' ? 'selected' : '' ?>>Izin</option>
                                                <option value="sakit" <?= $status === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                                <option value="absen" <?= $status === 'absen' ? 'selected' : '' ?>>Alpa</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 32px; color: #94a3b8;">Tidak ada data siswa aktif.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bottom Bar -->
                        <div class="card-bottom-bar">
                            <div class="bottom-hint-text">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                Status Belum dipilih tidak akan disimpan.
                            </div>
                            <button type="submit" class="btn-simpan-absensi">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Simpan / Perbarui Absensi
                            </button>
                        </div>
                    </div>
                </form>

                <!-- RIWAYAT ABSENSI -->
                <div class="riwayat-top-row">
                    <h3 class="riwayat-section-title">Riwayat Absensi</h3>
                    <div class="search-field-wrapper">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" placeholder="Cari tanggal atau kegiatan..." id="searchHistory" oninput="filterHistory()">
                    </div>
                </div>

                <div class="riwayat-card">
                    <div class="table-wrap" style="border:none;">
                        <table class="student-table" id="historyTable">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>KEGIATAN</th>
                                    <th>HADIR</th>
                                    <th>JUMLAH DATA</th>
                                    <th style="width: 140px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($history)): ?>
                                    <?php foreach ($history as $h): ?>
                                    <tr class="history-item-row" data-text="<?= htmlspecialchars($h['attendance_date']) ?>">
                                        <td><?= htmlspecialchars($h['attendance_date']) ?></td>
                                        <td>Kelas</td>
                                        <td><?= $h['hadir_count'] ?></td>
                                        <td><?= $h['total_entries'] ?></td>
                                        <td>
                                            <div style="display: flex; gap: 6px;">
                                                <a href="/admin/absensi?date=<?= urlencode($h['attendance_date']) ?>&kegiatan=Kelas" class="btn-action-edit">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    Edit
                                                </a>
                                                <form method="POST" action="/admin/absensi" style="display:inline;" onsubmit="return confirm('Hapus data absensi tanggal <?= $h['attendance_date'] ?>?')">
                                                    <input type="hidden" name="action" value="delete_attendance">
                                                    <input type="hidden" name="delete_date" value="<?= htmlspecialchars($h['attendance_date']) ?>">
                                                    <button type="submit" class="btn-action-hapus">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 24px; color: #94a3b8;">Belum ada riwayat absensi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ===== SIDEBAR LOGIC =====
        function getGroupKey(group) {
            const label = group.querySelector('.nav-group-header .nav-item-left span');
            return label ? 'navgroup_' + label.textContent.trim() : null;
        }

        function toggleNavGroup(headerEl) {
            const group = headerEl.closest('.nav-group');
            if (!group) return;
            group.classList.toggle('open');
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
                const subLinks = group.querySelectorAll('.nav-group-items a[href]');
                let hasActive = false;
                subLinks.forEach(function (link) {
                    const href = link.getAttribute('href');
                    if (href && href !== '#' && currentPath.startsWith(href)) {
                        link.classList.add('active');
                        hasActive = true;
                    }
                });
                if (hasActive || (key && localStorage.getItem(key) === '1')) {
                    group.classList.add('open');
                }
            });
        });

        // ===== ATTENDANCE LOGIC =====
        let currentStatusFilter = '';

        function handleStatusChange(selectEl) {
            selectEl.classList.remove('st-hadir', 'st-izin', 'st-sakit', 'st-absen');
            if (selectEl.value) {
                selectEl.classList.add('st-' + selectEl.value);
            }

            const row = selectEl.closest('tr');
            if (row) {
                row.setAttribute('data-status', selectEl.value);
            }

            updateCounts();
        }

        function updateCounts() {
            let hadir = 0, izin = 0, sakit = 0, alpa = 0, belum = 0;
            document.querySelectorAll('.student-row-item').forEach(function(row) {
                const select = row.querySelector('.select-status-dropdown');
                if (!select) return;
                const val = select.value;
                if (val === 'hadir') hadir++;
                else if (val === 'izin') izin++;
                else if (val === 'sakit') sakit++;
                else if (val === 'absen') alpa++;
                else belum++;
            });

            document.getElementById('countHadir').textContent = hadir;
            document.getElementById('countIzin').textContent = izin;
            document.getElementById('countSakit').textContent = sakit;
            document.getElementById('countAlpa').textContent = alpa;
            document.getElementById('countBelum').textContent = belum;
        }

        function markAllVisibleHadir() {
            document.querySelectorAll('.student-row-item').forEach(function(row) {
                if (row.style.display === 'none') return;
                const select = row.querySelector('.select-status-dropdown');
                if (select) {
                    select.value = 'hadir';
                    handleStatusChange(select);
                }
            });
        }

        function setStatusFilter(status) {
            currentStatusFilter = status;
            applyFilters();
        }

        function applyFilters() {
            const searchVal = document.getElementById('searchStudent').value.toLowerCase().trim();
            const programVal = document.getElementById('filterProgram').value;

            let visibleCount = 0;
            document.querySelectorAll('.student-row-item').forEach(function(row) {
                const name = row.getAttribute('data-name');
                const program = row.getAttribute('data-program');
                const select = row.querySelector('.select-status-dropdown');
                const rowStatus = select ? select.value : '';

                let match = true;
                if (searchVal && name.indexOf(searchVal) === -1) match = false;
                if (programVal && program !== programVal) match = false;
                
                if (currentStatusFilter !== '') {
                    if (currentStatusFilter === 'belum') {
                        if (rowStatus !== '') match = false;
                    } else {
                        if (rowStatus !== currentStatusFilter) match = false;
                    }
                }

                if (match) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('countTampil').textContent = visibleCount;
        }

        function filterHistory() {
            const searchVal = document.getElementById('searchHistory').value.toLowerCase().trim();
            document.querySelectorAll('.history-item-row').forEach(function(row) {
                const text = row.getAttribute('data-text').toLowerCase();
                row.style.display = text.indexOf(searchVal) !== -1 ? '' : 'none';
            });
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>