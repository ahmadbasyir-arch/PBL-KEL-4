<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\Unit;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;

class AdminPerawatanController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Aset (Ruangan & Unit)
        $ruangan = Ruangan::all()->map(function($item) {
            $item->type = 'Ruangan';
            $item->kode = $item->namaRuangan; // Ruangan gak ada kode, pake nama aja
            return $item;
        });

        $unit = Unit::all()->map(function($item) {
            $item->type = 'Unit';
            $item->kode = $item->namaUnit . ' (' . $item->kodeUnit . ')';
            return $item;
        });

        $assets = $ruangan->concat($unit);

        // 2. Pembobotan (Bisa dibuat dinamis nanti, hardcode dulu)
        $bobot = [
            'C1' => 0.35, // Frekuensi Pemakaian (Benefit)
            'C2' => 0.25, // Total Durasi / Workload (Benefit)
            'C3' => 0.40, // Riwayat Kerusakan/Masalah (Benefit - makin banyak masalah, makin prioritas)
        ];

        // 3. Kalkulasi Matriks Keputusan
        $matrix = [];
        
        foreach ($assets as $asset) {
            
            // Query Dasar Peminjaman Selesai untuk aset ini
            $idCol = $asset->type === 'Ruangan' ? 'idRuangan' : 'idUnit';
            
            $history = Peminjaman::where($idCol, $asset->id)
                ->where('status', 'selesai')
                ->get();

            // C1: Frekuensi
            $c1 = $history->count();

            // C2: Workload (Jam)
            $c2 = 0;
            foreach ($history as $h) {
                $start = Carbon::parse($h->jamMulai);
                $end   = Carbon::parse($h->jamSelesai);
                $c2 += $end->diffInHours($start);
            }

            // C3: Issues (Kondisi != Baik atau ada Catatan)
            // Ambil ID Peminjaman dari history aset ini
            $peminjamanIds = $history->pluck('id');
            $c3 = Pengembalian::whereIn('idPeminjaman', $peminjamanIds)
                ->where(function($q) {
                    $q->where('kondisi', '!=', 'Baik')
                      ->orWhereNotNull('catatan');
                })
                ->count();

            $matrix[] = [
                'asset' => $asset,
                'C1' => $c1,
                'C2' => $c2,
                'C3' => $c3
            ];
        }

        // 4. Normalisasi
        // Semua Kriteria adalah BENEFIT (Makin tinggi nilainya -> Makin butuh perawatan)
        
        $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
        $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
        $maxC3 = max(array_column($matrix, 'C3')) ?: 1;

        $rankings = [];

        foreach ($matrix as $row) {
            // Normalisasi (Value / Max)
            $n1 = $row['C1'] / $maxC1;
            $n2 = $row['C2'] / $maxC2;
            $n3 = $row['C3'] / $maxC3;

            // Hitung Skor Akhir (SAW)
            $score = ($n1 * $bobot['C1']) + ($n2 * $bobot['C2']) + ($n3 * $bobot['C3']);

            $rankings[] = (object) [
                'asset' => $row['asset'],
                'saw_score' => number_format($score, 4),
                'raw_metrics' => [
                    'C1' => $row['C1'],
                    'C2' => $row['C2'],
                    'C3' => $row['C3']
                ],
                'norm_metrics' => [
                    'C1' => $n1,
                    'C2' => $n2,
                    'C3' => $n3
                ]
            ];
        }

        // 5. Ranking (Sort Descending)
        usort($rankings, function($a, $b) {
            return $b->saw_score <=> $a->saw_score;
        });

        // Tambahkan Rank number
        foreach ($rankings as $index => $rank) {
            $rank->rank = $index + 1;
        }

        return view('admin.perawatan.index', compact('rankings', 'bobot'));
    }
}
