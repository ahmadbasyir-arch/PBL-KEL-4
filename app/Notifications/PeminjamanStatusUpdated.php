<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanStatusUpdated extends Notification
{
    use Queueable;

    public $peminjaman;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($peminjaman, $status)
    {
        $this->peminjaman = $peminjaman;
        $this->status = $status;
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
        // Custom logic for message
        $message = 'Status peminjaman Anda telah diperbarui menjadi: ' . ucfirst($this->status);
        if ($this->status == 'disetujui') {
            $message = 'Peminjaman Anda telah DISETUJUI. Silakan cek detailnya.';
        } elseif ($this->status == 'ditolak') {
            $message = 'Maaf, permohonan peminjaman Anda DITOLAK.';
        } elseif ($this->status == 'selesai') {
            $message = 'Peminjaman Anda telah SELESAI. Terima kasih.';
        }

        return [
            'peminjaman_id' => $this->peminjaman->id,
            'status' => $this->status,
            'message' => $message,
            'url' => route('riwayat'), // Link to history page
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
        
        // Header & Message logic
        $header = "";
        $body = "";
        $emoji = "";

        if ($this->status == 'disetujui') {
            $header = "PEMINJAMAN DISETUJUI";
            $body = "Kabar baik! Peminjaman Anda telah *DISETUJUI* oleh Admin.";
            $emoji = "✅";
        } elseif ($this->status == 'ditolak') {
            $header = "PEMINJAMAN DITOLAK";
            $body = "Mohon maaf, pengajuan peminjaman Anda *DITOLAK*.";
            $emoji = "❌";
        } elseif ($this->status == 'selesai') {
            $header = "PEMINJAMAN SELESAI";
            $body = "Peminjaman Anda telah ditandai *SELESAI*. Terima kasih telah menggunakan fasilitas kami.";
            $emoji = "🏁";
        } else {
            $header = "STATUS PEMINJAMAN UPDATE";
            $body = "Status peminjaman Anda berubah menjadi: *" . ucfirst($this->status) . "*.";
            $emoji = "ℹ️";
        }

        return "*$header* $emoji\n\n" .
               "Halo, {$notifiable->name}.\n" .
               "$body\n\n" .
               "Detail Item:\n" .
               "- Jenis: $jenis\n" .
               "- Nama: $namaItem\n" .
               "- Tanggal: $tgl\n\n" .
               "Login ke dashboard untuk info lebih lanjut.";
    }
}
