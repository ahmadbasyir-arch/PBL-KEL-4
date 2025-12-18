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
    // Menampilkan halaman utama ranking
    public function index(Request $request)
    {
        // Mengambil bobot dari database (Tabel Settings)
        // Jika tidak ada di database, pakai nilai default (0.35, 0.25, dst)
        $settings = Setting::whereIn('key', ['saw_c1', 'saw_c2', 'saw_c3', 'saw_c4'])->pluck('value', 'key');
        $bobot = [
            'C1' => (float) ($settings['saw_c1'] ?? 0.35), // C1: Urgensi (Benefit = Makin tinggi makin bagus)
            'C2' => (float) ($settings['saw_c2'] ?? 0.25), // C2: Perencanaan (Benefit)
            'C3' => (float) ($settings['saw_c3'] ?? 0.25), // C3: Reputasi User (Benefit)
            'C4' => (float) ($settings['saw_c4'] ?? 0.15)  // C4: Durasi (Cost = Makin tinggi makin jelek/kecil nilainya)
        ];

        // Hitung prioritas dengan fungsi private di bawah
        $data = $this->calculatePriority($bobot, $request->role);
        
        // Kirim data dan bobot ke view (tampilan)
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

    // Fungsi Inti Algoritma SAW (Simple Additive Weighting)
    private function calculatePriority($bobot, $role = null)
    {
        // ---------------------------------------------------------
        // TAHAP 1: Pengumpulan Data Alternatif
        // ---------------------------------------------------------
        // Ambil semua peminjaman yang statusnya masih 'pending'
        $query = Peminjaman::with(['user', 'ruangan', 'unit'])
            ->where('status', 'pending');

        // Jika ada filter role (misal hanya mau lihat mahasiswa/dosen)
        if ($role && in_array($role, ['mahasiswa', 'dosen'])) {
            $query->whereHas('user', function($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $requests = $query->get();

        // ---------------------------------------------------------
        // TAHAP 2: Membentuk Matriks Keputusan (X)
        // ---------------------------------------------------------
        $matrix = [];
        
        foreach ($requests as $req) {
            // [C1] Urgensi (Benefit) -> Nilai 1-5 berdasarkan kata kunci keperluan
            $c1 = $this->getUrgensiScore($req->keperluan);

            // [C2] Perencanaan (Benefit) -> Selisih hari antara "Request Dibuat" vs "Tanggal Pinjam"
            // Semakin jauh hari bookingnya, semakin bagus nilainya.
            $created = Carbon::parse($req->created_at);
            $booking = Carbon::parse($req->tanggalPinjam);
            $c2 = max(0, $created->diffInDays($booking)); 
            
            // Batasi maksimal 14 hari agar normalisasi tidak terlalu timpang
            if($c2 > 14) $c2 = 14; 

            // [C3] Reputasi User (Benefit) -> Jumlah histori peminjaman "Selesai" user ini
            // Semakin sering pinjam & tertib, reputasi makin bagus.
            $c3 = Peminjaman::where('idMahasiswa', $req->idMahasiswa)
                    ->where('status', 'selesai')
                    ->count();
            // Batasi maksimal 20 poin
            if($c3 > 20) $c3 = 20;

            // [C4] Durasi (Cost) -> Lama peminjaman dalam jam
            // Karena ini COST, nanti saat normalisasi rumusnya beda (Min / Nilai).
            // Artinya semakin lama pinjam, poin akhirnya justru semakin kecil.
            $start = Carbon::parse($req->jamMulai);
            $end   = Carbon::parse($req->jamSelesai);
            $c4 = max(1, $end->diffInHours($start)); // Minimal 1 jam

            // Masukkan ke array matriks sementara
            $matrix[$req->id] = [
                'request' => $req,
                'C1' => $c1,
                'C2' => $c2,
                'C3' => $c3,
                'C4' => $c4
            ];
        }

        // ---------------------------------------------------------
        // TAHAP 3: Normalisasi Matriks (R)
        // ---------------------------------------------------------
        $normalized = [];
        
        // Cari nilai Max/Min tiap kolom untuk rumus normalisasi
        if (!empty($matrix)) {
            $maxC1 = max(array_column($matrix, 'C1')) ?: 1;
            $maxC2 = max(array_column($matrix, 'C2')) ?: 1;
            $maxC3 = max(array_column($matrix, 'C3')) ?: 1; // Untuk kriteria Benefit
            $minC4 = min(array_column($matrix, 'C4')) ?: 1; // Untuk kriteria Cost
        } else {
            $maxC1 = $maxC2 = $maxC3 = $minC4 = 1;
        }

        foreach ($matrix as $id => $row) {
            $normalized[$id] = [
                'request' => $row['request'],
                'raw' => $row, // simpan nilai asli untuk ditampilkan di info
                
                // Rumus Benefit: Nilai / Max
                'C1' => $row['C1'] / $maxC1,             
                'C2' => $row['C2'] / $maxC2,             
                'C3' => $row['C3'] / $maxC3,             
                
                // Rumus Cost: Min / Nilai (Kebalikannya)
                'C4' => $minC4 / ($row['C4'] ?: 1)
            ];
        }

        // ---------------------------------------------------------
        // TAHAP 4: Perangkingan / Preferensi (V)
        // ---------------------------------------------------------
        $rankings = [];
        foreach ($normalized as $id => $row) {
            // Hitung skor akhir = (NilaiNormalisasi * Bobot) + ...
            $score = 
                ($row['C1'] * $bobot['C1']) +
                ($row['C2'] * $bobot['C2']) +
                ($row['C3'] * $bobot['C3']) +
                ($row['C4'] * $bobot['C4']);
            
            $rankings[] = (object) [
                'id' => $id,
                'data' => $row['request'],
                'raw_metrics' => $row['raw'],
                'saw_score' => number_format($score * 100, 2), // Jadikan skala 0-100
                'detail' => $row // nilai ternormalisasi
            ];
        }

        // Urutkan dari skor terbesar ke terkecil
        usort($rankings, function($a, $b) {
            return $b->saw_score <=> $a->saw_score;
        });

        // Beri nomor ranking
        foreach ($rankings as $idx => $r) {
            $r->rank = $idx + 1;
        }

        return compact('rankings');
    }

    // Fungsi Helper: Menentukan Skor 1-5 Berdasarkan Kata Kunci Keperluan
    private function getUrgensiScore($keperluan) {
        $text = strtolower($keperluan);
        
        // Priority 1: Akademik Fatal (Score 5)
        if (str_contains($text, 'sidang') || str_contains($text, 'skripsi') || str_contains($text, 'ta') || str_contains($text, 'ujian') || str_contains($text, 'uas') || str_contains($text, 'uts')) 
            return 5;
            
        // Priority 2: Kompetisi / Bawa Nama Kampus (Score 4)
        if (str_contains($text, 'lomba') || str_contains($text, 'kompetisi') || str_contains($text, 'delegasi') || str_contains($text, 'seminar')) 
            return 4;

        // Priority 3: Perkuliahan Reguler (Score 3)
        if (str_contains($text, 'kuliah') || str_contains($text, 'kelas') || str_contains($text, 'praktikum') || str_contains($text, 'praktek') || str_contains($text, 'pengganti')) 
            return 3;
            
        // Priority 4: Kegiatan Organisasi (Score 2)
        if (str_contains($text, 'rapat') || str_contains($text, 'hima') || str_contains($text, 'bem') || str_contains($text, 'ukm') || str_contains($text, 'kegiatan')) 
            return 2;

        // Priority 5: Lain-lain (Score 1)
        return 1;
    }
}
