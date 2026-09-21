<?php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

class KalkulatorSamaptaController extends Controller
{
    public function index()
    {
        return view('siswa.kalkulator-samapta');
    }
}
