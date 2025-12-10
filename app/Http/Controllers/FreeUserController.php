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
            ->pluck('idRuangan')
            ->toArray();

        $usedUnitIds = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idUnit')
            ->pluck('idUnit')
            ->toArray();

        // Ambil SEMUA Data Ruangan & Unit
        $allRuangan = \App\Models\Ruangan::orderBy('namaRuangan')->get();
        $allUnits = \App\Models\Unit::orderBy('namaUnit')->get();

        // Map status untuk Ruangan
        $allRuangan->map(function ($room) use ($usedRuanganIds) {
            $room->status = in_array($room->id, $usedRuanganIds) ? 'dipakai' : 'tersedia';
            return $room;
        });

        // Map status untuk Unit (Proyektor)
        $allUnits->map(function ($unit) use ($usedUnitIds) {
            $unit->status = in_array($unit->id, $usedUnitIds) ? 'dipakai' : 'tersedia';
            return $unit;
        });

        // Hitung statistik
        $ruanganDipakai = count($usedRuanganIds);
        $unitDipakai = count($usedUnitIds);

        return view('freeuser.home', compact('allRuangan', 'allUnits', 'ruanganDipakai', 'unitDipakai'));
    }
}