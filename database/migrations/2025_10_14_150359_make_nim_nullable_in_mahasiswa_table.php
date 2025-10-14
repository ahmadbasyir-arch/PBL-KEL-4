<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('mahasiswa', function (Blueprint $table) {
        $table->string('nim')->nullable()->change(); // ubah jadi nullable
    });
}


    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('nim')->nullable(false)->change();
        });
    }
};