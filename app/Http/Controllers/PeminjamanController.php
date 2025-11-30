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
                ->whereIn('status', ['disetujui', 'digunakan', 'sedang digunakan']);

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

        foreach ($validated['items'] as $item) {
            Peminjaman::create([
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => 'pending',

                // SOLUSI UTAMA — dosen & mahasiswa disimpan di kolom sama
                'idMahasiswa'   => $user->id,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
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
        $busyStatuses = ['disetujui', 'digunakan', 'pending', 'sedang digunakan'];

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

        foreach ($validated['items'] as $item) {
            Peminjaman::create([
                'idRuangan'     => $jenis === 'ruangan' ? $item['id'] : null,
                'idUnit'        => $jenis === 'unit' ? $item['id'] : null,
                'tanggalPinjam' => $validated['tanggalPinjam'],
                'jamMulai'      => $validated['jamMulai'],
                'jamSelesai'    => $validated['jamSelesai'],
                'keperluan'     => $validated['keperluan'],
                'status'        => $existing->status === 'pending' ? 'pending' : $existing->status,

                // SOLUSI UTAMA
                'idMahasiswa'   => $user->id,
            ]);
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

        return back()->with('success', 'Permintaan pengembalian dikirim.');
    }

    /**
     * CEK PEMILIK PEMINJAMAN (DOSEN & MAHASISWA SAMA)
     */
    protected function belongsToUser(Peminjaman $peminjaman, $user)
    {
        return $peminjaman->idMahasiswa == $user->id;
    }
}
