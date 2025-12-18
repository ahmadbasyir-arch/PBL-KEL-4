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

    // Fungsi Utama Perhitungan Ranking User
    public function calculateRankings($startDate = null, $endDate = null, $role = null)
    {
        // ------------------------------------------------------------------------------
        // TAHAP 1: Ambil Data Peminjam
        // ------------------------------------------------------------------------------
        // Mengambil user yang pernah meminjam dan statusnya 'selesai'
        $query = User::whereIn('role', ['mahasiswa', 'dosen']);

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        $query->with(['peminjaman' => function($q) use ($startDate, $endDate) {
                // Hanya ambil history peminjaman yang sudah 'selesai'
                $q->where('status', 'selesai')->with('pengembalian');
                
                // Filter tanggal (opsional jika user memilih periode)
                if ($startDate && $endDate) {
                    $q->whereBetween('tanggalPinjam', [$startDate, $endDate]);
                }
            }]);

        // Filter user yang punya minimal 1 history peminjaman
        $users = $query->get()->filter(function($user) {
            return $user->peminjaman->count() > 0;
        });

        // ------------------------------------------------------------------------------
        // TAHAP 2: Definisi Bobot Kriteria
        // ------------------------------------------------------------------------------
        $bobot = [
            'C1' => 0.35, // Kepentingan (Rata-rata urgensi peminjaman)
            'C2' => 0.25, // Perencanaan (Seberapa rajin booking jauh hari)
            'C3' => 0.15, // Durasi (Durasi peminjaman)
            'C4' => 0.25, // Kondisi (Seberapa baik barang saat dikembalikan)
        ];

        // ------------------------------------------------------------------------------
        // TAHAP 3: Matriks Keputusan (X)
        // ------------------------------------------------------------------------------
        $matrix = [];

        foreach ($users as $user) {
            $totalC1 = 0; $totalC2 = 0; $totalC3 = 0; $totalC4 = 0;
            $count = $user->peminjaman->count();

            // Loop semua history peminjaman user ini untuk cari rata-ratanya
            foreach ($user->peminjaman as $p) {
                // C1: Urgensi (Skor 1-5 berdasarkan keperluan)
                $totalC1 += $this->getUrgensiScore($p->keperluan);

                // C2: Perencanaan (Selisih Hari Booking vs Pakai)
                // Maksimal 14 hari
                $booking = Carbon::parse($p->tanggalPinjam);
                $created = Carbon::parse($p->created_at);
                $diff = $created->diffInDays($booking);
                $totalC2 += min($diff, 14);

                // C3: Durasi (Jam)
                // Rata-rata durasi pemakaian
                $start = Carbon::parse($p->jamMulai);
                $end   = Carbon::parse($p->jamSelesai);
                $totalC3 += max(1, $end->diffInHours($start));

                // C4: Kondisi Pengembalian
                // Jika kondisi 'baik' = 5 poin, jika rusak/hilang = 1 poin
                $kondisi = $p->pengembalian->kondisi ?? 'Baik';
                $scoreKondisi = (stripos($kondisi, 'baik') !== false) ? 5 : 1;
                
                // Penalti jika kondisi 'baik' tapi ada 'catatan' (misal telat, kotor dikit)
                if (!empty($p->pengembalian->catatan) && stripos($kondisi, 'baik') === false) {
                    $scoreKondisi = 0.5;
                }
                $totalC4 += $scoreKondisi;
            }

            // Simpan rata-rata nilai user ke matriks
            $matrix[] = [
                'user' => $user,
                'count' => $count,
                'C1' => $totalC1 / $count, // Rata-rata Urgensi
                'C2' => $totalC2 / $count, // Rata-rata Planning
                'C3' => $totalC3 / $count, // Rata-rata Durasi
                'C4' => $totalC4 / $count, // Rata-rata Kondisi
            ];
        }

        // ------------------------------------------------------------------------------
        // TAHAP 4: Normalisasi Matriks & Perangkingan
        // ------------------------------------------------------------------------------
        $rankings = [];
        if (!empty($matrix)) {
            // Cari nilai Max/Min untuk normalisasi
            $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
            $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
            $minC3 = min(array_column($matrix, 'C3')) ?: 1; // Durasi mungkin Cost atau Benefit tergantung persepsi, disini pakai Cost (Makin singkat makin efisien?) 
            // *Koreksi dari code sebelumnya: Disini logikanya minC3 dipakai pembilang, berarti C3 dianggap COST (makin kecil makin bagus / efisien).*
            $maxC4 = max(array_column($matrix, 'C4')) ?: 1;

            foreach ($matrix as $row) {
                // Rumus Normalisasi
                $n1 = $row['C1'] / $maxC1;       // Benefit
                $n2 = $row['C2'] / $maxC2;       // Benefit
                $n3 = $minC3 / $row['C3'];       // Cost (Durasi)
                $n4 = $row['C4'] / $maxC4;       // Benefit (Kondisi)

                // Hitung Skor Akhir (SAW)
                $score = ($n1 * $bobot['C1']) + ($n2 * $bobot['C2']) + ($n3 * $bobot['C3']) + ($n4 * $bobot['C4']);

                $rankings[] = (object) [
                    'user' => $row['user'],
                    'total_pinjam' => $row['count'],
                    'saw_score' => number_format($score * 100, 2), // Skala 0-100
                    'raw_metrics' => [
                        'C1' => number_format($row['C1'], 1), // Rata2 nilai asli
                        'C2' => number_format($row['C2'], 1),
                        'C3' => number_format($row['C3'], 1),
                        'C4' => number_format($row['C4'], 1)
                    ]
                ];
            }

            // Urutkan ranking dari skor tertinggi
            usort($rankings, function($a, $b) {
                return $b->saw_score <=> $a->saw_score;
            });

            // Beri nomor urut
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
