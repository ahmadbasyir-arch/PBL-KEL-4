<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanDiajukan extends Notification
{
    use Queueable;

    public $peminjaman;

    /**
     * Create a new notification instance.
     */
    public function __construct($peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', \App\Channels\WhatsAppChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $item = $this->peminjaman->ruangan ?? $this->peminjaman->unit;
        $jenis = $this->peminjaman->ruangan ? 'Ruangan' : 'Unit';
        
        $namaItem = 'Item tidak ditemukan';
        if ($item) {
            $namaItem = $jenis === 'Ruangan' ? $item->namaRuangan : $item->namaUnit;
        }

        return [
            'peminjaman_id' => $this->peminjaman->id,
            'status' => 'diajukan',
            'message' => "Anda berhasil mengajukan peminjaman $jenis: $namaItem. Mohon tunggu persetujuan.",
            'url' => route('riwayat'),
            'created_at' => now(),
        ];
    }

    public function toWhatsApp($notifiable)
    {
        $item = $this->peminjaman->ruangan ?? $this->peminjaman->unit;
        $jenis = $this->peminjaman->ruangan ? 'Ruangan' : 'Unit';
        
        $namaItem = 'Item tidak ditemukan';
        if ($item) {
            $namaItem = $jenis === 'Ruangan' ? $item->namaRuangan : $item->namaUnit;
        }
        
        $tgl = \Carbon\Carbon::parse($this->peminjaman->tanggalPinjam)->format('d-m-Y');
        $jam = $this->peminjaman->jamMulai . ' - ' . $this->peminjaman->jamSelesai;

        return "*PEMINJAMAN DIAJUKAN*\n\n" .
               "Halo, {$notifiable->name}.\n" .
               "Pengajuan peminjaman Anda berhasil masuk sistem.\n\n" .
               "Detail:\n" .
               "- Jenis: $jenis\n" .
               "- Nama: $namaItem\n" .
               "- Tanggal: $tgl\n" .
               "- Jam: $jam\n\n" .
               "Mohon tunggu persetujuan dari Admin. Anda akan menerima notifikasi selanjutnya saat status berubah.";
    }
}
