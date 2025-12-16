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

        // ================================
        // CHART SARPRAS
        // ================================
        $chartSarpras = [
            'labels' => ['Ruangan/Lab', 'Unit/Proyektor'],
            'data' => [
                Peminjaman::whereNotNull('idRuangan')->count(),
                Peminjaman::whereNotNull('idUnit')->count(),
            ],
        ];

        // ================================
        // CHART USERS
        // ================================
        $chartUsers = [
            'labels' => ['Mahasiswa', 'Dosen', 'Admin'],
            'data' => [
                Peminjaman::whereHas('mahasiswa', fn($q) => $q->where('role','mahasiswa'))->count(),
                Peminjaman::whereHas('mahasiswa', fn($q) => $q->where('role','dosen'))->count(),
                Peminjaman::whereHas('mahasiswa', fn($q) => $q->where('role','admin'))->count(),
            ],
        ];

        // ================================
        // CHART DURASI (REAL FIX)
        // ================================
        $durasiBuckets = [
            '0-2'  => 0,
            '3-5'  => 0,
            '6-10' => 0,
            '>10'  => 0,
        ];

        $rows = DB::table('peminjaman')
            ->whereNotNull('tanggalPinjam')
            ->whereNotNull('jamMulai')
            ->whereNotNull('jamSelesai')
            ->get();

        foreach ($rows as $r) {
            try {
                // Pastikan format jam 'H:i:s' atau 'H:i'
                // Kita gabungkan tanggal dan jam secara manual agar parsing lebih akurat
                $startString = $r->tanggalPinjam . ' ' . $r->jamMulai; // "2023-12-01 10:00:00"
                $endString   = $r->tanggalPinjam . ' ' . $r->jamSelesai;

                // Gunakan parse yang toleran tapi kita paksa format standard jika perlu
                $start = Carbon::parse($startString);
                $end   = Carbon::parse($endString);

                // Handle kasus lintas hari (malam -> pagi)
                // Jika jam selesai lebih kecil dari jam mulai, asumsikan itu besoknya
                // Cek hanya via time string untuk akurasi logika lintas hari
                if ($end->lt($start)) {
                    $end->addDay();
                }

                // Hitung durasi dalam jam (float)
                $minutes = $start->diffInMinutes($end);
                $jam = $minutes / 60;

                // --- LOGIKA BUCKETS ---
                // Pastikan menggunakan float comparison
                if ($jam <= 2.0) {
                    $durasiBuckets['0-2']++;
                } elseif ($jam <= 5.0) {
                    $durasiBuckets['3-5']++;
                } elseif ($jam <= 10.0) {
                    $durasiBuckets['6-10']++;
                } else {
                    $durasiBuckets['>10']++;
                }

            } catch (\Throwable $e) {
                // Jika error parsing, abaikan baris ini
                continue;
            }
        }

        $chartDurasi = [
            'labels' => array_keys($durasiBuckets),
            'data'   => array_values($durasiBuckets),
        ];

        // AJAX
        if ($request->ajax()) {
            return response()->json(compact(
                'totalPeminjaman',
                'totalPending',
                'totalDisetujui',
                'totalDitolak',
                'totalRiwayat',
                'chartSarpras',
                'chartUsers',
                'chartDurasi'
            ));
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
            'chartUsers',
            'chartDurasi'
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
