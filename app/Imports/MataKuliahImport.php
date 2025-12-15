<?php

namespace App\Imports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Headers from image: Kurikulum, Kode, Mata Kuliah, Semester
        // Normalized keys: kurikulum, kode, mata_kuliah, semester
        
        $kode = $row['kode'] ?? null;
        $nama = $row['mata_kuliah'] ?? $row['nama_matkul'] ?? null;
        
        if (!$kode || !$nama) return null;

        // Use updateOrCreate to avoid duplicates
        return MataKuliah::updateOrCreate(
            ['kode' => $kode],
            [
                'nama_matkul' => $nama,
                'semester'    => $row['semester'] ?? 1,
                'kurikulum'   => $row['kurikulum'] ?? date('Y'),
            ]
        );
    }
}
