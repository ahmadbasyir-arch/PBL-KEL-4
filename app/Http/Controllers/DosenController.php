<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Auth;

class DosenController extends Controller
{
    public function riwayat()
    {
        $user = Auth::user();

        $riwayat = Peminjaman::where('id_dosen', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('dosen.riwayat', compact('riwayat'));
    }
}
