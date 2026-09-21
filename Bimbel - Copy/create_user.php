<?php
/**
 * Script untuk membuat user baru
 * Run: php create_user.php
 */

require_once __DIR__ . '/config/database.php';

// User data
$name = "User Baru";
$nickname = "Newbie";
$whatsapp = "0812345678";
$email = "renonala28@gmail.com";
$password = "arekelek123";
$role = "siswa";
$status = "active";

try {
    $pdo = getDBConnection();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, nickname, whatsapp, email, password, role, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$name, $nickname, $whatsapp, $email, $hashed_password, $role, $status]);
    $user_id = $pdo->lastInsertId();
    
    echo "✓ User berhasil dibuat!\n";
    echo "User ID: $user_id\n";
    echo "Nama: $name\n";
    echo "Nama Panggilan: $nickname\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    echo "Role: $role\n";
    echo "Status: $status\n";
    echo "\nAnda bisa login di http://localhost:8000/login\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
