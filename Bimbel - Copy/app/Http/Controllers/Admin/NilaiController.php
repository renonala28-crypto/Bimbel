<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentMonthlyScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NilaiController extends Controller
{
    public function index()
    {
        return view('admin.nilai');
    }

    public function save(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);
        $studentNameNew = trim($request->input('student_name_new', ''));
        $periodMonth = (int) $request->input('period_month', date('n'));
        $periodYear = (int) $request->input('period_year', date('Y'));
        $recordedDate = $request->input('recorded_date', date('Y-m-d'));

        if ($userId <= 0 && $studentNameNew !== '') {
            $user = User::create([
                'name' => $studentNameNew,
                'nickname' => explode(' ', $studentNameNew)[0],
                'whatsapp' => '08' . rand(1000000000, 9999999999),
                'email' => 'siswa_' . time() . '_' . rand(100, 999) . '@bimbelalahaido.com',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'status' => 'active',
            ]);
            $userId = $user->id;
        }

        if ($userId <= 0) {
            return redirect('/admin/nilai?error=' . urlencode('Pilih siswa atau masukkan nama siswa baru.'));
        }

        $twk = (float) str_replace(',', '.', $request->input('twk', '0'));
        $tiu = (float) str_replace(',', '.', $request->input('tiu', '0'));
        $tkp = (float) str_replace(',', '.', $request->input('tkp', '0'));
        $nilaiCat = (float) str_replace(',', '.', $request->input('nilai_cat', '0'));
        if ($nilaiCat <= 0 && ($twk > 0 || $tiu > 0 || $tkp > 0)) {
            $nilaiCat = $twk + $tiu + $tkp;
        }

        $lariMeters = (int) $request->input('lari_meters', 0);
        $pushUp = (int) $request->input('push_up', 0);
        $sitUp = (int) $request->input('sit_up', 0);
        $pullUp = (int) $request->input('pull_up', 0);
        $shuttle = (float) str_replace(',', '.', $request->input('shuttle_seconds', '0'));
        $renang = (float) str_replace(',', '.', $request->input('renang_seconds', '0'));
        $renangDist = (int) $request->input('renang_distance', 25);
        $totalSamapta = (float) str_replace(',', '.', $request->input('total_samapta', '0'));
        $nilaiTotal = (float) str_replace(',', '.', $request->input('nilai_total', '0'));

        StudentMonthlyScore::updateOrCreate(
            [
                'user_id' => $userId,
                'period_year' => $periodYear,
                'period_month' => $periodMonth,
            ],
            [
                'recorded_date' => $recordedDate,
                'twk' => $twk,
                'tiu' => $tiu,
                'tkp' => $tkp,
                'nilai_cat' => $nilaiCat,
                'lari_meters' => $lariMeters,
                'push_up' => $pushUp,
                'sit_up' => $sitUp,
                'pull_up' => $pullUp,
                'shuttle_seconds' => $shuttle,
                'renang_seconds' => $renang,
                'renang_distance' => $renangDist,
                'total_samapta' => $totalSamapta,
                'nilai_total' => $nilaiTotal,
            ]
        );

        return redirect('/admin/nilai?month=' . $periodMonth . '&year=' . $periodYear . '&success=' . urlencode('Nilai siswa berhasil disimpan.'));
    }

    public function destroy(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        if ($id > 0) {
            StudentMonthlyScore::where('id', $id)->delete();
        }

        return redirect('/admin/nilai?month=' . $month . '&year=' . $year . '&success=' . urlencode('Nilai berhasil dihapus.'));
    }
}
