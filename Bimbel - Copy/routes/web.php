<?php

use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MateriController as AdminMateriController;
use App\Http\Controllers\Admin\NilaiController as AdminNilaiController;
use App\Http\Controllers\Admin\SoalController as AdminSoalController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use App\Http\Controllers\Siswa\LeaderboardController as SiswaLeaderboardController;
use App\Http\Controllers\Siswa\MateriController as SiswaMateriController;
use App\Http\Controllers\Siswa\PerkembanganController as SiswaPerkembanganController;
use App\Http\Controllers\LaporanPerkembanganController;
use App\Http\Controllers\IzinMateriController;
use App\Http\Controllers\Siswa\KalkulatorSamaptaController as SiswaKalkulatorSamaptaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root landing page / dispatcher
Route::get('/', function () {
    if (!Auth::check()) {
        return view('landing');
    }

    $user = Auth::user();
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
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/api/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/api/register', [AuthController::class, 'register']);

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/pending', [AuthController::class, 'pending'])->name('pending');
Route::get('/rejected', [AuthController::class, 'rejected'])->name('rejected');

// General /dashboard dispatcher
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
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
})->middleware('auth')->name('dashboard');

// Siswa Protected Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('/exam', [SiswaExamController::class, 'index'])->name('siswa.exam');
    Route::post('/api/exam/submit', [SiswaExamController::class, 'submit']);
    Route::get('/materi', [SiswaMateriController::class, 'index'])->name('siswa.materi');
    Route::get('/perkembangan', [SiswaPerkembanganController::class, 'index'])->name('siswa.perkembangan');
    Route::get('/leaderboard', [SiswaLeaderboardController::class, 'index'])->name('siswa.leaderboard');
    Route::get('/kalkulator-samapta', [SiswaKalkulatorSamaptaController::class, 'index'])->name('siswa.kalkulator-samapta');
    Route::get('/sudoku', function () {
        return redirect()->route('siswa.dashboard');
    });
    Route::post('/api/sudoku/save', function () {
        return response()->json(['success' => true]);
    });
});

// Admin Protected Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/verifikasi', [AdminVerifikasiController::class, 'index'])->name('admin.verifikasi');
    Route::post('/api/admin/verify', [AdminVerifikasiController::class, 'verify']);
    Route::get('/admin/absensi', [AdminAbsensiController::class, 'index'])->name('admin.absensi');
    Route::post('/admin/absensi', [AdminAbsensiController::class, 'index']);
    Route::get('/admin/nilai', [AdminNilaiController::class, 'index'])->name('admin.nilai');
    Route::get('/admin/nilai-fisik', [AdminNilaiController::class, 'index'])->name('admin.nilai-fisik');
    Route::post('/api/admin/nilai/save', [AdminNilaiController::class, 'save']);
    Route::post('/api/admin/nilai/delete', [AdminNilaiController::class, 'destroy']);
    Route::get('/admin/soal', [AdminSoalController::class, 'index'])->name('admin.soal');
    Route::get('/admin/konten', [AdminMateriController::class, 'index'])->name('admin.konten');
    Route::get('/admin/materi', function () {
        return redirect()->route('admin.konten');
    })->name('admin.materi');
    Route::post('/api/admin/materi/save', [AdminMateriController::class, 'storeMateri']);
    Route::post('/api/admin/materi/update', [AdminMateriController::class, 'updateMateri']);
    Route::post('/api/admin/materi/toggle', [AdminMateriController::class, 'toggleMateri']);
    Route::post('/api/admin/materi/delete', [AdminMateriController::class, 'deleteMateri']);
    Route::post('/api/admin/cat/save', [AdminMateriController::class, 'storeCat']);
    Route::post('/api/admin/cat/update', [AdminMateriController::class, 'updateCat']);
    Route::post('/api/admin/cat/toggle', [AdminMateriController::class, 'toggleCat']);
    Route::post('/api/admin/cat/delete', [AdminMateriController::class, 'deleteCat']);
    Route::post('/api/admin/konten/permissions', [AdminMateriController::class, 'updatePermissions']);
    Route::get('/admin/izin-konten', [IzinMateriController::class, 'index'])->name('admin.izinkonten');
    Route::get('/admin/izinkonten', [IzinMateriController::class, 'index']);
    Route::get('/admin/izin-materi', function () {
        return redirect()->route('admin.izinkonten');
    });
    Route::get('/admin/izinmateri', function () {
        return redirect()->route('admin.izinkonten');
    });
    Route::post('/api/admin/siswa/update', [AdminDashboardController::class, 'updateSiswa']);
    Route::post('/api/admin/siswa/deactivate', [AdminDashboardController::class, 'deactivateSiswa']);
    Route::get('/laporan-perkembangan', function () {
        return redirect()->route('admin.detail-laporan');
    });
    Route::get('/admin/laporan-perkembangan', function () {
        return redirect()->route('admin.detail-laporan');
    });
    Route::get('/admin/laporanPerkembangan', function () {
        return redirect()->route('admin.detail-laporan');
    });
    Route::get('/admin/detail-laporan', [LaporanPerkembanganController::class, 'detail'])->name('admin.detail-laporan');
    Route::get('/admin/detail-laporan-perkembangan', [LaporanPerkembanganController::class, 'detail']);
    Route::get('/admin/ganti-password', [AuthController::class, 'showChangePassword'])->name('admin.ganti-password');
    Route::post('/api/admin/ganti-password', [AuthController::class, 'changePassword'])->name('api.admin.ganti-password');
});

// Alias redirects
Route::get('/izinkonten', function () {
    return redirect()->route('admin.izinkonten');
});
Route::get('/izinmateri', function () {
    return redirect()->route('admin.izinkonten');
});
Route::get('/konten', function () {
    return redirect()->route('admin.konten');
});
Route::get('/laporanPerkembangan', function () {
    return redirect('/admin/detail-laporan');
});
Route::get('/detail-laporan', function () {
    return redirect('/admin/detail-laporan');
});
Route::get('/detail-laporan-perkembangan', function () {
    return redirect('/admin/detail-laporan');
});
Route::get('/ganti-password', function () {
    return redirect('/admin/ganti-password');
});