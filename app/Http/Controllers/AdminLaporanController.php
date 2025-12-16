<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Feedback;
use App\Models\Ulasan;
use PDF; // Requires dompdf package, if not available we use window.print()
use App\Http\Controllers\AdminRankingUserController;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');
        $role = $request->get('role', 'all');
        $periode = $request->get('periode');

        $data = $this->getFilteredData($type, $startDate, $endDate, $status, $role, $periode);

        return view('admin.laporan.index', compact('data', 'type', 'startDate', 'endDate', 'status', 'role', 'periode'));
    }

    public function print(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');

        $role = $request->get('role', 'all');
        $data = $this->getFilteredData($type, $startDate, $endDate, $status, $role);

        return view('admin.laporan.print', compact('data', 'type', 'startDate', 'endDate', 'status'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');
        $type = $request->get('type', 'peminjaman');

        $role = $request->get('role', 'all');
        $data = $this->getFilteredData($type, $startDate, $endDate, $status, $role);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.print', compact('data', 'type', 'startDate', 'endDate', 'status'));
        
        // Option 1: Download
        return $pdf->download('laporan-' . $type . '-' . date('YmdHis') . '.pdf');
        
        // Option 2: Stream (preview in browser)
        // return $pdf->stream('laporan.pdf');
    }

    private function getFilteredData($type, $startDate, $endDate, $status, $role = 'all', $periode = null)
    {
        $data = [];

        // Convert Periode to Dates if Dates are empty
        if (!$startDate && $periode) {
            switch ($periode) {
                case 'harian': $startDate = now()->startOfDay(); $endDate = now()->endOfDay(); break;
                case 'mingguan': $startDate = now()->startOfWeek(); $endDate = now()->endOfWeek(); break;
                case 'bulanan': $startDate = now()->startOfMonth(); $endDate = now()->endOfMonth(); break;
                case 'semester': $startDate = now()->subMonths(6); $endDate = now(); break;
                case 'tahunan': $startDate = now()->startOfYear(); $endDate = now()->endOfYear(); break;
            }
        }

        if ($type == 'peminjaman') {
            $query = Peminjaman::with(['mahasiswa', 'ruangan', 'unit'])
                ->whereIn('status', ['menunggu', 'disetujui', 'digunakan', 'menyelesaikan', 'menunggu_validasi']);
                
            if ($startDate && $endDate) {
                $query->whereBetween('tanggalPinjam', [$startDate, $endDate]);
            }
            if ($status) {
                $query->where('status', $status);
            }
            // Apply Role Filter
            if ($role && $role !== 'all') {
                $query->whereHas('mahasiswa', function($q) use ($role) {
                    $q->where('role', $role);
                });
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
            // Apply Role Filter
            if ($role && $role !== 'all') {
                $query->whereHas('mahasiswa', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
            $data = $query->orderBy('tanggalPinjam', 'desc')->get();

        } elseif ($type == 'feedback') {
            $query = Feedback::with(['peminjaman.mahasiswa', 'peminjaman.ruangan', 'peminjaman.unit']);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            // Apply Role Filter
            if ($role && $role !== 'all') {
                $query->whereHas('peminjaman.mahasiswa', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
            $data = $query->orderBy('created_at', 'desc')->get();

        } elseif ($type == 'ulasan') {
            $query = Ulasan::with('user');
            // Ulasan filter usually by period, but here we can support date if user uses date fields
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            // Apply Role Filter
            if ($role && $role !== 'all') {
                $query->whereHas('user', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
            $data = $query->orderBy('created_at', 'desc')->get();

        } elseif ($type == 'ranking_approval') {
            // Ranking Preview (Pending Items only)
            // Note: Full SAW calculation is only in Export/Ranking Controller. 
            // Here we just show the candidate list.
            $query = Peminjaman::with(['mahasiswa', 'ruangan', 'unit'])
                ->where('status', 'pending');

            // Apply Role Filter
            if ($role && $role !== 'all') {
                $query->whereHas('mahasiswa', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }

            $data = $query->orderBy('created_at', 'asc')->get();

        } elseif ($type == 'ranking_user') {
            // Ranking User Preview (Borrowed Logic from AdminRankingUserController)
            // We use the public method calculateRankings to get the data
            
            // Handle Period to Date logic if simple date filters are used,
            // but AdminRankingUserController::calculateRankings accepts dates directly.
            // If user uses "Periode" dropdown, we might need to convert it here OR
            // ensure index.blade.php passes start/end date OR passes 'periode'.
            // For now, let's rely on Start/End Date inputs which are standard in Laporan Hub.
            // But user asked for "hari ini minggu ini", so we should handle 'periode' param if passed?
            // Actually getFilteredData doesn't take 'periode'.
            // Let's stick to start/end date which the view sends.
            
            $rankingController = new AdminRankingUserController();
            $result = $rankingController->calculateRankings($startDate, $endDate, $role);
            $data = $result['rankings'];
        }

        return $data;
    }
}
