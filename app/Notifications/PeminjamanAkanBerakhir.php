<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanAkanBerakhir extends Notification
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
        $namaItem = $item ? $item->nama : 'Item tidak ditemukan'; // Assuming 'nama' is the attribute, check models if unsure (actually it is namaRuangan/namaUnit, need to handle that)
        
        // Let's refine the name fetching
        if ($this->peminjaman->ruangan) {
            $namaItem = $this->peminjaman->ruangan->namaRuangan;
        } elseif ($this->peminjaman->unit) {
            $namaItem = $this->peminjaman->unit->namaUnit;
        }

        $jenis = $this->peminjaman->ruangan ? 'Ruangan' : 'Unit';

        return [
            'peminjaman_id' => $this->peminjaman->id,
            'status' => 'peringatan',
            'message' => "PERINGATAN: Peminjaman $jenis $namaItem Anda akan berakhir dalam 30 menit.",
            'url' => route('riwayat'),
            'created_at' => now(),
        ];
    }

    public function toWhatsApp($notifiable)
    {
        $namaItem = '-';
        if ($this->peminjaman->ruangan) {
            $namaItem = $this->peminjaman->ruangan->namaRuangan;
        } elseif ($this->peminjaman->unit) {
            $namaItem = $this->peminjaman->unit->namaUnit;
        }
        $jenis = $this->peminjaman->ruangan ? 'Ruangan' : 'Unit';
        $jamSelesai = $this->peminjaman->jamSelesai;

        return "*PERINGATAN WAKTU HABIS*\n\n" .
               "Halo, {$notifiable->name}.\n" .
               "Waktu peminjaman Anda hampir habis (kurang dari 30 menit).\n\n" .
               "Detail:\n" .
               "- Jenis: $jenis\n" .
               "- Nama: $namaItem\n" .
               "- Jam Selesai: $jamSelesai\n\n" .
               "Harap segera menyelesaikan peminjaman atau mengembalikan barang tepat waktu.";
    }
}
