<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        // Data Ruangan (Nama Ruang from image which includes code)
        $roomNames = [
            'A01 - Java',
            'A02 - CI',
            'A03 - HTML',
            'A04 - Cisco',
            'A05 - MySQL',
            'A06 - PHP',
            'A07 - Android',
            'A08 - Bootstrap',
            'A09 - Lab. Komputer C',
            'A10 - Lab. Komputer A',
            'A11 - Lab. Komputer B',
            'A12 - Lab. Jaringan',
            'A13 - Lab. Komputer D',
            'A14 - jQuery',
            'A15 - C++',
            'A16 - Laboratorium Guido Van Rossum',
            'A17 - Laboratorium Steve Jobs',
            'A18 - Laboratorium Bill Gates',
            'A19 - Laboratorium Kenneth Thompson',
            'A20 - Laboratorium Linus Torvalds',
            'A21 - Lab. Komputer E',
            'A22 - Posko TA',
            'A29 - Phyton',
            'A30 - Ruang Teknisi Lab Kenneth Thompson',
            'A31 - Ruang Seminar Kenneth Thompson',
            'A32 - Ruang Teknisi Lab Linus Torvalds',
            'A33 - Ruang Seminar Lab Linus Torvalds',
            'A34 - Ruang Teknisi Lab Steve Jobs',
            'A35 - Ruang Seminar Lab Bill Gates',
            'A36 - Ruang Teknisi Lab Bill Gates',
            'D26 - Ruang Kelas 3 (Hima TI)',
            'GTIO1 - Aula GTI',
        ];

        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel ruangan (reset)
        DB::table('ruangan')->truncate();

        $data = [];
        foreach ($roomNames as $name) {
            $data[] = [
                'namaRuangan' => $name,
                'lokasi' => 'Gedung TI', // Default location
                'kapasitas' => 30,      // Default capacity
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('ruangan')->insert($data);

        Schema::enableForeignKeyConstraints();
    }
}
