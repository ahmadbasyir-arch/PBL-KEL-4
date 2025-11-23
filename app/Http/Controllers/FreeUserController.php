<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class FreeUserController extends Controller
{
    public function index()
    {
        $statusSedangDipakai = ['disetujui', 'digunakan'];

        $ruangan = Peminjaman::with('ruangan')
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idRuangan')
            ->get()
            ->pluck('ruangan')
            ->filter()
            ->unique('id')
            ->values();

        $proyektor = Peminjaman::with('unit')
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idUnit')
            ->get()
            ->pluck('unit')
            ->filter()
            ->unique('id')
            ->values();

        return view('freeuser.home', compact('ruangan', 'proyektor'));
    }
}