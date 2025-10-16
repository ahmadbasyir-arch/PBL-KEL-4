<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'idMahasiswa',
        'idRuangan',
        'idUnit',
        'tanggalPinjam',
        'jamMulai',
        'jamSelesai',
        'status',
        'keperluan',
    ];

    // Relasi ke tabel users (mahasiswa)
    public function user()
    {
        return $this->belongsTo(User::class, 'idMahasiswa');
    }

    // Relasi ke tabel ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'idRuangan');
    }

    // Relasi ke tabel unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'idUnit');
    }
}