<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index()
    {
        return view('admin.soal');
    }
}
