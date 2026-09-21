<?php
require_once __DIR__ . '/config/database.php';

$email = 'siswa.pending@bimbelalahaido.com';
$password = 'siswa123';

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Akun demo siswa sudah ada.\n";
        echo "Email: {$email}\n";
        echo "Password: {$password}\n";
        echo "Status: pending\n";
        exit(0);
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, nickname, whatsapp, email, password, role, status) VALUES (?, ?, ?, ?, ?, 'siswa', 'pending')");
    $stmt->execute([
        'Alya Putri',
        'Alya',
        '081234567890',
        $email,
        password_hash($password, PASSWORD_BCRYPT)
    ]);

    $userId = $pdo->lastInsertId();
    $proofFile = 'uploads/payments/payment_demo_pending.png';
    $uploadDir = __DIR__ . '/public/uploads/payments';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (file_exists(__DIR__ . '/public/qris bimbel.jpeg')) {
        copy(__DIR__ . '/public/qris bimbel.jpeg', $uploadDir . '/payment_demo_pending.png');
    }

    $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, proof_file, verified_at) VALUES (?, 100000, ?, NULL)");
    $stmt->execute([$userId, $proofFile]);

    echo "Demo siswa berhasil dibuat.\n";
    echo "Nama: Alya Putri\n";
    echo "Nickname: Alya\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    echo "Status: pending\n";
    echo "Login: http://localhost:8000/login\n";
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
