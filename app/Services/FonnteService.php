<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    public function sendMessage($target, $message)
    {
        $url = "https://api.fonnte.com/send";

        return Http::withHeaders([
            'Authorization' => env('FONNTE_API_KEY')
        ])->post($url, [
            'target'  => $target,
            'message' => $message
        ]);
    }
}