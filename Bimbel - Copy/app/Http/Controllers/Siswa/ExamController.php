<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return view('siswa.exam');
    }

    public function submit(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan.',
        ]);
    }
}
