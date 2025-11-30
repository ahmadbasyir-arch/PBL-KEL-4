<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'namaUnit',
        'kodeUnit', // ← DITAMBAHKAN
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'idUnit');
    }
}
