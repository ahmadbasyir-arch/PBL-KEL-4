<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminRankingController extends Controller
{
    public function index(Request $request)
    {
        // Default bobot jika belum di-set
        $settings = Setting::whereIn('key', ['saw_c1', 'saw_c2', 'saw_c3', 'saw_c4'])->pluck('value', 'key');
        $bobot = [
            'C1' => (float) ($settings['saw_c1'] ?? 0.35), // Urgensi (Benefit)
            'C2' => (float) ($settings['saw_c2'] ?? 0.25), // Perencanaan (Benefit)
            'C3' => (float) ($settings['saw_c3'] ?? 0.25), // Reputasi User (Benefit)
            'C4' => (float) ($settings['saw_c4'] ?? 0.15)  // Durasi (Cost)
        ];

        $data = $this->calculatePriority($bobot, $request->role);
        return view('admin.ranking.index', array_merge($data, ['bobot' => $bobot]));
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

        return redirect()->back()->with('success', 'Bobot kriteria prioritas berhasil diperbarui!');
    }

    public function exportPdf(Request $request)
    {
        // Re-fetch bobot for export
        $settings = Setting::whereIn('key', ['saw_c1', 'saw_c2', 'saw_c3', 'saw_c4'])->pluck('value', 'key');
        $bobot = [
            'C1' => (float) ($settings['saw_c1'] ?? 0.35),
            'C2' => (float) ($settings['saw_c2'] ?? 0.25),
            'C3' => (float) ($settings['saw_c3'] ?? 0.25),
            'C4' => (float) ($settings['saw_c4'] ?? 0.15)
        ];

        $data = $this->calculatePriority($bobot, $request->role);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.ranking.pdf', array_merge($data, ['bobot' => $bobot]));
        return $pdf->download('laporan-prioritas-approval-' . date('Y-m-d') . '.pdf');
    }

    private function calculatePriority($bobot, $role = null)
    {
        // 1. Ambil Data Peminjaman status 'pending'
        $query = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->where('status', 'pending');

        // Optional filter by role user peminjaman
        if ($role && in_array($role, ['mahasiswa', 'dosen'])) {
            $query->whereHas('user', function($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $requests = $query->get();

        // 2. Matriks Keputusan (X)
        $matrix = [];
        
        foreach ($requests as $req) {
            // C1: Urgensi (Benefit) - 1-5
            $c1 = $this->getUrgensiScore($req->keperluan);

            // C2: Perencanaan (Benefit) - Selisih hari pengajuan vs tanggal pakai
            $created = Carbon::parse($req->created_at);
            $booking = Carbon::parse($req->tanggalPinjam);
            $c2 = max(0, $created->diffInDays($booking)); 
            // Cap di 7 hari agar normalisasi tidak jomplang jika ada yg booking 1 bulan sebelumnya
            if($c2 > 14) $c2 = 14; 

            // C3: Reputasi User (Benefit) - Jumlah historis peminjaman "selesai" tanpa masalah
            // Kita bisa ambil count status 'selesai' user ini
            $c3 = Peminjaman::where('idMahasiswa', $req->idMahasiswa)
                    ->where('status', 'selesai')
                    ->count();
            // Cap reputasi di 20 agar pemula tetap punya peluang
            if($c3 > 20) $c3 = 20;

            // C4: Durasi (Cost) - Lama pinjam dalam jam
            $start = Carbon::parse($req->jamMulai);
            $end   = Carbon::parse($req->jamSelesai);
            $c4 = max(1, $end->diffInHours($start)); // Minimal 1 jam

            $matrix[$req->id] = [
                'request' => $req,
                'C1' => $c1,
                'C2' => $c2,
                'C3' => $c3,
                'C4' => $c4
            ];
        }

        // 3. Normalisasi Matriks (R)
        $normalized = [];
        
        if (!empty($matrix)) {
            $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
            $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
            $maxC3 = max(array_column($matrix, 'C3')) ?: 1; // Benefit
            $minC4 = min(array_column($matrix, 'C4')) ?: 1; // Cost
        } else {
            $maxC1 = $maxC2 = $maxC3 = $minC4 = 1;
        }

        foreach ($matrix as $id => $row) {
            $normalized[$id] = [
                'request' => $row['request'],
                'raw' => $row, // simpan nilai asli untuk display info
                'C1' => $row['C1'] / $maxC1,             // Benefit
                'C2' => $row['C2'] / $maxC2,             // Benefit
                'C3' => $row['C3'] / $maxC3,             // Benefit
                'C4' => $minC4 / ($row['C4'] ?: 1)       // Cost (Min / Value)
            ];
        }

        // 4. Ranking (V)
        $rankings = [];
        foreach ($normalized as $id => $row) {
            $score = 
                ($row['C1'] * $bobot['C1']) +
                ($row['C2'] * $bobot['C2']) +
                ($row['C3'] * $bobot['C3']) +
                ($row['C4'] * $bobot['C4']);
            
            $rankings[] = (object) [
                'id' => $id,
                'data' => $row['request'],
                'raw_metrics' => $row['raw'],
                'saw_score' => number_format($score * 100, 2),
                'detail' => $row // nilai ternormalisasi
            ];
        }

        // Urutan terbesar ke terkecil
        usort($rankings, function($a, $b) {
            return $b->saw_score <=> $a->saw_score;
        });

        // Assign Rank Number
        foreach ($rankings as $idx => $r) {
            $r->rank = $idx + 1;
        }

        return compact('rankings');
    }

    private function getUrgensiScore($keperluan) {
        // Skala 1-5 (5 Paling Urgent)
        $text = strtolower($keperluan);
        
        // Priority 1: Akademik Fatal (Sidang, Ujian)
        if (str_contains($text, 'sidang') || str_contains($text, 'skripsi') || str_contains($text, 'ta') || str_contains($text, 'ujian') || str_contains($text, 'uas') || str_contains($text, 'uts')) 
            return 5;
            
        // Priority 2: Kompetisi / Nama Kampus
        if (str_contains($text, 'lomba') || str_contains($text, 'kompetisi') || str_contains($text, 'delegasi') || str_contains($text, 'seminar')) 
            return 4;

        // Priority 3: Perkuliahan Reguler / Praktikum
        if (str_contains($text, 'kuliah') || str_contains($text, 'kelas') || str_contains($text, 'praktikum') || str_contains($text, 'praktek') || str_contains($text, 'pengganti')) 
            return 3;
            
        // Priority 4: Organisasi / Rapat Resmi
        if (str_contains($text, 'rapat') || str_contains($text, 'hima') || str_contains($text, 'bem') || str_contains($text, 'ukm') || str_contains($text, 'kegiatan')) 
            return 2;

        // Priority 5: Lain-lain
        return 1;
    }
}
