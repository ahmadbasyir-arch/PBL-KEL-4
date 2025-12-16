<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ================================
    // DASHBOARD ADMIN & STAFF
    // ================================
    public function admin(Request $request)
    {
        // Statistik
        $totalPeminjaman = Peminjaman::count();
        $totalPending    = Peminjaman::where('status', 'pending')->count();
        $totalDisetujui  = Peminjaman::where('status', 'disetujui')->count();
        $totalDitolak    = Peminjaman::where('status', 'ditolak')->count();
        $totalRiwayat    = Peminjaman::count();

        // ------------- Data untuk charts (HANYA YANG SEDANG BERJALAN) -------------
        $activeStatuses = ['disetujui', 'digunakan', 'sedang digunakan', 'menunggu_validasi', 'menyelesaikan'];

        // Chart 1: distribusi jenis sarpras (ruangan vs unit) - AKTIF
        try {
            $sarprasLab = Peminjaman::whereIn('status', $activeStatuses)->whereHas('ruangan')->count();
            $sarprasUnit = Peminjaman::whereIn('status', $activeStatuses)->whereHas('unit')->count();
        } catch (\Throwable $e) {
            $sarprasLab = 0;
            $sarprasUnit = 0;
        }

        $chartSarpras = [
            'labels' => ['Ruangan/Lab', 'Unit/Proyektor'],
            'data' => [$sarprasLab, $sarprasUnit],
        ];

        // Chart 2: distribusi peminjam berdasarkan role - AKTIF
        try {
            $totalMahasiswa = Peminjaman::whereIn('status', $activeStatuses)
                ->whereHas('mahasiswa', function($q){
                    $q->where('role', 'mahasiswa');
                })->count();

            $totalDosen = Peminjaman::whereIn('status', $activeStatuses)
                ->whereHas('mahasiswa', function($q){
                    $q->where('role', 'dosen');
                })->count();

            $totalAdmin = Peminjaman::whereIn('status', $activeStatuses)
                ->whereHas('mahasiswa', function($q){
                    $q->where('role', 'admin');
                })->count();
        } catch (\Throwable $e) {
            $totalMahasiswa = $totalDosen = $totalAdmin = 0;
        }

        $chartUsers = [
            'labels' => ['Mahasiswa', 'Dosen', 'Admin'],
            'data' => [$totalMahasiswa, $totalDosen, $totalAdmin],
        ];

        // AJAX
        if ($request->ajax()) {
            return response()->json([
                'totalPeminjaman' => $totalPeminjaman,
                'totalPending' => $totalPending,
                'totalDisetujui' => $totalDisetujui,
                'totalDitolak' => $totalDitolak,
                'totalRiwayat' => $totalRiwayat,
                'chartSarpras' => $chartSarpras,
                'chartUsers' => $chartUsers,
            ]);
        }

        $peminjamanTerkini = Peminjaman::with(['ruangan','unit','mahasiswa'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.dashboard', compact(
            'totalPeminjaman',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'totalRiwayat',
            'peminjamanTerkini',
            'chartSarpras',
            'chartUsers'
        ));
    }

    // ================================
    // ROUTE INDEX
    // ================================
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin','staff','super_admin']))
            return redirect()->route('admin.dashboard');

        if ($user->role === 'mahasiswa')
            return redirect()->route('mahasiswa.dashboard');

        if ($user->role === 'dosen')
            return redirect()->route('dosen.dashboard');

        abort(403);
    }

    // ================================
    // DASHBOARD MAHASISWA
    // ================================
    public function mahasiswa()
    {
        $user = Auth::user();

        $stats = [
            'totalAktif'     => Peminjaman::where('idMahasiswa',$user->id)->whereIn('status',['pending','disetujui'])->count(),
            'totalPending'   => Peminjaman::where('idMahasiswa',$user->id)->where('status','pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa',$user->id)->where('status','disetujui')->count(),
            'totalRiwayat'   => Peminjaman::where('idMahasiswa',$user->id)->count(),
        ];

        $peminjamanTerkini = Peminjaman::with(['ruangan','unit'])
            ->where('idMahasiswa',$user->id)
            ->paginate(10);

        return view('mahasiswa.dashboard', compact('stats','peminjamanTerkini'));
    }

    // ================================
    // DASHBOARD DOSEN
    // ================================
    public function dosen()
    {
        $user = Auth::user();

        $stats = [
            'totalAktif'     => Peminjaman::where('idMahasiswa',$user->id)->whereIn('status',['pending','disetujui'])->count(),
            'totalPending'   => Peminjaman::where('idMahasiswa',$user->id)->where('status','pending')->count(),
            'totalDisetujui' => Peminjaman::where('idMahasiswa',$user->id)->where('status','disetujui')->count(),
            'totalRiwayat'   => Peminjaman::where('idMahasiswa',$user->id)->count(),
        ];

        $peminjamanTerkini = Peminjaman::with(['ruangan','unit'])
            ->where('idMahasiswa',$user->id)
            ->paginate(10);

        return view('dosen.dashboard', compact('stats','peminjamanTerkini'));
    }
}
