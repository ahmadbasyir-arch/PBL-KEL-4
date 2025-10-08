<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status jadi VARCHAR(50)
        DB::statement("ALTER TABLE `peminjaman` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Balik lagi ke enum kalau perlu rollback
        DB::statement("ALTER TABLE `peminjaman` MODIFY `status` ENUM('pending','disetujui','ditolak','selesai') NOT NULL DEFAULT 'pending'");
    }
};