<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL AFTER password");
        echo "Kolom remember_token berhasil ditambahkan.\n";
    } else {
        echo "Kolom remember_token sudah ada.\n";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
