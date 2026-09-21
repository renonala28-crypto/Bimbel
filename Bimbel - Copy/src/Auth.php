<?php
/**
 * Authentication Handler
 */

namespace App;

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    public function register($name, $nickname, $whatsapp, $email, $password, $proof_file) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new \Exception("Email sudah terdaftar");
            }

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, nickname, whatsapp, email, password, role, status)
                VALUES (?, ?, ?, ?, ?, 'siswa', 'pending')
            ");
            $stmt->execute([$name, $nickname, $whatsapp, $email, $hashed_password]);
            $user_id = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("
                INSERT INTO payments (user_id, proof_file)
                VALUES (?, ?)
            ");
            $stmt->execute([$user_id, $proof_file]);

            return [
                'success' => true,
                'user_id' => $user_id,
                'message' => 'Pendaftaran berhasil! Silakan tunggu verifikasi.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getPendingUsers() {
        $stmt = $this->pdo->prepare("
            SELECT u.*, p.proof_file, p.amount, p.verified_at
            FROM users u
            LEFT JOIN payments p ON p.user_id = u.id
            WHERE u.role = 'siswa' AND u.status = 'pending'
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function approveUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET status = 'active', rejected_reason = NULL, rejected_at = NULL WHERE id = ? AND role = 'siswa'");
            $stmt->execute([$userId]);

            $stmt = $this->pdo->prepare("UPDATE payments SET verified_at = NOW() WHERE user_id = ?");
            $stmt->execute([$userId]);

            return [
                'success' => true,
                'message' => 'Akun siswa berhasil disetujui.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function rejectUser($userId, $reason = 'Akun Anda ditolak karena data pembayaran atau dokumen tidak memenuhi syarat.') {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET status = 'rejected', rejected_reason = ?, rejected_at = NOW() WHERE id = ? AND role = 'siswa'");
            $stmt->execute([$reason, $userId]);

            return [
                'success' => true,
                'message' => 'Akun siswa ditolak dan akan menerima peringatan bahwa data mereka ditolak.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAdminSummary() {
        $pending = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'pending'")->fetchColumn();
        $active = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'active'")->fetchColumn();
        $rejected = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND status = 'rejected'")->fetchColumn();
        $total = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'siswa'")->fetchColumn();

        return [
            'pending' => (int) $pending,
            'active' => (int) $active,
            'rejected' => (int) $rejected,
            'total' => (int) $total,
        ];
    }

    public function login($email, $password, $rememberMe = false) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, nickname, email, password, role, status
                FROM users
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                throw new \Exception("Email atau password salah");
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_nickname'] = $user['nickname'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_status'] = $user['status'];

            $token = null;
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $_SESSION['remember_token'] = $token;
                setcookie('bimbel_remember', $token, time() + (60 * 60 * 24 * 30), '/', '', false, true);
            } else {
                unset($_SESSION['remember_token']);
                setcookie('bimbel_remember', '', time() - 3600, '/');
            }

            if ($rememberMe) {
                $updateStmt = $this->pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $updateStmt->execute([$token, $user['id']]);
            } else {
                $clearStmt = $this->pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
                $clearStmt->execute([$user['id']]);
            }

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Login berhasil'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function user() {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'status' => $_SESSION['user_status']
        ];
    }

    public static function isAdmin() {
        return self::check() && $_SESSION['user_role'] === 'admin';
    }
}
?>

