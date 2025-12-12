<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Peminjaman; // Assuming Peminjaman model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRankingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter role dari request, default 'all'
        $roleFilter = $request->input('role', 'all');

        $query = User::query();

        if ($roleFilter === 'mahasiswa') {
            $query->where('role', 'mahasiswa');
        } elseif ($roleFilter === 'dosen') {
            $query->where('role', 'dosen');
        } else {
            // Default: mahasiswa & dosen
            $query->whereIn('role', ['mahasiswa', 'dosen']);
        }

        $rankings = $query->withCount(['peminjaman as total_minjam' => function ($query) {
                $query->whereIn('status', ['selesai', 'disetujui']);
            }])
            ->orderByDesc('total_minjam')
            ->get();

        // Berikan ranking
        $rank = 1;
        $previousScore = null;
        $realRank = 1;

        foreach ($rankings as $index => $user) {
            if ($previousScore !== $user->total_minjam) {
                $rank = $realRank;
            }
            $user->rank = $rank;
            $previousScore = $user->total_minjam;
            $realRank++;
        }

        return view('admin.ranking.index', compact('rankings'));
    }

    public function exportPdf(Request $request)
    {
        $periode = $request->input('periode', 'bulanan');
        $roleFilter = $request->input('role', 'all');

        $query = User::query();

        if ($roleFilter === 'mahasiswa') {
            $query->where('role', 'mahasiswa');
        } elseif ($roleFilter === 'dosen') {
            $query->where('role', 'dosen');
        } else {
            $query->whereIn('role', ['mahasiswa', 'dosen']);
        }

        $dateFilter = function ($q) use ($periode) {
            $q->whereIn('status', ['selesai', 'disetujui']);
            $now = \Carbon\Carbon::now();
            
            if ($periode == 'harian') {
                $q->whereDate('tanggalPinjam', $now->format('Y-m-d'));
            } elseif ($periode == 'mingguan') {
                $q->whereBetween('tanggalPinjam', [$now->startOfWeek()->format('Y-m-d'), $now->endOfWeek()->format('Y-m-d')]);
            } elseif ($periode == 'bulanan') {
                $q->whereMonth('tanggalPinjam', $now->month)->whereYear('tanggalPinjam', $now->year);
            } elseif ($periode == 'semester') {
                if ($now->month >= 7) {
                    $q->whereMonth('tanggalPinjam', '>=', 7);
                } else {
                    $q->whereMonth('tanggalPinjam', '<=', 6);
                }
                $q->whereYear('tanggalPinjam', $now->year);
            } elseif ($periode == 'tahunan') {
                $q->whereYear('tanggalPinjam', $now->year);
            }
        };

        $rankings = $query->withCount(['peminjaman as total_minjam' => $dateFilter])
            ->get()
            ->sortByDesc('total_minjam')
            ->values();

        $rank = 1; $realRank = 1; $activeRank = 1; $prevScore = null;
        foreach ($rankings as $user) {
            if ($prevScore !== $user->total_minjam) { $activeRank = $rank; }
            $user->rank = $activeRank;
            $prevScore = $user->total_minjam;
            $rank++;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ranking.pdf', [
            'rankings' => $rankings,
            'periode' => ucfirst($periode)
        ]);

        return $pdf->download('Laporan_Ranking_' . $periode . '_' . date('Ymd') . '.pdf');
    }
}
