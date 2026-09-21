<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function updateSiswa(Request $request)
    {
        $id = (int) $request->input('id');
        $user = User::where('id', $id)->where('role', 'siswa')->first();
        if (!$user) {
            return redirect('/admin/dashboard?error=' . urlencode('Siswa tidak ditemukan.'));
        }

        $user->name = trim($request->input('name', $user->name));
        $user->whatsapp = trim($request->input('whatsapp', $user->whatsapp));
        $user->whatsapp_ortu = trim($request->input('whatsapp_ortu', ''));
        $user->sekolah_asal = trim($request->input('sekolah_asal', ''));
        $user->program_tujuan = trim($request->input('program_tujuan', ''));
        $user->tempat_lahir = trim($request->input('tempat_lahir', ''));
        $user->tanggal_lahir = $request->input('tanggal_lahir') ?: null;
        $user->nama_paket = trim($request->input('nama_paket', ''));
        $user->harga_paket = (float) str_replace(',', '.', $request->input('harga_paket', '0'));
        $user->tanggal_mulai = $request->input('tanggal_mulai') ?: null;
        $user->alamat = trim($request->input('alamat', ''));

        if (!empty($user->name)) {
            $user->nickname = explode(' ', $user->name)[0];
        }

        $user->save();

        return redirect('/admin/dashboard?success=' . urlencode('Data siswa ' . $user->name . ' berhasil diperbarui.'));
    }

    public function deactivateSiswa(Request $request)
    {
        $id = (int) $request->input('id');
        $user = User::where('id', $id)->where('role', 'siswa')->first();
        if (!$user) {
            return redirect('/admin/dashboard?error=' . urlencode('Siswa tidak ditemukan.'));
        }

        $user->status = 'inactive';
        $user->save();

        return redirect('/admin/dashboard?success=' . urlencode('Siswa ' . $user->name . ' berhasil dinonaktifkan.'));
    }
}
