<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class FreeUserController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $statusSedangDipakai = ['disetujui', 'digunakan', 'sedang digunakan'];

        // Ambil ID Ruangan & Unit yang sedang dipakai HARI INI
        $usedRuanganIds = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idRuangan')
            ->pluck('idRuangan');

        $usedUnitIds = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idUnit')
            ->pluck('idUnit');

        // Data Objek untuk View (Yang Sedang Dipakai)
        $ruangan = \App\Models\Ruangan::whereIn('id', $usedRuanganIds)->get();
        $proyektor = \App\Models\Unit::whereIn('id', $usedUnitIds)->get();

        // Data Objek untuk View (Yang Tersedia / Kosong)
        $availableRuangan = \App\Models\Ruangan::whereNotIn('id', $usedRuanganIds)->get();

        return view('freeuser.home', compact('ruangan', 'proyektor', 'availableRuangan'));
    }
}