<?php
/**
 * Database Migration
 * Run this file to create all required database tables
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

$migrations = [
    // Tabel Users
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        nickname VARCHAR(50) NOT NULL,
        whatsapp VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        remember_token VARCHAR(255) NULL,
        role ENUM('admin', 'siswa') DEFAULT 'siswa',
        status ENUM('pending', 'active', 'rejected') DEFAULT 'pending',
        rejected_reason TEXT NULL,
        rejected_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_role (role),
        INDEX idx_status (status),
        INDEX idx_remember_token (remember_token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Payments
    "CREATE TABLE IF NOT EXISTS payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        amount INT DEFAULT 100000,
        proof_file VARCHAR(255) NOT NULL,
        verified_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_verified (verified_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Questions (Bank Soal)
    "CREATE TABLE IF NOT EXISTS questions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        question_text LONGTEXT NOT NULL,
        option_a TEXT NOT NULL,
        option_b TEXT NOT NULL,
        option_c TEXT NOT NULL,
        option_d TEXT NOT NULL,
        correct_option CHAR(1) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Exam Sessions
    "CREATE TABLE IF NOT EXISTS exam_sessions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        exam_name VARCHAR(150) NOT NULL,
        duration_minutes INT DEFAULT 90,
        total_questions INT,
        passing_score INT DEFAULT 70,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Exam Results
    "CREATE TABLE IF NOT EXISTS exam_results (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        exam_id INT NOT NULL,
        score INT,
        correct_answers INT,
        duration_spent INT,
        completed_at TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (exam_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_exam_id (exam_id),
        INDEX idx_completed (completed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Materials
    "CREATE TABLE IF NOT EXISTS materials (
        id INT PRIMARY KEY AUTO_INCREMENT,
        category VARCHAR(50) NOT NULL,
        title VARCHAR(150) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_size INT,
        uploaded_by INT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Sudoku Logs
    "CREATE TABLE IF NOT EXISTS sudoku_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        score INT,
        duration_seconds INT,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_completed (completed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Attendance
    "CREATE TABLE IF NOT EXISTS attendance (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        status ENUM('hadir', 'absen', 'izin') DEFAULT 'absen',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_attendance (user_id, attendance_date),
        INDEX idx_user_id (user_id),
        INDEX idx_date (attendance_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Physical Performance
    "CREATE TABLE IF NOT EXISTS physical_performance (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        lari_distance_km DECIMAL(5,2),
        lari_time_minutes INT,
        renang_distance_m DECIMAL(5,2),
        renang_time_minutes INT,
        recorded_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_date (recorded_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // Tabel Student Monthly Scores (Rekap Nilai Bulanan CAT & Samapta)
    "CREATE TABLE IF NOT EXISTS student_monthly_scores (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        student_name_custom VARCHAR(150) NULL,
        period_year INT NOT NULL,
        period_month INT NOT NULL,
        recorded_date DATE NOT NULL,
        twk DECIMAL(6,2) DEFAULT 0,
        tiu DECIMAL(6,2) DEFAULT 0,
        tkp DECIMAL(6,2) DEFAULT 0,
        nilai_cat DECIMAL(6,2) DEFAULT 0,
        lari_meters INT DEFAULT 0,
        push_up INT DEFAULT 0,
        sit_up INT DEFAULT 0,
        pull_up INT DEFAULT 0,
        shuttle_seconds DECIMAL(5,2) DEFAULT 0,
        renang_seconds DECIMAL(5,2) DEFAULT 0,
        renang_distance INT DEFAULT 25,
        total_samapta DECIMAL(6,2) DEFAULT 0,
        nilai_total DECIMAL(6,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_period (period_year, period_month),
        INDEX idx_user_period (user_id, period_year, period_month)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

$schemaUpdates = [
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS rejected_reason TEXT NULL AFTER status",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS rejected_at TIMESTAMP NULL AFTER rejected_reason",
    "ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'rejected', 'inactive') NOT NULL DEFAULT 'pending'",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER title",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS type VARCHAR(50) DEFAULT 'teks' AFTER description",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS content LONGTEXT NULL AFTER type",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS allowed_students TEXT NULL AFTER content",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS status ENUM('aktif', 'nonaktif') DEFAULT 'aktif' AFTER allowed_students",
    "ALTER TABLE materials ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status",
    "ALTER TABLE materials MODIFY COLUMN file_path VARCHAR(255) NULL",
    "ALTER TABLE exam_sessions ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'Umum' AFTER exam_name",
    "ALTER TABLE exam_sessions ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER category",
    "ALTER TABLE exam_sessions ADD COLUMN IF NOT EXISTS show_discussion TINYINT(1) DEFAULT 1 AFTER passing_score",
    "ALTER TABLE exam_sessions ADD COLUMN IF NOT EXISTS allowed_students TEXT NULL AFTER show_discussion",
    "ALTER TABLE exam_sessions ADD COLUMN IF NOT EXISTS status ENUM('aktif', 'nonaktif') DEFAULT 'nonaktif' AFTER allowed_students",
];

try {
    foreach ($migrations as $sql) {
        $pdo->exec($sql);
        echo "✓ Migration executed successfully\n";
    }

    foreach ($schemaUpdates as $sql) {
        $pdo->exec($sql);
        echo "✓ Schema updated successfully\n";
    }

    echo "\n✓✓✓ All tables created successfully! ✓✓✓\n";
} catch (PDOException $e) {
    die("Migration Error: " . $e->getMessage());
}
?>
