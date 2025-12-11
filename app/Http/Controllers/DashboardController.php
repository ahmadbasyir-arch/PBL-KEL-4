<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            ->paginate(10);

        // ------------- Data untuk charts -------------
        // Chart 1: distribusi jenis sarpras (ruangan vs unit)
        try {
            $sarprasLab = Peminjaman::whereHas('ruangan')->count();
            $sarprasUnit = Peminjaman::whereHas('unit')->count();
        } catch (\Throwable $e) {
            $sarprasLab = 0;
            $sarprasUnit = 0;
        }
        $chartSarpras = [
            'labels' => ['Ruangan/Lab', 'Unit/Proyektor'],
            'data' => [$sarprasLab, $sarprasUnit],
        ];

        // Chart 2: distribusi peminjam berdasarkan role (via relation mahasiswa -> user)
        try {
            $totalMahasiswa = Peminjaman::whereHas('mahasiswa', function($q){
                $q->where('role', 'mahasiswa');
            })->count();

            $totalDosen = Peminjaman::whereHas('mahasiswa', function($q){
                $q->where('role', 'dosen');
            })->count();

            $totalAdmin = Peminjaman::whereHas('mahasiswa', function($q){
                $q->where('role', 'admin');
            })->count();
        } catch (\Throwable $e) {
            $totalMahasiswa = $totalDosen = $totalAdmin = 0;
        }

        $sumKnown = $totalMahasiswa + $totalDosen + $totalAdmin;
        $unknown = max(0, $totalPeminjaman - $sumKnown);

        // Build labels/data but only include Unknown if > 0
        $userLabels = ['Mahasiswa', 'Dosen', 'Admin'];
        $userData = [$totalMahasiswa, $totalDosen, $totalAdmin];

        if ($unknown > 0) {
            $userLabels[] = 'Unknown';
            $userData[] = $unknown;
        }

        $chartUsers = [
            'labels' => $userLabels,
            'data' => $userData,
        ];

        // Chart 3: distribusi durasi (dalam jam) -> lebih tahan banting
        $durasiBuckets = [
            '0-2' => 0,
            '3-5' => 0,
            '6-10' => 0,
            '>10' => 0,
        ];

        // 1) Cek apakah ada kolom durasi secara eksplisit
        $possibleDurasiCols = ['durasi', 'lama', 'lama_jam', 'durasi_jam', 'lama_peminjaman'];
        $durasiCol = null;
        foreach ($possibleDurasiCols as $c) {
            if (Schema::hasColumn('peminjaman', $c)) {
                $durasiCol = $c;
                break;
            }
        }

        // Helper untuk memasukkan jam ke bucket
        $addToBucket = function(float $hours) use (&$durasiBuckets) {
            if ($hours <= 2) $durasiBuckets['0-2']++;
            elseif ($hours <= 5) $durasiBuckets['3-5']++;
            elseif ($hours <= 10) $durasiBuckets['6-10']++;
            else $durasiBuckets['>10']++;
        };

        if ($durasiCol) {
            // ambil nilai durasi langsung
            try {
                $rows = DB::table('peminjaman')->select($durasiCol)->whereNotNull($durasiCol)->get();
                foreach ($rows as $r) {
                    $val = (float) ($r->$durasiCol ?? 0);
                    $addToBucket($val);
                }
            } catch (\Throwable $e) {
                // ignore, tetap nol
            }
        } else {
            // 2) Coba hitung dari kombinasi tanggal + waktu
            $possibleDateCols = [
                ['tanggalPinjam', 'tanggalKembali'],
                ['tanggal_pinjam', 'tanggal_kembali'],
                ['start_datetime', 'end_datetime'],
                ['start_at', 'end_at']
            ];
            $possibleTimePairs = [
                ['tanggalPinjam', 'waktu_mulai', 'waktu_selesai'],
                ['tanggalPinjam', 'jam_mulai', 'jam_selesai'],
                ['tanggal_pinjam', 'jam_mulai', 'jam_selesai'],
                ['tanggalPinjam', 'waktuMulai', 'waktuSelesai'],
                ['tanggalPinjam', 'start_time', 'end_time'],
                ['waktu_mulai', 'waktu_selesai'],
                ['jam_mulai', 'jam_selesai'],
            ];

            $computedAny = false;

            // First: try rows with explicit start/end datetime columns
            foreach ($possibleDateCols as $pair) {
                if (Schema::hasColumn('peminjaman', $pair[0]) && Schema::hasColumn('peminjaman', $pair[1])) {
                    try {
                        $rows = DB::table('peminjaman')
                            ->select($pair[0] . ' as start', $pair[1] . ' as end')
                            ->whereNotNull($pair[0])
                            ->whereNotNull($pair[1])
                            ->get();

                        foreach ($rows as $r) {
                            try {
                                $start = Carbon::parse($r->start);
                                $end = Carbon::parse($r->end);
                                $hours = max(0, ($end->diffInMinutes($start) / 60.0));
                                $addToBucket($hours);
                                $computedAny = true;
                            } catch (\Throwable $e) {
                                // skip inaccurate row
                            }
                        }
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }

            // Second: try patterns where date + separate time columns exist
            foreach ($possibleTimePairs as $pattern) {
                if (count($pattern) === 3) {
                    $dateCol = $pattern[0];
                    $timeStartCol = $pattern[1];
                    $timeEndCol = $pattern[2];
                    if (Schema::hasColumn('peminjaman', $dateCol) && Schema::hasColumn('peminjaman', $timeStartCol) && Schema::hasColumn('peminjaman', $timeEndCol)) {
                        try {
                            $rows = DB::table('peminjaman')
                                ->select($dateCol . ' as date', $timeStartCol . ' as start_time', $timeEndCol . ' as end_time')
                                ->whereNotNull($dateCol)
                                ->whereNotNull($timeStartCol)
                                ->whereNotNull($timeEndCol)
                                ->get();

                            foreach ($rows as $r) {
                                try {
                                    $startStr = trim($r->date . ' ' . $r->start_time);
                                    $endStr = trim($r->date . ' ' . $r->end_time);
                                    $start = Carbon::parse($startStr);
                                    $end = Carbon::parse($endStr);

                                    if ($end->lessThan($start)) {
                                        $end = $end->addDay();
                                    }

                                    $hours = max(0, ($end->diffInMinutes($start) / 60.0));
                                    $addToBucket($hours);
                                    $computedAny = true;
                                } catch (\Throwable $e) {
                                    // skip row
                                }
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                } elseif (count($pattern) === 2) {
                    $startCol = $pattern[0];
                    $endCol = $pattern[1];
                    if (Schema::hasColumn('peminjaman', $startCol) && Schema::hasColumn('peminjaman', $endCol) && Schema::hasColumn('peminjaman', 'tanggalPinjam')) {
                        try {
                            $rows = DB::table('peminjaman')
                                ->select('tanggalPinjam as date', $startCol . ' as start_time', $endCol . ' as end_time')
                                ->whereNotNull('tanggalPinjam')
                                ->whereNotNull($startCol)
                                ->whereNotNull($endCol)
                                ->get();

                            foreach ($rows as $r) {
                                try {
                                    $startStr = trim($r->date . ' ' . $r->start_time);
                                    $endStr = trim($r->date . ' ' . $r->end_time);
                                    $start = Carbon::parse($startStr);
                                    $end = Carbon::parse($endStr);

                                    if ($end->lessThan($start)) $end = $end->addDay();

                                    $hours = max(0, ($end->diffInMinutes($start) / 60.0));
                                    $addToBucket($hours);
                                    $computedAny = true;
                                } catch (\Throwable $e) {}
                            }
                        } catch (\Throwable $e) {}
                    }
                }
            }

            // Final fallback: created_at / updated_at
            if (!$computedAny) {
                try {
                    $rows = DB::table('peminjaman')
                        ->select('created_at', 'updated_at')
                        ->whereNotNull('created_at')
                        ->whereNotNull('updated_at')
                        ->get();

                    foreach ($rows as $r) {
                        try {
                            $start = Carbon::parse($r->created_at);
                            $end = Carbon::parse($r->updated_at);
                            if ($end->lessThan($start)) continue;
                            $hours = max(0, ($end->diffInMinutes($start) / 60.0));
                            $addToBucket($hours);
                        } catch (\Throwable $e) {}
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        $chartDurasi = [
            'labels' => array_keys($durasiBuckets),
            'data' => array_values($durasiBuckets),
        ];

        // ------------- Kirim ke view -------------
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
            ->paginate(10);

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
            ->paginate(10);

        return view('dosen.dashboard', compact('stats', 'peminjamanTerkini'));
    }
}
