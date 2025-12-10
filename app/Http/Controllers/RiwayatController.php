<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil semua riwayat user
        $allHistory = Peminjaman::where('idMahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika lebih dari 30, hapus sisanya (yang paling lama)
        if ($allHistory->count() > 30) {
            $idsToDelete = $allHistory->slice(30)->pluck('id'); 
            Peminjaman::whereIn('id', $idsToDelete)->delete();
        }

        // Ambil ulang 30 data terbaru
        $riwayat = Peminjaman::with(['ruangan', 'unit', 'feedback']) // Load relation feedback
            ->where('idMahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('riwayat.index', compact('riwayat'));
    }
}
