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

        // Tanggal dipilih user (jika tidak ada = hari ini)
        $selectedDate = $request->query('tanggal', now()->toDateString());

        // Ambil seluruh ruangan/unit
        $listData = $jenis === 'unit'
            ? Unit::orderBy('namaUnit')->get()
            : Ruangan::orderBy('namaRuangan')->get();

        foreach ($listData as $item) {

            // Query: cari peminjaman yang disetujui pada tanggal tersebut
            $q = Peminjaman::where('tanggalPinjam', $selectedDate)
                ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan']);

            if ($jenis === 'ruangan') {
                $q->where('idRuangan', $item->id);
            } else {
                $q->where('idUnit', $item->id);
            }

            // Jika ada peminjaman yg belum selesai → ruangan sedang digunakan
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

        foreach ($validated['items'] as $item) {
            $payload = [
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => 'pending',
            ];

            if ($user->role === 'dosen') {
                $payload['id_dosen'] = $user->id;
            } else {
                $payload['idMahasiswa'] = $user->id;
            }

            Peminjaman::create($payload);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }

    /**
     * EDIT PEMINJAMAN (tampilkan form edit)
     */
    public function edit($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($peminjaman, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit peminjaman ini.');
        }

        if (in_array($peminjaman->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Peminjaman yang sudah selesai atau ditolak tidak dapat diedit.');
        }

        $jenis = $peminjaman->idRuangan ? 'ruangan' : 'unit';

        $busyStatuses = ['disetujui', 'digunakan', 'pending', 'sedang digunakan'];

        if ($jenis === 'ruangan') {
            $busyIds = Peminjaman::where('tanggalPinjam', $peminjaman->tanggalPinjam)
                ->whereIn('status', $busyStatuses)
                ->whereNotNull('idRuangan')
                ->where('id', '!=', $peminjaman->id)
                ->pluck('idRuangan')
                ->toArray();

            $listData = Ruangan::whereNotIn('id', $busyIds)->orderBy('namaRuangan')->get();

            if ($peminjaman->idRuangan) {
                $own = Ruangan::find($peminjaman->idRuangan);
                if ($own && ! $listData->contains('id', $own->id)) {
                    $listData->push($own);
                }
            }
        } else {
            $busyIds = Peminjaman::where('tanggalPinjam', $peminjaman->tanggalPinjam)
                ->whereIn('status', $busyStatuses)
                ->whereNotNull('idUnit')
                ->where('id', '!=', $peminjaman->id)
                ->pluck('idUnit')
                ->toArray();

            $listData = Unit::whereNotIn('id', $busyIds)->orderBy('namaUnit')->get();

            if ($peminjaman->idUnit) {
                $own = Unit::find($peminjaman->idUnit);
                if ($own && ! $listData->contains('id', $own->id)) {
                    $listData->push($own);
                }
            }
        }

        $items = [
            (object)[
                'id' => $peminjaman->idRuangan ?? $peminjaman->idUnit
            ]
        ];

        $blade = $user->role === 'dosen' ? 'dosen.peminjaman_edit' : 'mahasiswa.peminjaman_edit';

        return view($blade, [
            'peminjaman' => $peminjaman,
            'jenis' => $jenis,
            'listData' => $listData,
            'items' => $items
        ]);
    }

    /**
     * UPDATE PEMINJAMAN
     */
    public function update(Request $request, $id)
    {
        $existing = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($existing, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah peminjaman ini.');
        }

        if (in_array($existing->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Peminjaman yang sudah selesai atau ditolak tidak dapat diubah.');
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

        foreach ($validated['items'] as $item) {
            $payload = [
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => $existing->status === 'pending' ? 'pending' : $existing->status,
            ];

            if ($user->role === 'dosen') {
                $payload['id_dosen'] = $user->id;
            } else {
                $payload['idMahasiswa'] = $user->id;
            }

            Peminjaman::create($payload);
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
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk aksi ini.');
        }

        if (! in_array($peminjaman->status, ['menyelesaikan', 'menunggu_validasi', 'selesai', 'ditolak'])) {
            $peminjaman->status = 'menyelesaikan';
            $peminjaman->save();

            return redirect()->back()->with('success', 'Pengajuan penyelesaian telah dikirim ke admin.');
        }

        return redirect()->back()->with('error', 'Status peminjaman tidak dapat diajukan penyelesaian lagi.');
    }

    /**
     * KEMBALIKAN
     */
    public function kembalikan(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        if (! $this->belongsToUser($peminjaman, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk aksi ini.');
        }

        if (in_array($peminjaman->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Peminjaman ini sudah selesai atau ditolak.');
        }

        $peminjaman->status = 'menyelesaikan';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Permintaan pengembalian telah dikirim. Tunggu validasi admin.');
    }

    /**
     * FIX UTAMA — perbaikan akses pemilik peminjaman
     */
    protected function belongsToUser(Peminjaman $peminjaman, $user)
    {
        if (! $user) return false;

        // Jika user = dosen → cek id_dosen
        if ($user->role === 'dosen') {
            return $peminjaman->id_dosen == $user->id;
        }

        // Jika user = mahasiswa → cek idMahasiswa
        if ($user->role === 'mahasiswa') {
            return $peminjaman->idMahasiswa == $user->id;
        }

        return false;
    }
}
