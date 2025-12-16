<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $type; // 'info', 'success', 'warning', 'danger'

    public function __construct($title, $message, $url = '#', $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'status' => $this->type, // Reuse existing 'status' key for icon logic if possible, or mapping
            'type' => 'admin_notification'
        ];
    }
}
