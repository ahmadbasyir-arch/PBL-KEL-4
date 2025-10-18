<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // 1️⃣ Hapus foreign key lama (ke tabel mahasiswa)
            $table->dropForeign(['idMahasiswa']);

            // 2️⃣ Ubah relasi ke tabel users
            $table->foreign('idMahasiswa')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['idMahasiswa']);
            $table->foreign('idMahasiswa')
                  ->references('id')
                  ->on('mahasiswa')
                  ->onDelete('cascade');
        });
    }
};