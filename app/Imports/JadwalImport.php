<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JadwalImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Headers are expected to be normalized (snake_case) by default, 
        // e.g., 'Mata Kuliah' -> 'mata_kuliah'
        
        $kodeRuangan = trim($row['ruangan'] ?? $row['kode_ruangan'] ?? $row['nama_ruangan'] ?? '');
        $ruangan = Ruangan::where('namaRuangan', $kodeRuangan)->first();

        // Parse Time (Excel sometimes returns float for time, but if cell is text it's fine)
        // Ideally we expect H:i format.
        
        return new Jadwal([
            'mata_kuliah' => $row['mata_kuliah'] ?? $row['matkul'] ?? '',
            'dosen'       => $row['dosen'] ?? '',
            'kelas'       => $row['kelas'] ?? '',
            'hari'        => $row['hari'] ?? '',
            'jam_mulai'   => $this->transformTime($row['jam_mulai'] ?? ''),
            'jam_selesai' => $this->transformTime($row['jam_selesai'] ?? ''),
            'ruangan_id'  => $ruangan ? $ruangan->id : null,
        ]);
    }

    private function transformTime($value)
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                // If excel serial timestamp (fraction of day)
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i');
            }
            return \Carbon\Carbon::parse($value)->format('H:i');
        } catch (\Exception $e) {
            return $value; // Fallback
        }
    }
}
