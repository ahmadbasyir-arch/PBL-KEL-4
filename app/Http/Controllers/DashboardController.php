<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Unit;

class DashboardController extends Controller
{
    /**
     * Mengarahkan pengguna ke dashboard yang sesuai dengan perannya.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'admin' || $user->role == 'staff') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'dosen') {
            return redirect()->route('dosen.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk ADMIN.
     */
    public function admin()
    {
        // [PERBAIKAN] Menghitung semua statistik yang dibutuhkan oleh view
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();

        // Ambil data peminjaman terbaru (semua status) untuk ditampilkan di tabel
        $peminjamanTerkini = Peminjaman::with('user', 'ruangan', 'unit')
            ->orderByDesc('created_at')
            ->limit(10) // Tampilkan 10 data terbaru
            ->get();

        return view('admin.dashboard', compact(
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'peminjamanTerkini'
        ));
    }

    /**
     * Menyiapkan data dan menampilkan dashboard untuk MAHASISWA.
     */
    public function mahasiswa()
    {
        $userId = Auth::id();
        $stats = [
            'totalAktif'   => Peminjaman::where('idMahasiswa', $userId)->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])->count(),
            'totalPending' => Peminjaman::where('idMahasiswa', $userId)->where('status', 'pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa', $userId)->where('status', 'disetujui')->count(),
            'totalRiwayat' => Peminjaman::where('idMahasiswa', $userId)->count(),
        ];
        
        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
            ->where('idMahasiswa', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
            
        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    /**
     * Mahasiswa mengajukan penyelesaian peminjaman.
     */
    public function selesaikanPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        if ($peminjaman->idMahasiswa != Auth::id() || $peminjaman->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        $peminjaman->status = 'menunggu_validasi';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Pengajuan selesai telah dikirim.');
    }
}