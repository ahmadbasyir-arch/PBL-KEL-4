<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $namaLengkap
 * @property string $nim
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Secara eksplisit memberitahu Laravel untuk menggunakan tabel 'mahasiswa'.
     * INI ADALAH BAGIAN PALING PENTING.
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * Kolom-kolom yang boleh diisi saat membuat user baru.
     * @var array<int, string>
     */
    protected $fillable = [
        'namaLengkap',
        'username',
        'email',
        'nim',
        'role',
        'password',
    ];

    /**
     * Kolom yang harus disembunyikan saat data user diambil.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}