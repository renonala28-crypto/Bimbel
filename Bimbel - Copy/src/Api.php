<?php
/**
 * API Handler
 * Menangani request API seperti login, register, submit exam, dll
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Auth.php';

use App\Auth;

header('Content-Type: application/json');

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle API endpoints
if ($request_method === 'POST') {
    if (strpos($request_uri, '/api/login') !== false) {
        handleLogin();
    } elseif (strpos($request_uri, '/api/register') !== false) {
        handleRegister();
    } elseif (strpos($request_uri, '/api/admin/verify') !== false) {
        handleAdminVerification();
    } elseif (strpos($request_uri, '/api/admin/nilai/save') !== false) {
        handleAdminSaveNilai();
    } elseif (strpos($request_uri, '/api/admin/nilai/delete') !== false) {
        handleAdminDeleteNilai();
    } elseif (strpos($request_uri, '/api/exam/submit') !== false) {
        handleExamSubmit();
    } elseif (strpos($request_uri, '/api/sudoku/save') !== false) {
        handleSudokuSave();
    }
}

/**
 * Handle Login
 */
function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] == '1';

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi']);
        exit;
    }

    $auth = new Auth();
    $result = $auth->login($email, $password, $rememberMe);

    if ($result['success']) {
        if ($result['user']['status'] === 'pending') {
            header('Location: /pending');
        } elseif ($result['user']['status'] === 'rejected') {
            header('Location: /rejected');
        } else {
            header('Location: /dashboard');
        }
    } else {
        header('Location: /login?error=' . urlencode($result['message']));
    }
}

/**
 * Handle Register
 */
function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $name = $_POST['name'] ?? null;
    $nickname = $_POST['nickname'] ?? null;
    $whatsapp = $_POST['whatsapp'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Validasi
    if (!$name || !$nickname || !$whatsapp || !$email || !$password || !$confirm_password) {
        header('Location: /register?error=' . urlencode('Semua field harus diisi'));
        exit;
    }

    if ($password !== $confirm_password) {
        header('Location: /register?error=' . urlencode('Password tidak cocok'));
        exit;
    }

    if (strlen($password) < 8) {
        header('Location: /register?error=' . urlencode('Password minimal 8 karakter'));
        exit;
    }

    // Handle file upload
    if (!isset($_FILES['proof_file']) || $_FILES['proof_file']['error'] !== UPLOAD_ERR_OK) {
        header('Location: /register?error=' . urlencode('Bukti transfer harus diupload'));
        exit;
    }

    $file = $_FILES['proof_file'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 2097152; // 2 MB

    if (!in_array($file['type'], $allowed_types)) {
        header('Location: /register?error=' . urlencode('Format file harus JPG, PNG, atau WEBP'));
        exit;
    }

    if ($file['size'] > $max_size) {
        header('Location: /register?error=' . urlencode('Ukuran file terlalu besar (maksimal 2 MB)'));
        exit;
    }

    // Save file
    $upload_dir = __DIR__ . '/../public/uploads/payments/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_name = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        header('Location: /register?error=' . urlencode('Gagal upload file'));
        exit;
    }

    // Register user
    $auth = new Auth();
    $result = $auth->register($name, $nickname, $whatsapp, $email, $password, 'uploads/payments/' . $file_name);

    if ($result['success']) {
        // Login otomatis
        $login_result = $auth->login($email, $password);
        header('Location: /pending');
    } else {
        header('Location: /register?error=' . urlencode($result['message']));
    }
}

/**
 * Handle Exam Submit
 */
function handleAdminVerification() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
        exit;
    }

    $userId = (int) ($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$userId || !in_array($action, ['approve', 'reject'], true)) {
        header('Location: /admin/verifikasi?error=' . urlencode('Data verifikasi tidak valid'));
        exit;
    }

    $auth = new Auth();
    $reason = 'Akun Anda ditolak karena data yang dikirim tidak memenuhi persyaratan.';
    $result = $action === 'approve' ? $auth->approveUser($userId) : $auth->rejectUser($userId, $reason);

    $status = $result['success'] ? 'success' : 'error';
    header('Location: /admin/verifikasi?' . $status . '=' . urlencode($result['message']));
    exit;
}

/**
 * Handle Admin Save Nilai (Tambah/Edit Nilai Bulanan)
 */
