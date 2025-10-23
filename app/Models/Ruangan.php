<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    // 🔹 pastikan nama tabel sesuai di database
    protected $table = 'ruangan'; 

    protected $fillable = [
        'namaRuangan',
        'lokasi',
        'kapasitas',
    ];

    public $timestamps = false; // karena kolom created_at dan updated_at NULL
}