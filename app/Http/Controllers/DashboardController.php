<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Kalau admin atau staff, arahkan ke dashboard admin
        if (in_array($user->role, ['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        // Kalau mahasiswa atau dosen, arahkan ke dashboard mahasiswa
        if (in_array($user->role, ['mahasiswa', 'dosen'])) {
            return redirect()->route('mahasiswa.dashboard');
        }

        // Default
        abort(403, 'Role tidak dikenali.');
    }

    public function admin()
    {
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();
        $totalRiwayat = Peminjaman::where('status', 'selesai')->count();

        $peminjamanTerkini = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'totalRiwayat',
            'peminjamanTerkini'
        ));
    }

    public function mahasiswa()
    {
        $user = Auth::user();

        $stats = [
            'totalAktif' => Peminjaman::where('idMahasiswa', $user->id)
                ->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])
                ->count(),
            'totalPending' => Peminjaman::where('idMahasiswa', $user->id)
                ->where('status', 'pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa', $user->id)
                ->where('status', 'disetujui')->count(),
            'totalRiwayat' => Peminjaman::where('idMahasiswa', $user->id)->count(),
        ];

        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
            ->where('idMahasiswa', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }
}