function handleAdminSaveNilai() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
        exit;
    }

    $pdo = getDBConnection();

    $userId = (int) ($_POST['user_id'] ?? 0);
    $studentNameNew = trim($_POST['student_name_new'] ?? '');
    $periodMonth = (int) ($_POST['period_month'] ?? date('n'));
    $periodYear = (int) ($_POST['period_year'] ?? date('Y'));
    $recordedDate = $_POST['recorded_date'] ?? date('Y-m-d');

    // If new student name entered and user_id is 0 or "new"
    if ($userId <= 0 && $studentNameNew !== '') {
        // Create user as active student
        $fakeEmail = 'siswa_' . time() . '@bimbelalahaido.com';
        $stmtInsert = $pdo->prepare("INSERT INTO users (name, nickname, whatsapp, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'siswa', 'active')");
        $stmtInsert->execute([
            $studentNameNew,
            explode(' ', $studentNameNew)[0],
            '08' . rand(1000000000, 9999999999),
            $fakeEmail,
            password_hash('siswa123', PASSWORD_BCRYPT)
        ]);
        $userId = $pdo->lastInsertId();
    }

    if ($userId <= 0) {
        header('Location: /admin/nilai?error=' . urlencode('Pilih siswa atau masukkan nama siswa baru'));
        exit;
    }

    $twk = (float) str_replace(',', '.', $_POST['twk'] ?? '0');
    $tiu = (float) str_replace(',', '.', $_POST['tiu'] ?? '0');
    $tkp = (float) str_replace(',', '.', $_POST['tkp'] ?? '0');
    $nilaiCat = (float) str_replace(',', '.', $_POST['nilai_cat'] ?? '0');
    if ($nilaiCat <= 0 && ($twk > 0 || $tiu > 0 || $tkp > 0)) {
        $nilaiCat = $twk + $tiu + $tkp;
    }

    $lariMeters = (int) ($_POST['lari_meters'] ?? 0);
    $pushUp = (int) ($_POST['push_up'] ?? 0);
    $sitUp = (int) ($_POST['sit_up'] ?? 0);
    $pullUp = (int) ($_POST['pull_up'] ?? 0);
    $shuttle = (float) str_replace(',', '.', $_POST['shuttle_seconds'] ?? '0');
    $renang = (float) str_replace(',', '.', $_POST['renang_seconds'] ?? '0');
    $renangDist = (int) ($_POST['renang_distance'] ?? 25);
    $totalSamapta = (float) str_replace(',', '.', $_POST['total_samapta'] ?? '0');
    $nilaiTotal = (float) str_replace(',', '.', $_POST['nilai_total'] ?? '0');

    // Check if score exists for user in this period
    $stmtCheck = $pdo->prepare("SELECT id FROM student_monthly_scores WHERE user_id = ? AND period_year = ? AND period_month = ?");
    $stmtCheck->execute([$userId, $periodYear, $periodMonth]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmtUpdate = $pdo->prepare("UPDATE student_monthly_scores SET
            recorded_date = ?, twk = ?, tiu = ?, tkp = ?, nilai_cat = ?,
            lari_meters = ?, push_up = ?, sit_up = ?, pull_up = ?,
            shuttle_seconds = ?, renang_seconds = ?, renang_distance = ?,
            total_samapta = ?, nilai_total = ?
            WHERE id = ?");
        $stmtUpdate->execute([
            $recordedDate, $twk, $tiu, $tkp, $nilaiCat,
            $lariMeters, $pushUp, $sitUp, $pullUp,
            $shuttle, $renang, $renangDist,
            $totalSamapta, $nilaiTotal, $existing['id']
        ]);
    } else {
        $stmtInsert = $pdo->prepare("INSERT INTO student_monthly_scores
            (user_id, period_year, period_month, recorded_date, twk, tiu, tkp, nilai_cat,
             lari_meters, push_up, sit_up, pull_up, shuttle_seconds, renang_seconds, renang_distance,
             total_samapta, nilai_total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsert->execute([
            $userId, $periodYear, $periodMonth, $recordedDate, $twk, $tiu, $tkp, $nilaiCat,
            $lariMeters, $pushUp, $sitUp, $pullUp, $shuttle, $renang, $renangDist,
            $totalSamapta, $nilaiTotal
        ]);
    }

    header('Location: /admin/nilai?month=' . $periodMonth . '&year=' . $periodYear . '&success=' . urlencode('Nilai siswa berhasil disimpan'));
    exit;
}

/**
 * Handle Admin Delete Nilai
 */
function handleAdminDeleteNilai() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $month = (int) ($_POST['month'] ?? date('n'));
    $year = (int) ($_POST['year'] ?? date('Y'));

    if ($id > 0) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM student_monthly_scores WHERE id = ?");
        $stmt->execute([$id]);
    }

    header('Location: /admin/nilai?month=' . $month . '&year=' . $year . '&success=' . urlencode('Nilai berhasil dihapus'));
    exit;
}
?>
