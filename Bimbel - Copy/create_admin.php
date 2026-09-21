<?php
/**
 * Script untuk membuat admin default
 * Run: php create_admin.php
 */

require_once __DIR__ . '/config/database.php';

$email = 'admin@bimbelalahaido.com';
$password = 'admin123';

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin', status = 'active', password = ? WHERE email = ?");
        $stmt->execute([password_hash($password, PASSWORD_BCRYPT), $email]);
        echo "Admin sudah ada. Data admin diupdate.\n";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, nickname, whatsapp, email, password, role, status)
            VALUES (?, ?, ?, ?, ?, 'admin', 'active')
        ");
        $stmt->execute([
            'Admin Bimbel Alahaido',
            'Admin',
            '081234567890',
            $email,
            password_hash($password, PASSWORD_BCRYPT)
        ]);
        echo "Admin berhasil dibuat.\n";
    }

    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Login: http://localhost:8000/login\n";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
