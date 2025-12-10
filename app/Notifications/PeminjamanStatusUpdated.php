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
        return ['database']; // Add 'whatsapp' channel here later if implementing gateway
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
}
