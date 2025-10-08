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
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'staff') {
            return redirect()->route('staff.dashboard');
        } elseif ($user->role == 'dosen') {
            return redirect()->route('dosen.dashboard');
        } else {
            return redirect()->route('mahasiswa.dashboard');
        }
    }

    /** =======================
     * DASHBOARD ADMIN
     * ======================= */
    public function admin()
    {
        $totalPeminjaman = Peminjaman::count();
        $totalPending = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak = Peminjaman::where('status', 'ditolak')->count();
        $totalMenungguValidasi = Peminjaman::where('status', 'menunggu_validasi')->count();
        $totalRuangan = Ruangan::count();
        $totalUser = User::count();

        $peminjamanTerkini = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'totalMenungguValidasi',
            'totalRuangan',
            'totalUser',
            'peminjamanTerkini'
        ));
    }

    /** =======================
     * DASHBOARD MAHASISWA
     * ======================= */
    public function mahasiswa()
    {
        $userId = Auth::id();

        $stats = [
            'totalAktif' => Peminjaman::where('idMahasiswa', $userId)
                ->whereIn('status', ['pending', 'disetujui', 'menunggu_validasi'])->count(),
            'totalPending' => Peminjaman::where('idMahasiswa', $userId)
                ->where('status', 'pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa', $userId)
                ->where('status', 'disetujui')->count(),
            'totalRiwayat' => Peminjaman::where('idMahasiswa', $userId)->count(),
        ];

        $peminjamanTerkini = Peminjaman::with(['ruangan', 'unit'])
            ->where('idMahasiswa', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('mahasiswa.dashboard', compact('stats', 'peminjamanTerkini'));
    }

    /** =======================
     * SELESAIKAN PEMINJAMAN (Mahasiswa)
     * ======================= */
    public function selesaikanPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Pastikan hanya mahasiswa pemilik peminjaman yang bisa menyelesaikan
        if ($peminjaman->idMahasiswa != Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak menyelesaikan peminjaman ini.');
        }

        // Hanya bisa mengajukan selesai jika status disetujui
        if ($peminjaman->status === 'disetujui') {
            $peminjaman->status = 'menunggu_validasi';
            $peminjaman->save();

            return redirect()->back()->with('success', 'Pengajuan selesai telah dikirim dan menunggu validasi admin.');
        }

        return redirect()->back()->with('error', 'Peminjaman ini tidak dapat diajukan untuk selesai.');
    }
}