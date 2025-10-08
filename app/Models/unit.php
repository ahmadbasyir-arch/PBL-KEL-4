<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';
    protected $primaryKey = 'id';

    protected $fillable = [
        'namaUnit',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'idUnit');
    }
}