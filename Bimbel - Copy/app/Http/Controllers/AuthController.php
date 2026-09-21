<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function syncPhpSession($user = null): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if ($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_nickname'] = $user->nickname;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['user_status'] = $user->status;
        } else {
            unset(
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_nickname'],
                $_SESSION['user_email'],
                $_SESSION['user_role'],
                $_SESSION['user_status']
            );
        }
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->syncPhpSession($user);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isPending()) {
                return redirect()->route('pending');
            }
            if ($user->isRejected()) {
                return redirect()->route('rejected');
            }
            return redirect()->route('siswa.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember_me');

        if (!$email || !$password) {
            return redirect('/login?error=' . urlencode('Email dan kata sandi harus diisi.'));
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return redirect('/login?error=' . urlencode('Email atau kata sandi tidak cocok.'));
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $this->syncPhpSession($user);

        if ($user->isPending()) {
            return redirect()->route('pending');
        }
        if ($user->isRejected()) {
            return redirect()->route('rejected');
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $name = trim($request->input('name', ''));
        $nickname = trim($request->input('nickname', ''));
        $whatsapp = trim($request->input('whatsapp', ''));
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $confirmPassword = $request->input('confirm_password', '');

        if (!$name || !$nickname || !$whatsapp || !$email || !$password || !$confirmPassword) {
            return redirect('/register?error=' . urlencode('Semua field formulir harus diisi.'));
        }

        if ($password !== $confirmPassword) {
            return redirect('/register?error=' . urlencode('Konfirmasi kata sandi tidak sesuai.'));
        }

        if (strlen($password) < 8) {
            return redirect('/register?error=' . urlencode('Kata sandi minimal 8 karakter.'));
        }

        if (User::where('email', $email)->exists()) {
            return redirect('/register?error=' . urlencode('Email sudah terdaftar. Silakan login atau gunakan email lain.'));
        }

        if (!$request->hasFile('proof_file') || !$request->file('proof_file')->isValid()) {
            return redirect('/register?error=' . urlencode('Bukti transfer wajib diunggah.'));
        }

        $file = $request->file('proof_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            return redirect('/register?error=' . urlencode('Format file harus JPG, PNG, atau WEBP.'));
        }

        if ($file->getSize() > 2097152) {
            return redirect('/register?error=' . urlencode('Ukuran file maksimal 2 MB.'));
        }

        $uploadDir = public_path('uploads/payments');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $file->move($uploadDir, $filename);
        $savedPath = 'uploads/payments/' . $filename;

        $user = User::create([
            'name' => $name,
            'nickname' => $nickname,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'siswa',
            'status' => 'pending',
        ]);

        Payment::create([
            'user_id' => $user->id,
            'amount' => (int) env('QRIS_AMOUNT', 100000),
            'proof_file' => $savedPath,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->syncPhpSession($user);

        return redirect()->route('pending');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->syncPhpSession(null);

        return redirect('/login?logout=1');
    }

    public function pending()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->syncPhpSession($user);
            if ($user->isActive()) {
                return redirect()->route('siswa.dashboard');
            }
        }

        return view('auth.pending');
    }

    public function rejected()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->syncPhpSession($user);
            if ($user->isActive()) {
                return redirect()->route('siswa.dashboard');
            }
        }

        return view('auth.rejected');
    }

    public function showChangePassword()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/login');
        }

        return view('admin.ganti-password');
    }

    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $currentPassword = $request->input('current_password', '');
        $newPassword = $request->input('new_password', '');
        $confirmPassword = $request->input('confirm_password', '');

        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            return redirect('/admin/ganti-password?error=' . urlencode('Semua kolom kata sandi wajib diisi.'));
        }

        if (!Hash::check($currentPassword, $user->password)) {
            return redirect('/admin/ganti-password?error=' . urlencode('Kata sandi saat ini tidak cocok.'));
        }

        if (strlen($newPassword) < 8) {
            return redirect('/admin/ganti-password?error=' . urlencode('Kata sandi baru minimal 8 karakter.'));
        }

        if ($newPassword !== $confirmPassword) {
            return redirect('/admin/ganti-password?error=' . urlencode('Konfirmasi kata sandi baru tidak sesuai.'));
        }

        if (Hash::check($newPassword, $user->password)) {
            return redirect('/admin/ganti-password?error=' . urlencode('Kata sandi baru tidak boleh sama dengan kata sandi saat ini.'));
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect('/admin/ganti-password?success=' . urlencode('Kata sandi berhasil diperbarui!'));
    }
}
