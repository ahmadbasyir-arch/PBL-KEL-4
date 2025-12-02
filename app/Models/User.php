<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

protected $fillable = [
    'name',
    'username',
    'email',
    'namaLengkap',
    'foto_profil',
    'provider',
    'role',
    'password',
    'is_completed',
    'nim',
    'google_id',
    'telepon',
];




    protected $hidden = [
        'password',
        'remember_token',
    ];

public function mahasiswa()
{
    return $this->hasOne(Mahasiswa::class, 'user_id');
}


    // 🔹 Tambahkan accessor untuk uniformitas tampilan di blade
    public function getNameAttribute()
    {
        return $this->namaLengkap ?? $this->username ?? 'Tanpa Nama';
    }
}