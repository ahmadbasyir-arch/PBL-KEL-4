<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ruangan;
use App\Models\Unit;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /**
     * FORM PEMINJAMAN
     */
    public function create(Request $request)
    {
        $jenis = $request->query('jenis', 'ruangan');
        $selectedDate = $request->query('tanggal', now()->toDateString());

        $listData = $jenis === 'unit'
    ? Unit::orderBy('namaUnit')->get(['id','namaUnit','kodeUnit'])
    : Ruangan::orderBy('namaRuangan')->get(['id','namaRuangan']);


        foreach ($listData as $item) {
            $q = Peminjaman::where('tanggalPinjam', $selectedDate)
                ->whereIn('status', ['pending', 'disetujui', 'digunakan', 'sedang digunakan', 'menyelesaikan', 'menunggu_validasi']);

            $jenis === 'ruangan'
                ? $q->where('idRuangan', $item->id)
                : $q->where('idUnit', $item->id);

            $item->is_used = $q->exists();
        }

        $user = Auth::user();

        return view('mahasiswa.peminjaman_form', compact('jenis', 'listData', 'user'));
    }

    /**
     * SIMPAN PEMINJAMAN
     */
    public function store(Request $request)
    {
        $jenis = $request->input('jenis_item');

        $validated = $request->validate([
            'jenis_item'     => 'required|in:ruangan,unit',
            'tanggalPinjam'  => 'required|date|after_or_equal:today',
            'jamMulai'       => 'required',
            'jamSelesai'     => 'required|after:jamMulai',
            'keperluan'      => 'required|string|max:255',
            'items'          => 'required|array|min:1',
            'items.*.id'     => $jenis === 'unit'
                ? 'required|integer|exists:unit,id'
                : 'required|integer|exists:ruangan,id',
        ]);

        $user = Auth::user();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $user, $jenis) {
                foreach ($validated['items'] as $item) {
                    
                    // 🔒 CONCURRENCY CHECK (Pencegahan Double Booking)
                    // Cek lagi apakah di detik yang sama sudah ada yang booking
                    $isBooked = Peminjaman::where('tanggalPinjam', $validated['tanggalPinjam'])
                        ->where(function($query) use ($validated) {
                            $query->whereBetween('jamMulai', [$validated['jamMulai'], $validated['jamSelesai']])
                                ->orWhereBetween('jamSelesai', [$validated['jamMulai'], $validated['jamSelesai']])
                                ->orWhere(function($q) use ($validated) {
                                    $q->where('jamMulai', '<=', $validated['jamMulai'])
                                      ->where('jamSelesai', '>=', $validated['jamSelesai']);
                                });
                        })
                        ->whereIn('status', ['pending', 'disetujui', 'digunakan', 'sedang digunakan', 'menyelesaikan', 'menunggu_validasi'])
                        ->where(function($q) use ($jenis, $item) {
                            if ($jenis === 'ruangan') {
                                $q->where('idRuangan', $item['id']);
                            } else {
                                $q->where('idUnit', $item['id']);
                            }
                        })
                        ->lockForUpdate() // 🔒 Lock row untuk mencegah race condition
                        ->exists();

                    if ($isBooked) {
                        throw new \Exception("Maaf, salah satu item yang dipilih baru saja dipesan oleh orang lain. Silakan pilih waktu atau item lain.");
                    }

                    $peminjaman = Peminjaman::create([
                        'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                        'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                        'tanggalPinjam' => $validated['tanggalPinjam'],
                        'jamMulai'      => $validated['jamMulai'],
                        'jamSelesai'    => $validated['jamSelesai'],
                        'keperluan'     => $validated['keperluan'],
                        'status'        => 'pending',
                        'idMahasiswa'   => $user->id,
                    ]);

                    // Kirim Notifikasi (Web & WA) ke User
                    $user->notify(new \App\Notifications\PeminjamanDiajukan($peminjaman));

                    // 🔔 NOTIFIKASI KE ADMIN
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    $jenisText = $jenis === 'ruangan' ? 'Ruangan' : 'Unit';
                    $namaItem = $jenis === 'ruangan' 
                        ? (\App\Models\Ruangan::find($item['id'])->namaRuangan ?? 'Ruangan') 
                        : (\App\Models\Unit::find($item['id'])->namaUnit ?? 'Unit');

                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification(
                        'Peminjaman Baru',
                        "{$user->name} mengajukan peminjaman $jenisText: $namaItem",
                        route('admin.peminjaman.show', $peminjaman->id),
                        'info'
                    ));
                }
            });

            return redirect()->route('dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * EDIT PEMINJAMAN
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($peminjaman, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if (in_array($peminjaman->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dapat diedit.');
        }

        $jenis = $peminjaman->idRuangan ? 'ruangan' : 'unit';
        $busyStatuses = ['pending', 'disetujui', 'digunakan', 'sedang digunakan', 'menyelesaikan', 'menunggu_validasi'];

        if ($jenis === 'ruangan') {
            $busyIds = Peminjaman::where('tanggalPinjam', $peminjaman->tanggalPinjam)
                ->whereIn('status', $busyStatuses)
                ->whereNotNull('idRuangan')
                ->where('id', '!=', $peminjaman->id)
                ->pluck('idRuangan')
                ->toArray();

            $listData = Ruangan::whereNotIn('id', $busyIds)->orderBy('namaRuangan')->get();

            $own = Ruangan::find($peminjaman->idRuangan);
            if ($own && ! $listData->contains('id', $own->id)) {
                $listData->push($own);
            }
        } else {
            $busyIds = Peminjaman::where('tanggalPinjam', $peminjaman->tanggalPinjam)
                ->whereIn('status', $busyStatuses)
                ->whereNotNull('idUnit')
                ->where('id', '!=', $peminjaman->id)
                ->pluck('idUnit')
                ->toArray();

            $listData = Unit::whereNotIn('id', $busyIds)->orderBy('namaUnit')->get();

            $own = Unit::find($peminjaman->idUnit);
            if ($own && ! $listData->contains('id', $own->id)) {
                $listData->push($own);
            }
        }

        $items = [
            (object)[ 'id' => $peminjaman->idRuangan ?? $peminjaman->idUnit ]
        ];

        $blade = $user->role === 'dosen'
            ? 'dosen.peminjaman_edit'
            : 'mahasiswa.peminjaman_edit';

        return view($blade, compact('peminjaman', 'jenis', 'listData', 'items'));
    }

    /**
     * UPDATE PEMINJAMAN
     */
    public function update(Request $request, $id)
    {
        $existing = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($existing, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        if (in_array($existing->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Peminjaman tidak dapat diubah.');
        }

        $jenis = $request->input('jenis_item', ($existing->idRuangan ? 'ruangan' : 'unit'));

        $validated = $request->validate([
            'jenis_item'     => 'required|in:ruangan,unit',
            'tanggalPinjam'  => 'required|date',
            'jamMulai'       => 'required',
            'jamSelesai'     => 'required|after:jamMulai',
            'keperluan'      => 'required|string|max:255',
            'items'          => 'required|array|min:1',
            'items.*.id'     => $jenis === 'unit'
                ? 'required|integer|exists:unit,id'
                : 'required|integer|exists:ruangan,id',
        ]);

        $existing->delete();

        $newPeminjamanId = null;

        foreach ($validated['items'] as $item) {
            $newPeminjaman = Peminjaman::create([
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => $existing->status === 'pending' ? 'pending' : $existing->status,
                'idMahasiswa'   => $user->id,
            ]);
            $newPeminjamanId = $newPeminjaman->id;
        }

        // 🔔 NOTIFIKASI KE ADMIN (UPDATE)
        if ($newPeminjamanId) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            $roleLabel = ucfirst($user->role); // Mahasiswa / Dosen
            $jenisText = $jenis === 'ruangan' ? 'Ruangan' : 'Unit';
            
            // Ambil nama item dari item pertama
            $firstItem = $validated['items'][0];
            $namaItem = $jenis === 'ruangan' 
                ? (\App\Models\Ruangan::find($firstItem['id'])->namaRuangan ?? 'Item') 
                : (\App\Models\Unit::find($firstItem['id'])->namaUnit ?? 'Item');

            // 1. Notifikasi ke ADMIN
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification(
                'Perubahan Peminjaman',
                "$roleLabel {$user->name} telah mengubah detail peminjaman $jenisText: $namaItem.",
                route('admin.peminjaman.show', $newPeminjamanId),
                'info'
            ));

            // 2. Notifikasi ke USER (Mahasiswa/Dosen yg bersangkutan)
            $user->notify(new \App\Notifications\AdminNotification(
                'Peminjaman Diubah',
                "Anda berhasil memperbarui data peminjaman $jenisText: $namaItem.",
                route('peminjaman.show', $newPeminjamanId), // Arahkan ke detail peminjaman user
                'success'
            ));
        }

        return redirect()->route('dashboard')->with('success', 'Peminjaman berhasil diperbarui.');
    }

    /**
     * AJUKAN SELESAI
     */
    public function ajukanSelesai(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($peminjaman, $user)) {
            return back()->with('error', 'Tidak memiliki akses.');
        }

        if (! in_array($peminjaman->status, ['menyelesaikan', 'menunggu_validasi', 'selesai', 'ditolak'])) {
            $peminjaman->status = 'menyelesaikan';
            $peminjaman->save();

            // 🔔 Kirim Notifikasi (Web & WA) ke User
            $user->notify(new \App\Notifications\PeminjamanStatusUpdated($peminjaman, 'menyelesaikan'));

            // 🔔 NOTIFIKASI KE ADMIN
            $admins = \App\Models\User::where('role', 'admin')->get();
            $namaItem = $peminjaman->ruangan->namaRuangan ?? ($peminjaman->unit->namaUnit ?? 'Item');
            
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification(
                'Pengajuan Pengembalian',
                "{$user->name} mengajukan pengembalian untuk $namaItem.",
                route('admin.peminjaman.show', $peminjaman->id),
                'warning'
            ));

            return back()->with('success', 'Pengajuan penyelesaian dikirim.');
        }

        return back()->with('error', 'Tidak dapat diajukan lagi.');
    }

    /**
     * KEMBALIKAN PEMINJAMAN
     */
    public function kembalikan(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($peminjaman, $user)) {
            return back()->with('error', 'Tidak memiliki akses.');
        }

        if (in_array($peminjaman->status, ['selesai', 'ditolak'])) {
            return back()->with('error', 'Peminjaman sudah selesai.');
        }

        $peminjaman->status = 'menyelesaikan';
        $peminjaman->save();

        // 🔔 Kirim Notifikasi (Web & WA)
        $user->notify(new \App\Notifications\PeminjamanStatusUpdated($peminjaman, 'menyelesaikan'));

        // 🔔 NOTIFIKASI KE ADMIN
        $admins = \App\Models\User::where('role', 'admin')->get();
        $namaItem = $peminjaman->ruangan->namaRuangan ?? ($peminjaman->unit->namaUnit ?? 'Item');
        
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification(
            'Pengajuan Pengembalian',
            "{$user->name} ingin mengembalikan $namaItem.",
            route('admin.peminjaman.show', $peminjaman->id),
            'warning'
        ));

        return back()->with('success', 'Permintaan pengembalian dikirim.');
    }

    /**
     * CEK PEMILIK PEMINJAMAN (DOSEN & MAHASISWA SAMA)
     */
    protected function belongsToUser(Peminjaman $peminjaman, $user)
    {
        return $peminjaman->idMahasiswa == $user->id;
    }

    /**
     * SIMPAN FEEDBACK
     */
    public function storeFeedback(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Cek kepemilikan
        if ($peminjaman->idMahasiswa != Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        // Cek status harus selesai
        if ($peminjaman->status != 'selesai') {
            return back()->with('error', 'Hanya peminjaman selesai yang bisa diberi feedback.');
        }

        // Cek apakah sudah ada feedback
        if (\App\Models\Feedback::where('peminjaman_id', $id)->exists()) {
            return back()->with('error', 'Anda sudah memberikan feedback.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        \App\Models\Feedback::create([
            'peminjaman_id' => $id,
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'],
        ]);

        return back()->with('success', 'Terima kasih atas masukan Anda!');
    }
}
