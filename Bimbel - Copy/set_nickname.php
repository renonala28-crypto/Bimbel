<?php
/**
 * Script untuk set nickname untuk user tertentu
 * Run: php set_nickname.php
 */

require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Update nickname untuk user renonala28@gmail.com
    $stmt = $pdo->prepare("UPDATE users SET nickname = ? WHERE email = ?");
    $stmt->execute(['Reno', 'renonala28@gmail.com']);
    
    // Verify
    $stmt = $pdo->prepare("SELECT id, name, nickname, email FROM users WHERE email = ?");
    $stmt->execute(['renonala28@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✓ Nickname berhasil diperbarui!\n";
        echo "Nama: {$user['name']}\n";
        echo "Nickname: {$user['nickname']}\n";
        echo "Email: {$user['email']}\n";
    } else {
        echo "User tidak ditemukan\n";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
