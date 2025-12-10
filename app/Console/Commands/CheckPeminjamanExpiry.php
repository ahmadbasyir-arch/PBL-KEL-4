<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPeminjamanExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peminjaman:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for borrowings expring in 30 minutes and notify users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $targetTime = $now->copy()->addMinutes(30);

        // Cari peminjaman hari ini yang disetujui / digunakan
        // Karena jamMulai & jamSelesai tipe TIME, kita perlu filter tanggal dulu
        $today = $now->format('Y-m-d');
        
        $peminjamans = \App\Models\Peminjaman::where('tanggalPinjam', $today)
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan']) // Asumsikan status aktif
            ->with('mahasiswa') // Eager load user
            ->get();

        foreach ($peminjamans as $peminjaman) {
            try {
                // Gabungkan tanggal & jam selesai
                $endTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $peminjaman->tanggalPinjam . ' ' . $peminjaman->jamSelesai);
            } catch (\Exception $e) {
                // Fallback format jika jamSelesai cuma H:i
                try {
                     $endTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $peminjaman->tanggalPinjam . ' ' . substr($peminjaman->jamSelesai, 0, 5));
                } catch (\Exception $ex) {
                    continue; // Skip invalid date format
                }
            }

            // Cek jika waktu tersisa <= 30 menit DAN belum lewat waktu selesai
            if ($endTime->greaterThan($now) && $endTime->diffInMinutes($now) <= 30) {
                // Cek apakah sudah dinotifikasi sebelumnya agar tidak spam?
                // Cara simpel: Cek notifikasi database terakhir
                $alreadyNotified = $peminjaman->mahasiswa->notifications()
                    ->where('type', \App\Notifications\PeminjamanAkanBerakhir::class)
                    ->get()
                    ->filter(function($note) use ($peminjaman) {
                        return ($note->data['peminjaman_id'] ?? null) == $peminjaman->id;
                    })
                    ->isNotEmpty();

                if (!$alreadyNotified) {
                    $peminjaman->mahasiswa->notify(new \App\Notifications\PeminjamanAkanBerakhir($peminjaman));
                    $this->info("Notified user {$peminjaman->mahasiswa->name} for peminjaman ID {$peminjaman->id}");
                }
            }
        }
    }
}
