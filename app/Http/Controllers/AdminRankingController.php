<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRankingController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->calculateRankings($request->role);
        return view('admin.ranking.index', $data);
    }

    public function updateWeights(Request $request)
    {
        $request->validate([
            'saw_c1' => 'required|numeric|min:0|max:1',
            'saw_c2' => 'required|numeric|min:0|max:1',
            'saw_c3' => 'required|numeric|min:0|max:1',
            'saw_c4' => 'required|numeric|min:0|max:1',
        ]);

        $keys = ['saw_c1', 'saw_c2', 'saw_c3', 'saw_c4'];
        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return redirect()->back()->with('success', 'Bobot kriteria berhasil diperbarui!');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->calculateRankings($request->role);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ranking.pdf', $data);
        return $pdf->download('laporan-ranking-saw-' . date('Y-m-d') . '.pdf');
    }

    private function calculateRankings($role = null)
    {
        // 1. Ambil Data User & Peminjaman Selesai
        $query = User::whereIn('role', ['mahasiswa', 'dosen']);

        if ($role && in_array($role, ['mahasiswa', 'dosen'])) {
            $query->where('role', $role);
        }

        $users = $query->with(['peminjaman' => function($q) {
                $q->where('status', 'selesai');
            }])
            ->get();

        // 2. Kriteria & Bobot
        // 2. Kriteria & Bobot
        $settings = Setting::whereIn('key', ['saw_c1', 'saw_c2', 'saw_c3', 'saw_c4'])->pluck('value', 'key');
        $bobot = [
            'C1' => (float) ($settings['saw_c1'] ?? 0.30),
            'C2' => (float) ($settings['saw_c2'] ?? 0.20),
            'C3' => (float) ($settings['saw_c3'] ?? 0.20),
            'C4' => (float) ($settings['saw_c4'] ?? 0.30)
        ];

        // 3. Matriks Keputusan (X)
        $matrix = [];
        foreach ($users as $user) {
            
            // Default Values untuk User tanpa Peminjaman
            $c1 = 0; // Kepentingan (Benefit -> 0 nilai terburuk)
            $c2 = 0; // Perencanaan (Benefit -> 0 nilai terburuk)
            $c3 = 999999; // Durasi (Cost -> nilai tertinggi terburuk)
            $c4 = 0; // Kondisi (Benefit -> 0 nilai terburuk)

            if ($user->peminjaman->isNotEmpty()) {
                // C1: Kepentingan
                $totalKepentingan = 0;
                foreach ($user->peminjaman as $p) {
                    $totalKepentingan += $this->getKepentinganScore($p->keperluan);
                }
                $c1 = $totalKepentingan / $user->peminjaman->count();

                // C2: Meminjam
                $totalAdvance = 0;
                foreach ($user->peminjaman as $p) {
                    $created = \Carbon\Carbon::parse($p->created_at);
                    $booking = \Carbon\Carbon::parse($p->tanggalPinjam);
                    $diff = max(0, $created->diffInDays($booking));
                    $totalAdvance += $diff;
                }
                $c2 = $totalAdvance / $user->peminjaman->count();

                // C3: Durasi
                $totalDurasi = 0;
                foreach ($user->peminjaman as $p) {
                    $start = \Carbon\Carbon::parse($p->jamMulai);
                    $end   = \Carbon\Carbon::parse($p->jamSelesai);
                    $totalDurasi += max(0, $end->diffInHours($start));
                }
                $c3 = $totalDurasi / $user->peminjaman->count();

                // C4: Kondisi
                $c4 = 100; 
            }

            $matrix[$user->id] = [
                'user' => $user,
                'C1' => $c1,
                'C2' => $c2,
                'C3' => $c3,
                'C4' => $c4
            ];
        }

        // 4. Normalisasi Matriks (R)
        $normalized = [];
        
        // Cari Max/Min tiap kolom
        $maxC1 = 0; $maxC2 = 0; $minC3 = 999999; $maxC4 = 0;
        
        if (!empty($matrix)) {
            $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
            $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
            $minC3 = min(array_column($matrix, 'C3')) ?: 1;
            $maxC4 = max(array_column($matrix, 'C4')) ?: 1;
        } else {
            $maxC1 = $maxC2 = $minC3 = $maxC4 = 1;
        }

        foreach ($matrix as $uid => $row) {
            $normalized[$uid] = [
                'user' => $row['user'],
                'C1' => $row['C1'] / $maxC1,             // Benefit
                'C2' => $row['C2'] / $maxC2,             // Benefit
                'C3' => $minC3 / ($row['C3'] ?: 1),      // Cost (Min / Value)
                'C4' => $row['C4'] / $maxC4              // Benefit
            ];
        }

        // 5. Ranking (V)
        $rankings = [];
        foreach ($normalized as $uid => $row) {
            $score = 
                ($row['C1'] * $bobot['C1']) +
                ($row['C2'] * $bobot['C2']) +
                ($row['C3'] * $bobot['C3']) +
                ($row['C4'] * $bobot['C4']);
            
            $rankings[] = (object) [
                'user' => $row['user'],
                'total_minjam' => $matrix[$uid]['user']->peminjaman->count(),
                'saw_score' => number_format($score * 100, 2),
                'detail' => $row 
            ];
        }

        // Urutan terbesar ke terkecil
        usort($rankings, function($a, $b) {
            return $b->saw_score <=> $a->saw_score;
        });

        // Rank
        foreach ($rankings as $idx => $r) {
            $r->rank = $idx + 1;
        }

        return compact('rankings', 'bobot');
    }

    private function getKepentinganScore($keperluan) {
        $keperluan = strtolower($keperluan);
        if (str_contains($keperluan, 'sidang') || str_contains($keperluan, 'kompetensi')) return 5;
        if (str_contains($keperluan, 'seminar') || str_contains($keperluan, 'lomba')) return 4;
        if (str_contains($keperluan, 'kuliah') || str_contains($keperluan, 'pelatihan')) return 3;
        if (str_contains($keperluan, 'rapat') || str_contains($keperluan, 'kegiatan')) return 2;
        return 1;
    }
}
