<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Unit;
use Carbon\Carbon; // ✅ Tambahan penting untuk format tanggal

class DashboardController extends Controller
{
    /**
     * Mengarahkan pengguna ke dashboard yang sesuai dengan perannya.
     */
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        // Default nilai jika mahasiswa belum terdaftar
        $stats = [
            'totalAktif' => 0,
            'totalPending' => 0,
            'totalDisetujui' => 0,
            'totalRiwayat' => 0,
        ];
        $peminjamanTerkini = collect();

        if ($mahasiswa) {
            $mahasiswaId = $mahasiswa->id;

            $stats = [
                'totalAktif'   => Peminjaman::where('idMahasiswa', $mahasiswaId)->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])->count(),
                'totalPending' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'pending')->count(),
                'totalDisetujui' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'disetujui')->count(),
                'totalRiwayat' => Peminjaman::where('idMahasiswa', $mahasiswaId)->count(),
            ];
    
            $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
                ->where('idMahasiswa', $mahasiswaId)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    /**
     * Dashboard ADMIN.
     */
    public function admin()
    {
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();

        $peminjamanTerkini = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->orderByDesc('created_at')
            ->limit(10)
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
     * Dashboard MAHASISWA.
     */
    public function mahasiswa()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            $stats = [
                'totalAktif'   => 0,
                'totalPending' => 0,
                'totalDisetujui' => 0,
                'totalRiwayat' => 0,
            ];
            $peminjamanTerkini = collect();
            return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
        }

        $mahasiswaId = $mahasiswa->id;

        $stats = [
            'totalAktif'   => Peminjaman::where('idMahasiswa', $mahasiswaId)->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])->count(),
            'totalPending' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'disetujui')->count(),
            'totalRiwayat' => Peminjaman::where('idMahasiswa', $mahasiswaId)->count(),
        ];
        
        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
            ->where('idMahasiswa', $mahasiswaId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
            
        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    /**
     * Dashboard DOSEN — dibuat sama seperti mahasiswa.
     */
    public function dosen()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            $stats = [
                'totalAktif'   => 0,
                'totalPending' => 0,
                'totalDisetujui' => 0,
                'totalRiwayat' => 0,
            ];
            $peminjamanTerkini = collect();
            return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
        }

        $mahasiswaId = $mahasiswa->id;

        $stats = [
            'totalAktif'   => Peminjaman::where('idMahasiswa', $mahasiswaId)->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])->count(),
            'totalPending' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa', $mahasiswaId)->where('status', 'disetujui')->count(),
            'totalRiwayat' => Peminjaman::where('idMahasiswa', $mahasiswaId)->count(),
        ];
        
        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
            ->where('idMahasiswa', $mahasiswaId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
            
        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    /**
     * Mahasiswa/Dosen mengajukan penyelesaian peminjaman.
     */
    public function selesaikanPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $mahasiswaId = $mahasiswa->id ?? null;

        // ✅ Pastikan hanya peminjam yang sah dan status disetujui yang bisa ajukan selesai
        if ($peminjaman->idMahasiswa != $mahasiswaId || !in_array($peminjaman->status, ['disetujui', 'digunakan'])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        $peminjaman->status = 'menunggu_validasi';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Pengajuan selesai telah dikirim ke admin.');
    }
}