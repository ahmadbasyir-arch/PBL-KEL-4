<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Auth;

class MahasiswaController extends Controller
{
    public function riwayat()
    {
        $user = Auth::user();

        $riwayat = Peminjaman::where('idMahasiswa', $user->idMahasiswa)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('mahasiswa.riwayat', compact('riwayat'));
    }
}
