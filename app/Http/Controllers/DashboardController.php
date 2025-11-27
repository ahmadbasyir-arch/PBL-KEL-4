<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ================================
    // DASHBOARD ADMIN & STAFF
    // ================================
    public function admin()
    {
        // Hitung statistik
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();
        $totalRiwayat = Peminjaman::count(); // opsional

        // Data peminjaman terbaru
        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit', 'mahasiswa'])
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

    public function index()
    {
        $user = Auth::user();

        // ADMIN & STAFF
        if (in_array($user->role, ['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        // MAHASISWA
        if ($user->role == 'mahasiswa') {
            return redirect()->route('mahasiswa.dashboard');
        }

        // DOSEN
        if ($user->role == 'dosen') {
            return redirect()->route('dosen.dashboard');
        }

        // Tidak dikenali
        abort(403, 'Role tidak dikenali.');
    }

    // ================================
    // DASHBOARD MAHASISWA
    // ================================
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

    // ================================
    // DASHBOARD DOSEN
    // ================================
    public function dosen()
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

        return view('dosen.dashboard', compact('stats', 'peminjamanTerkini'));
    }
}
