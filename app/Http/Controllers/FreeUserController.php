<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class FreeUserController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        
        // --- 1. Statistik Ringkas ---
        $ruanganDipakai = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan'])
            ->whereNotNull('idRuangan')
            ->count();

        $unitDipakai = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan'])
            ->whereNotNull('idUnit')
            ->count();

        // --- 2. Data untuk Tabel Status (Restored) ---
        $statusSedangDipakai = ['disetujui', 'digunakan', 'sedang digunakan'];

        // Ambil ID Ruangan & Unit yang sedang dipakai HARI INI
        $usedRuanganIds = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idRuangan')
            ->pluck('idRuangan')
            ->toArray();

        $usedUnitIds = Peminjaman::whereDate('tanggalPinjam', $today)
            ->whereIn('status', $statusSedangDipakai)
            ->whereNotNull('idUnit')
            ->pluck('idUnit')
            ->toArray();

        // Ambil SEMUA Data Ruangan & Unit
        $allRuangan = \App\Models\Ruangan::orderBy('namaRuangan')->get();
        $allUnits = \App\Models\Unit::orderBy('namaUnit')->get();

        // Map status untuk Ruangan
        $allRuangan->map(function ($room) use ($usedRuanganIds) {
            $room->status = in_array($room->id, $usedRuanganIds) ? 'dipakai' : 'tersedia';
            return $room;
        });

        // Map status untuk Unit (Proyektor)
        $allUnits->map(function ($unit) use ($usedUnitIds) {
            $unit->status = in_array($unit->id, $usedUnitIds) ? 'dipakai' : 'tersedia';
            return $unit;
        });

        return view('freeuser.home', compact('ruanganDipakai', 'unitDipakai', 'allRuangan', 'allUnits'));
    }

    public function getEvents()
    {
        // Ambil peminjaman yang statusnya aktif
        $bookings = Peminjaman::with(['ruangan', 'unit', 'mahasiswa', 'dosen'])
            ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan', 'selesai'])
            ->get();

        $events = $bookings->map(function ($booking) {
            $title = '';
            $color = '';
            $resourceType = ''; 

            if ($booking->idRuangan) {
                // Gunakan optional chaining atau cek null untuk menghindari error jika data ruangan terhapus
                $title = $booking->ruangan ? $booking->ruangan->namaRuangan : 'Ruangan Hapus';
                $color = '#3b82f6'; // Blue
                $resourceType = 'ruangan';
            } elseif ($booking->idUnit) {
                $title = $booking->unit ? $booking->unit->namaUnit : 'Unit Hapus';
                $color = '#f59e0b'; // Orange
                $resourceType = 'unit';
            } else {
                $title = 'Peminjaman';
                $color = '#6b7280';
            }

            if($booking->kegiatan) {
                $title .= ' - ' . $booking->kegiatan;
            }

            $peminjamName = 'User';
            if ($booking->mahasiswa) {
                $peminjamName = $booking->mahasiswa->nama;
            } elseif ($booking->dosen) {
                $peminjamName = $booking->dosen->nama;
            }

            // CRITICAL FIX: Replace '.' with ':' in time strings for ISO8601 compatibility
            // e.g. "08.00" -> "08:00"
            $jamMulai = str_replace('.', ':', $booking->jamMulai);
            $jamSelesai = str_replace('.', ':', $booking->jamSelesai);

            return [
                'id' => $booking->id,
                'title' => $title,
                'start' => $booking->tanggalPinjam . 'T' . $jamMulai,
                'end' => $booking->tanggalPinjam . 'T' . $jamSelesai,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $booking->status,
                    'type' => $resourceType,
                    'peminjam' => $peminjamName,
                ]
            ];
        });

        return response()->json($events);
    }
}