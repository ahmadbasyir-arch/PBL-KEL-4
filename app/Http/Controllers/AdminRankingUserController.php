<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Peminjaman;
use Carbon\Carbon;
use App\Models\Setting;

class AdminRankingUserController extends Controller
{
    public function index()
    {
        $data = $this->calculateRankings();
        $rankings = $data['rankings'];
        $bobot = $data['bobot'];

        return view('admin.ranking_user.index', compact('rankings', 'bobot'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $periode = $request->get('periode');

        // Handle standard periods if dates are null
        if (!$startDate && $periode) {
            switch ($periode) {
                case 'harian': $startDate = now()->startOfDay(); $endDate = now()->endOfDay(); break;
                case 'mingguan': $startDate = now()->startOfWeek(); $endDate = now()->endOfWeek(); break;
                case 'bulanan': $startDate = now()->startOfMonth(); $endDate = now()->endOfMonth(); break;
                case 'semester': $startDate = now()->subMonths(6); $endDate = now(); break;
                case 'tahunan': $startDate = now()->startOfYear(); $endDate = now()->endOfYear(); break;
            }
        }

        $data = $this->calculateRankings($startDate, $endDate);
        $rankings = $data['rankings'];
        $bobot = $data['bobot'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ranking_user.pdf', compact('rankings', 'bobot', 'startDate', 'endDate'));
        return $pdf->download('laporan-ranking-peminjam.pdf');
    }

    // Public so AdminLaporanController can use it for preview
    public function calculateRankings($startDate = null, $endDate = null, $role = null)
    {
        // 1. Ambil User yang pernah meminjam (Status Selesai)
        $query = User::whereIn('role', ['mahasiswa', 'dosen']);

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        $query->with(['peminjaman' => function($q) use ($startDate, $endDate) {
                // Hanya ambil history yang selesai
                $q->where('status', 'selesai')->with('pengembalian');
                
                // Filter Date
                if ($startDate && $endDate) {
                    $q->whereBetween('tanggalPinjam', [$startDate, $endDate]);
                }
            }]);

        $users = $query->get()->filter(function($user) {
            return $user->peminjaman->count() > 0;
        });

        // 2. Definisi Bobot
        $bobot = [
            'C1' => 0.35, // Kepentingan
            'C2' => 0.25, // Perencanaan
            'C3' => 0.15, // Durasi
            'C4' => 0.25, // Kondisi
        ];

        // 3. Matriks Keputusan
        $matrix = [];

        foreach ($users as $user) {
            $totalC1 = 0; $totalC2 = 0; $totalC3 = 0; $totalC4 = 0;
            $count = $user->peminjaman->count();

            foreach ($user->peminjaman as $p) {
                // C1: Urgensi
                $totalC1 += $this->getUrgensiScore($p->keperluan);

                // C2: Perencanaan (Hari)
                $booking = Carbon::parse($p->tanggalPinjam);
                $created = Carbon::parse($p->created_at);
                $diff = $created->diffInDays($booking);
                $totalC2 += min($diff, 14);

                // C3: Durasi (Jam)
                $start = Carbon::parse($p->jamMulai);
                $end   = Carbon::parse($p->jamSelesai);
                $totalC3 += max(1, $end->diffInHours($start));

                // C4: Kondisi
                $kondisi = $p->pengembalian->kondisi ?? 'Baik';
                $scoreKondisi = (stripos($kondisi, 'baik') !== false) ? 5 : 1;
                if (!empty($p->pengembalian->catatan) && stripos($kondisi, 'baik') === false) {
                    $scoreKondisi = 0.5;
                }
                $totalC4 += $scoreKondisi;
            }

            $matrix[] = [
                'user' => $user,
                'count' => $count,
                'C1' => $totalC1 / $count,
                'C2' => $totalC2 / $count,
                'C3' => $totalC3 / $count,
                'C4' => $totalC4 / $count,
            ];
        }

        // 4. Normalisasi & SAW
        $rankings = [];
        if (!empty($matrix)) {
            $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
            $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
            $minC3 = min(array_column($matrix, 'C3')) ?: 1;
            $maxC4 = max(array_column($matrix, 'C4')) ?: 1;

            foreach ($matrix as $row) {
                $n1 = $row['C1'] / $maxC1;
                $n2 = $row['C2'] / $maxC2;
                $n3 = $minC3 / $row['C3'];
                $n4 = $row['C4'] / $maxC4;

                $score = ($n1 * $bobot['C1']) + ($n2 * $bobot['C2']) + ($n3 * $bobot['C3']) + ($n4 * $bobot['C4']);

                $rankings[] = (object) [
                    'user' => $row['user'],
                    'total_pinjam' => $row['count'],
                    'saw_score' => number_format($score * 100, 2),
                    'raw_metrics' => [
                        'C1' => number_format($row['C1'], 1),
                        'C2' => number_format($row['C2'], 1),
                        'C3' => number_format($row['C3'], 1),
                        'C4' => number_format($row['C4'], 1)
                    ]
                ];
            }

            usort($rankings, function($a, $b) {
                return $b->saw_score <=> $a->saw_score;
            });

            foreach ($rankings as $index => $rank) {
                $rank->rank = $index + 1;
            }
        }

        return ['rankings' => $rankings, 'bobot' => $bobot];
    }

    private function getUrgensiScore($keperluan) {
        $text = strtolower($keperluan);
        if (str_contains($text, 'sidang') || str_contains($text, 'skripsi') || str_contains($text, 'ta') || str_contains($text, 'ujian')) return 5;
        if (str_contains($text, 'lomba') || str_contains($text, 'kompetisi') || str_contains($text, 'delegasi')) return 4;
        if (str_contains($text, 'kuliah') || str_contains($text, 'kelas') || str_contains($text, 'praktikum')) return 3;
        if (str_contains($text, 'rapat') || str_contains($text, 'hima') || str_contains($text, 'bem')) return 2;
        return 1;
    }
}
