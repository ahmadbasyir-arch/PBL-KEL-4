<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_peminjaman_table.php
public function up()
{
    Schema::create('peminjaman', function (Blueprint $table) {
        $table->id();
        $table->foreignId('idMahasiswa')->constrained('mahasiswa');
        $table->foreignId('idRuangan')->nullable()->constrained('ruangan');
        $table->foreignId('idUnit')->nullable()->constrained('unit');
        $table->date('tanggalPinjam');
        $table->time('jamMulai');
        $table->time('jamSelesai');
        $table->text('keperluan');
        $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
        $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
