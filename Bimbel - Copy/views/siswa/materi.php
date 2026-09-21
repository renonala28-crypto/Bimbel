<?php
require_once __DIR__ . '/../../config/database.php';

$pdo = getDBConnection();
$userId = auth()->id() ?? ($_SESSION['user_id'] ?? 0);

// Get materials allowed for this student
$allMaterials = $pdo->query("SELECT * FROM materials WHERE status = 'aktif' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$allowedMaterials = [];
foreach ($allMaterials as $m) {
    $allowedStr = trim($m['allowed_students'] ?? '');
    if (empty($allowedStr)) {
        // Allowed for all students
        $allowedMaterials[] = $m;
    } else {
        $arr = explode(',', $allowedStr);
        if (in_array((string)$userId, $arr) || in_array((int)$userId, $arr)) {
            $allowedMaterials[] = $m;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Belajar - Bimbel Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .materi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .m-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .m-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .m-cat {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            margin-bottom: 10px;
        }
        .m-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }
        .m-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 16px;
            flex: 1;
        }
        .m-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            background: #1d4ed8;
            color: #ffffff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }
        .m-btn:hover {
            background: #1e40af;
        }
        /* Modal for preview */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-backdrop.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 12px;
            max-width: 600px;
            width: 100%;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-body {
            padding: 20px;
            overflow-y: auto;
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
        }
        .modal-footer {
            padding: 12px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            background: #f8fafc;
        }
    </style>
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
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
                <a href="/materi" class="nav-item active">Materi Belajar</a>
                <a href="/perkembangan" class="nav-item">Perkembangan</a>
                <a href="/kalkulator-samapta" class="nav-item">Kalkulator Samapta</a>
                <a href="/leaderboard" class="nav-item">Leaderboard</a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <p class="user-name"><?php echo htmlspecialchars(auth()->user()->name ?? ($_SESSION['user_name'] ?? 'Siswa')); ?></p>
                    <p class="user-role">Siswa Aktif</p>
                </div>
                <a href="/logout" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <div class="topbar-title"><h1>Materi Belajar 📚</h1></div>
            </div>

            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Kumpulan Materi Pembelajaran</h2>
                        <p style="color:#64748b; font-size:13px; margin:4px 0 0;">Akses modul, teks bimbingan, video, dan dokumen persiapan seleksi.</p>
                    </div>

                    <?php if (empty($allowedMaterials)): ?>
                        <p style="text-align: center; padding: 40px; color: var(--text-light);">
                            Belum ada materi pembelajaran yang dapat diakses saat ini.
                        </p>
                    <?php else: ?>
                        <div class="materi-grid">
                            <?php foreach ($allowedMaterials as $m): ?>
                                <div class="m-card">
                                    <div>
                                        <span class="m-cat"><?php echo htmlspecialchars($m['category'] ?? 'Umum'); ?></span>
                                        <h3 class="m-title"><?php echo htmlspecialchars($m['title']); ?></h3>
                                        <p class="m-desc"><?php echo htmlspecialchars($m['description'] ?: 'Tidak ada keterangan tambahan.'); ?></p>
                                    </div>
                                    <div>
                                        <?php if (!empty($m['file_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="m-btn" download>
                                                📥 Unduh File Dokumen
                                            </a>
                                        <?php elseif (($m['type'] ?? 'teks') === 'video' && !empty($m['content'])): ?>
                                            <a href="<?php echo htmlspecialchars($m['content']); ?>" target="_blank" class="m-btn">
                                                ▶️ Tonton Video
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="m-btn" onclick='readMaterial(<?php echo json_encode($m); ?>)'>
                                                📖 Baca Materi
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Baca Materi -->
    <div class="modal-backdrop" id="readModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="readModalTitle" style="margin:0; font-size:16px;">Materi</h3>
                <button type="button" style="background:none; border:none; font-size:20px; cursor:pointer;" onclick="document.getElementById('readModal').classList.remove('open')">&times;</button>
            </div>
            <div class="modal-body" id="readModalBody" style="white-space: pre-wrap;"></div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('readModal').classList.remove('open')">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function readMaterial(item) {
            document.getElementById('readModalTitle').textContent = item.title;
            document.getElementById('readModalBody').textContent = item.content || 'Konten teks belum tersedia.';
            document.getElementById('readModal').classList.add('open');
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
