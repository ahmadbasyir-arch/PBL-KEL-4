<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Unit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil peminjaman berdasarkan user yang sedang login
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

    public function admin()
    {
        // Statistik untuk dashboard admin
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();
        $totalRiwayat = Peminjaman::where('status', 'selesai')->count(); // ✅ Tambahan: total peminjaman selesai

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

        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    public function selesaikanPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        // ✅ Pastikan hanya peminjam yang sah dan status disetujui yang bisa ajukan selesai
        if ($peminjaman->idMahasiswa != $user->id || !in_array($peminjaman->status, ['disetujui', 'digunakan'])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        $peminjaman->status = 'menunggu_validasi';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Pengajuan selesai telah dikirim ke admin.');
    }
}