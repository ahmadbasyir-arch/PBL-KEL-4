<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard yang sesuai dengan peran pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin' || $user->role == 'staff') {
            return $this->adminDashboard();
        } elseif ($user->role == 'dosen') {
            return $this->dosenDashboard();
        } else {
            return $this->mahasiswaDashboard();
        }
    }

    /**
     * Dashboard Mahasiswa
     */
    private function mahasiswaDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'totalAktif' => DB::table('peminjaman')->where('idMahasiswa', $userId)->whereIn('status', ['pending', 'disetujui'])->count(),
            'totalPending' => DB::table('peminjaman')->where('idMahasiswa', $userId)->where('status', 'pending')->count(),
            'totalDisetujui' => DB::table('peminjaman')->where('idMahasiswa', $userId)->where('status', 'disetujui')->count(),
            'totalRiwayat' => DB::table('peminjaman')->where('idMahasiswa', $userId)->count(),
        ];
        
        $peminjamanTerkini = DB::table('peminjaman as p')
            ->leftJoin('ruangan as r', 'p.idRuangan', '=', 'r.id')
            ->leftJoin('unit as u', 'p.idUnit', '=', 'u.id')
            ->where('p.idMahasiswa', $userId)
            ->select('p.id', 'p.tanggalPinjam', 'p.status', 'p.keperluan', 'r.namaRuangan', 'u.namaUnit')
            ->orderByDesc('p.created_at')
            ->limit(5)
            ->get();
            
        return view('mahasiswa.dashboard', [
            'stats' => $stats,
            'peminjamanTerkini' => $peminjamanTerkini
        ]);
    }

    /**
     * Dashboard Dosen
     */
    private function dosenDashboard()
    {
        // Bisa dikembangkan nanti sesuai kebutuhan
        return view('dosen.dashboard');
    }

    /**
     * Dashboard Admin/Staff
     */
    private function adminDashboard()
    {
        // Data statistik untuk admin
        $jumlahPeminjaman = Peminjaman::count();
        $menunggu = Peminjaman::where('status', 'Menunggu')->count();
        $disetujui = Peminjaman::where('status', 'Disetujui')->count();
        $ditolak = Peminjaman::where('status', 'Ditolak')->count();

        return view('admin.dashboard', compact('jumlahPeminjaman', 'menunggu', 'disetujui', 'ditolak'));
    }
}