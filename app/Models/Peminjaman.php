<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman'; // nama tabel di database kamu
    protected $primaryKey = 'id'; // sesuaikan kalau primary key bukan 'id'

    protected $fillable = [
        'idMahasiswa',
        'idRuangan',
        'idUnit',
        'tanggalPinjam',
        'tanggalKembali',
        'status',
        'keperluan',
    ];
}