<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JadwalImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Normalize Keys (Lowercase) - Maatwebsite usually does this, but good to be safe if using heading row.
        // We will stick to accessing via the array keys provided by WithHeadingRow (slugged).

        // 2. Resolve 'Mata Kuliah'
        $mataKuliah = $row['mata_kuliah'] ?? $row['matkul'] ?? $row['mk'] ?? $row['nama_matkul'] ?? null;

        // 3. Resolve 'Dosen'
        $dosen = $row['dosen'] ?? $row['nama_dosen'] ?? $row['pengajar'] ?? null;

        // 4. Resolve 'Kelas'
        $kelas = $row['kelas'] ?? $row['kls'] ?? null;

        // 5. Resolve 'Hari'
        $hari = $row['hari'] ?? $row['day'] ?? null;

        // 6. Resolve Time (Jam Mulai & Jam Selesai)
        $jamMulai = null;
        $jamSelesai = null;

        // Check for specific columns first
        if (isset($row['jam_mulai'])) $jamMulai = $row['jam_mulai'];
        elseif (isset($row['start'])) $jamMulai = $row['start'];
        elseif (isset($row['mulai'])) $jamMulai = $row['mulai'];

        if (isset($row['jam_selesai'])) $jamSelesai = $row['jam_selesai'];
        elseif (isset($row['end'])) $jamSelesai = $row['end'];
        elseif (isset($row['selesai'])) $jamSelesai = $row['selesai'];

        // If specific columns missing, check for "Jam" or "Waktu" range (e.g. "08:00-10:00")
        if (!$jamMulai || !$jamSelesai) {
            $waktu = $row['jam'] ?? $row['waktu'] ?? $row['pukul'] ?? $row['time'] ?? null;
            if ($waktu) {
                // Split by '-' or 's.d' or 'to'
                $parts = preg_split('/(-|s\.d|to)/i', $waktu);
                if (count($parts) >= 2) {
                    $jamMulai = trim($parts[0]);
                    $jamSelesai = trim($parts[1]);
                }
            }
        }

        // 7. Resolve 'Ruangan'
        $kodeRuangan = trim($row['ruangan'] ?? $row['kode_ruangan'] ?? $row['nama_ruangan'] ?? $row['room'] ?? '');
        $ruangan = null;
        if ($kodeRuangan) {
            // Try explicit match first
            $ruangan = Ruangan::where('namaRuangan', $kodeRuangan)->first();
            
            // Optional: Fuzzy match or code match if you had a 'kode' column
            // if (!$ruangan) $ruangan = Ruangan::where('kode', $kodeRuangan)->first();
        }

        // Clean & Formatted Times
        $formattedJamMulai = $this->transformTime($jamMulai);
        $formattedJamSelesai = $this->transformTime($jamSelesai);

        // Skip if critical data is missing
        if (!$mataKuliah || !$hari) {
            return null;
        }

        return new Jadwal([
            'mata_kuliah' => $mataKuliah,
            'dosen'       => $dosen,
            'kelas'       => $kelas,
            'hari'        => ucwords(strtolower($hari)), // Format Hari (e.g. senin -> Senin)
            'jam_mulai'   => $formattedJamMulai,
            'jam_selesai' => $formattedJamSelesai,
            'ruangan_id'  => $ruangan ? $ruangan->id : null,
        ]);
    }

    public function rules(): array
    {
        return [
            // '*.mata_kuliah' => 'required', // Can't strictly enforce because keys might vary
            // We handle nulls in model() to be graceful
        ];
    }

    private function transformTime($value)
    {
        if (!$value) return null;
        try {
            // Remove non-alphanumeric chars except : and .
            $clean = preg_replace('/[^0-9:\.]/', '', $value);
            
            if (is_numeric($value)) {
                // Excel serial date handling
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i');
            }
            
            // Try standard parse
            return \Carbon\Carbon::parse($clean)->format('H:i');
        } catch (\Exception $e) {
            return null; 
        }
    }
}
