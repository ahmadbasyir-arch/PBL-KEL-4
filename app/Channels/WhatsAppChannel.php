<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FonnteService;

class WhatsAppChannel
{
    protected $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        $this->fonnte = $fonnte;
    }

    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        
        // Asumsikan user model punya kolom 'telepon' atau 'no_hp'
        $target = $notifiable->telepon ?? $notifiable->no_hp;

        if (!$target) {
            return;
        }

        try {
            $this->fonnte->sendMessage($target, $message);
        } catch (\Exception $e) {
            // Log error or ignore if silent failure is desired
            \Log::error('WA Notification Error: ' . $e->getMessage());
        }
    }
}
