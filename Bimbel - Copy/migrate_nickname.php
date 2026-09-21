<?php
/**
 * Script untuk migrate kolom nickname ke users yang sudah ada
 * Run: php migrate_nickname.php
 */

require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if nickname column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'nickname'");
    $stmt->execute();
    $exists = $stmt->fetch();
    
    if (!$exists) {
        // Add nickname column
        $pdo->exec("ALTER TABLE users ADD COLUMN nickname VARCHAR(50) NOT NULL DEFAULT 'User' AFTER name");
        echo "✓ Kolom nickname berhasil ditambahkan!\n";
    } else {
        echo "✓ Kolom nickname sudah ada!\n";
    }
    
    // Update existing users - set nickname from first word of name
    $stmt = $pdo->prepare("UPDATE users SET nickname = SUBSTRING_INDEX(name, ' ', 1) WHERE nickname = 'User'");
    $stmt->execute();
    
    echo "✓ Nickname untuk user yang ada sudah diperbarui!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
