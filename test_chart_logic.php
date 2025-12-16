<?php
require __DIR__.'/vendor/autoload.php';
use Carbon\Carbon;

$durasiBuckets = [
    '0-2'  => 0,
    '3-5'  => 0,
    '6-10' => 0,
    '>10'  => 0,
];

// Mock data inputs based on user scenario
$rows = [
    // 2 hours exactly -> Expect '0-2'
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '10:00:00', 'jamSelesai' => '12:00:00'], 
    
    // 3 hours -> Expect '3-5'
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '10:00', 'jamSelesai' => '13:00'],      
    
    // 2.5 hours -> Expect '3-5'
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '14:00', 'jamSelesai' => '16:30'],      
    
    // 4 hours (overnight) -> Expect '3-5'
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '22:00', 'jamSelesai' => '02:00'],      
    
    // 5 hours -> Expect '3-5' (since logic is <= 5)
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '12:00', 'jamSelesai' => '17:00'],

    // 1 hour -> Expect '0-2'
    (object)['tanggalPinjam' => '2023-12-16', 'jamMulai' => '10:00', 'jamSelesai' => '11:00'],
];

foreach ($rows as $r) {
    echo "Processing: {$r->tanggalPinjam} {$r->jamMulai} to {$r->jamSelesai}\n";
    try {
        $start = Carbon::parse($r->tanggalPinjam.' '.$r->jamMulai);
        $end   = Carbon::parse($r->tanggalPinjam.' '.$r->jamSelesai);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $jam = $end->diffInMinutes($start) / 60;
        echo "  Duration: $jam hours\n";

        if ($jam <= 2) {
             echo "  >> Category: 0-2\n";
             $durasiBuckets['0-2']++;
        }
        elseif ($jam <= 5) {
             echo "  >> Category: 3-5\n";
             $durasiBuckets['3-5']++;
        }
        elseif ($jam <= 10) {
             echo "  >> Category: 6-10\n";
             $durasiBuckets['6-10']++;
        }
        else {
             echo "  >> Category: >10\n";
             $durasiBuckets['>10']++;
        }
    } catch (\Throwable $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}

print_r($durasiBuckets);
