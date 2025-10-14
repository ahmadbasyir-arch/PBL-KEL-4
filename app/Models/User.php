<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // ✅ Pastikan tabel yang digunakan benar
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'namaLengkap',
        'username',
        'email',
        'nim',
        'role',
        'password',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 🔹 Tambahkan accessor untuk uniformitas tampilan di blade
    public function getNameAttribute()
    {
        return $this->namaLengkap ?? $this->username ?? 'Tanpa Nama';
    }
}