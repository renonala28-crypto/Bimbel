<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index()
    {
        return view('admin.verifikasi');
    }

    public function verify(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);
        $action = $request->input('action', '');

        if (!$userId || !in_array($action, ['approve', 'reject'], true)) {
            return redirect('/admin/verifikasi?error=' . urlencode('Data verifikasi tidak valid.'));
        }

        $user = User::where('id', $userId)->where('role', 'siswa')->first();
        if (!$user) {
            return redirect('/admin/verifikasi?error=' . urlencode('Siswa tidak ditemukan.'));
        }

        if ($action === 'approve') {
            $user->update([
                'status' => 'active',
                'rejected_reason' => null,
                'rejected_at' => null,
            ]);

            Payment::where('user_id', $userId)->update([
                'verified_at' => now(),
            ]);

            return redirect('/admin/verifikasi?success=' . urlencode('Akun siswa ' . $user->name . ' berhasil disetujui.'));
        } else {
            $reason = 'Akun Anda ditolak karena data yang dikirim tidak memenuhi persyaratan.';
            $user->update([
                'status' => 'rejected',
                'rejected_reason' => $reason,
                'rejected_at' => now(),
            ]);

            return redirect('/admin/verifikasi?success=' . urlencode('Akun siswa ' . $user->name . ' berhasil ditolak.'));
        }
    }
}
