<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'namaRuangan',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'idRuangan');
    }
}