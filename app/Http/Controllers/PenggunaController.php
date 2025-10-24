<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PenggunaController extends Controller
{
    public function index()
    {
        $mahasiswa = User::where('role', 'mahasiswa')->orderBy('name')->get();
        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('admin.sidebar-pengguna', compact('mahasiswa', 'dosen'));
    }
}