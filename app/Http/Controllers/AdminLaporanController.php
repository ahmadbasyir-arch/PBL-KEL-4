<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Feedback;
use PDF; // Requires dompdf package, if not available we use window.print()

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ?? date('Y-m-d');
        $endDate = $request->get('end_date') ?? date('Y-m-d');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');

        $data = $this->getFilteredData($type, $startDate, $endDate, $status);

        return view('admin.laporan.index', compact('data', 'type', 'startDate', 'endDate', 'status'));
    }

    public function print(Request $request)
    {
        $startDate = $request->get('start_date') ?? date('Y-m-d');
        $endDate = $request->get('end_date') ?? date('Y-m-d');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');

        $data = $this->getFilteredData($type, $startDate, $endDate, $status);

        return view('admin.laporan.print', compact('data', 'type', 'startDate', 'endDate', 'status'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date') ?? date('Y-m-d');
        $endDate = $request->get('end_date') ?? date('Y-m-d');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');

        $data = $this->getFilteredData($type, $startDate, $endDate, $status);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.print', compact('data', 'type', 'startDate', 'endDate', 'status'));
        
        // Option 1: Download
        return $pdf->download('laporan-' . $type . '-' . date('YmdHis') . '.pdf');
        
        // Option 2: Stream (preview in browser)
        // return $pdf->stream('laporan.pdf');
    }

    private function getFilteredData($type, $startDate, $endDate, $status)
    {
        $data = [];

        if ($type == 'peminjaman') {
            $query = Peminjaman::with(['mahasiswa', 'ruangan', 'unit'])
                ->whereIn('status', ['menunggu', 'disetujui', 'digunakan', 'menyelesaikan', 'menunggu_validasi']);
                
            if ($startDate && $endDate) {
                $query->whereBetween('tanggalPinjam', [$startDate, $endDate]);
            }
            if ($status) {
                $query->where('status', $status);
            }
            $data = $query->orderBy('tanggalPinjam', 'desc')->get();

        } elseif ($type == 'riwayat') {
            $query = Peminjaman::with(['mahasiswa', 'ruangan', 'unit', 'pengembalian'])
                ->whereIn('status', ['selesai', 'ditolak']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggalPinjam', [$startDate, $endDate]);
            }
            if ($status) {
                $query->where('status', $status);
            }
            $data = $query->orderBy('tanggalPinjam', 'desc')->get();

        } elseif ($type == 'feedback') {
            $query = Feedback::with(['peminjaman.mahasiswa', 'peminjaman.ruangan', 'peminjaman.unit']);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            $data = $query->orderBy('created_at', 'desc')->get();
        }

        return $data;
    }
}
