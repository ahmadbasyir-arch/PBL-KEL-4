<?php

namespace App\Http\Controllers;

use App\Services\FonnteService;

class FonnteController extends Controller
{
    public function test()
    {
        $wa = new FonnteService();
        $send = $wa->sendMessage(
            '62', // nomor tujuan
            'Halo! Notifikasi dari Laravel berhasil.' // isi pesan
        );

        return $send->json(); // untuk debug
    }
}