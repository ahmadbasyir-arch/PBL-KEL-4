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
    'id_dosen',   // <-- WAJIB DITAMBAHKAN
    'idRuangan',
    'idUnit',
    'tanggalPinjam',
    'jamMulai',
    'jamSelesai',
    'status',     // <-- UPDATE status akan BERHASIL setelah ini
    'keperluan',
];


    // Relasi ke tabel users (mahasiswa)
    public function mahasiswa()
    {
        return $this->belongsTo(\App\Models\User::class, 'idMahasiswa');
    }

    // 🔹 Alias relasi user, untuk konsistensi kode
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'idMahasiswa');
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

    // Relasi ke tabel pengembalian
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'idPeminjaman');
    }
}