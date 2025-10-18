<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom kondisi & catatan pengembalian ke tabel peminjaman
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // ✅ Tambahkan kolom kondisi dan catatan pengembalian (boleh kosong)
            if (!Schema::hasColumn('peminjaman', 'kondisi_pengembalian')) {
                $table->enum('kondisi_pengembalian', ['Baik', 'Kurang Baik', 'Rusak'])->nullable()->after('status');
            }

            if (!Schema::hasColumn('peminjaman', 'catatan_pengembalian')) {
                $table->text('catatan_pengembalian')->nullable()->after('kondisi_pengembalian');
            }
        });
    }

    /**
     * Hapus kolom jika rollback migration
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'kondisi_pengembalian')) {
                $table->dropColumn('kondisi_pengembalian');
            }

            if (Schema::hasColumn('peminjaman', 'catatan_pengembalian')) {
                $table->dropColumn('catatan_pengembalian');
            }
        });
    }
};