<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('namaLengkap', 100);
            $table->string('nim', 20)->unique(); // nim/nip harus unik
            $table->string('email', 100)->unique();
            $table->string('username', 50)->unique();
            $table->string('password'); // Laravel otomatis mengurus panjangnya (255)
            $table->enum('role', ['mahasiswa', 'dosen', 'admin', 'staff'])->default('mahasiswa');
            $table->timestamps(); // membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
