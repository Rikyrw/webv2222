<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiSetor;
use App\Models\TransaksiPenarikan;
use App\Models\DetailSetor;
use App\Models\Nasabah;
use Illuminate\Support\Facades\DB;
use DateTime;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'laporan';
        $pageTitle = 'Laporan';
        
        // Get period parameter (default: month)
        $period = $request->get('periode', 'month');
        
        // Determine date range
        $now = new DateTime();
        switch ($period) {
            case 'today':
                $start = (new DateTime('today'))->format('Y-m-d');
                $end = $now->format('Y-m-d');
                break;
            case 'week':
                $start = (new DateTime('monday this week'))->format('Y-m-d');
                $end = $now->format('Y-m-d');
                break;
            case 'year':
                $start = (new DateTime($now->format('Y') . '-01-01'))->format('Y-m-d');
                $end = $now->format('Y-m-d');
                break;
            case 'month':
            default:
                $start = (new DateTime($now->format('Y-m-01')))->format('Y-m-d');
                $end = $now->format('Y-m-d');
        }
        
        // Initialize default values
        $totalSetoran = 0;
        $totalSetoranCount = 0;
        $totalPenarikan = 0;
        $saldoAkhir = 0;
        $composition = [];
        $topNasabah = [];
        $databaseError = null;
        $canViewFinanceReport = session('admin_role') === 'superadmin';

        try {
            // Get financial data from the configured Laravel database connection.
            if ($canViewFinanceReport) {
                $totalSetoran = TransaksiSetor::where('status', 'selesai')
                    ->whereBetween('tanggal_setor', [$start, $end])
                    ->sum('total_nilai') ?? 0;
                
                $totalSetoranCount = TransaksiSetor::where('status', 'selesai')
                    ->whereBetween('tanggal_setor', [$start, $end])
                    ->count();
                
                // Total penarikan/withdrawal
                $totalPenarikan = TransaksiPenarikan::where('status', 'selesai')
                    ->whereBetween('tanggal_proses', [$start, $end])
                    ->sum('nominal') ?? 0;
                
                // Total saldo nasabah aktif
                $saldoAkhir = Nasabah::where('status', 'aktif')->sum('saldo') ?? 0;
            }
            
            // Waste composition by type (for the selected period)
            $compositionData = DetailSetor::join('transaksi_setor', 'detail_setor.id_transaksi_setor', '=', 'transaksi_setor.id_transaksi_setor')
                ->join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('jenis_sampah.nama_jenis', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
                ->groupBy('jenis_sampah.id_jenis_sampah', 'jenis_sampah.nama_jenis')
                ->orderBy('total_berat', 'desc')
                ->get();
            
            $composition = [];
            foreach ($compositionData as $item) {
                $composition[$item->nama_jenis] = (float)$item->total_berat;
            }
            
            // Top nasabah by total weight
            $topNasabahData = Nasabah::join('transaksi_setor', 'nasabah.id_nasabah', '=', 'transaksi_setor.id_nasabah')
                ->join('detail_setor', 'transaksi_setor.id_transaksi_setor', '=', 'detail_setor.id_transaksi_setor')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('nasabah.id_nasabah as id', 'nasabah.nama_lengkap as nama', DB::raw('SUM(detail_setor.berat_kg) as berat'))
                ->groupBy('nasabah.id_nasabah', 'nasabah.nama_lengkap')
                ->orderBy('berat', 'desc')
                ->limit(5)
                ->get()
                ->toArray();
            
            $topNasabah = [];
            foreach ($topNasabahData as $item) {
                $topNasabah[] = [
                    'id' => $item['id'],
                    'nama' => $item['nama'],
                    'berat' => (float)$item['berat']
                ];
            }
            
        } catch (\Exception $e) {
            \Log::error('Laporan Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil data dari database. Periksa koneksi internet.';
        }
        
        return view('admin.laporan', compact(
            'activePage',
            'pageTitle',
            'period',
            'totalSetoran',
            'totalSetoranCount',
            'totalPenarikan',
            'saldoAkhir',
            'composition',
            'topNasabah',
            'databaseError',
            'canViewFinanceReport'
        ));
    }

    public function excelKeuangan(Request $request)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403);
        }

        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $totalSetoran = TransaksiSetor::where('status', 'selesai')
                ->whereBetween('tanggal_setor', [$start, $end])
                ->sum('total_nilai') ?? 0;

            $totalSetoranCount = TransaksiSetor::where('status', 'selesai')
                ->whereBetween('tanggal_setor', [$start, $end])
                ->count();
            
            $totalPenarikan = TransaksiPenarikan::where('status', 'selesai')
                ->whereBetween('tanggal_proses', [$start, $end])
                ->sum('nominal') ?? 0;

            $saldoAkhir = Nasabah::where('status', 'aktif')->sum('saldo') ?? 0;

            $html = view('admin.laporan-keuangan-excel', [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'totalSetoran' => $totalSetoran,
                'totalSetoranCount' => $totalSetoranCount,
                'totalPenarikan' => $totalPenarikan,
                'saldoAkhir' => $saldoAkhir,
                'currentDate' => now()->format('d F Y H:i')
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=laporan_keuangan_' . now()->format('Y-m-d') . '.xls',
            ]);
        } catch (\Exception $e) {
            \Log::error('Export Excel Keuangan Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }

    public function pdfKeuangan(Request $request)
    {
        if (session('admin_role') !== 'superadmin') {
            abort(403);
        }

        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $totalSetoran = TransaksiSetor::where('status', 'selesai')
                ->whereBetween('tanggal_setor', [$start, $end])
                ->sum('total_nilai') ?? 0;

            $totalSetoranCount = TransaksiSetor::where('status', 'selesai')
                ->whereBetween('tanggal_setor', [$start, $end])
                ->count();
            
            $totalPenarikan = TransaksiPenarikan::where('status', 'selesai')
                ->whereBetween('tanggal_proses', [$start, $end])
                ->sum('nominal') ?? 0;

            $saldoAkhir = Nasabah::where('status', 'aktif')->sum('saldo') ?? 0;
            
            $data = [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'totalSetoran' => $totalSetoran,
                'totalSetoranCount' => $totalSetoranCount,
                'totalPenarikan' => $totalPenarikan,
                'saldoAkhir' => $saldoAkhir,
                'currentDate' => now()->format('d F Y H:i')
            ];

            return Pdf::loadView('admin.laporan-keuangan-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->stream('laporan_keuangan_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF Keuangan Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }

    public function excelSampah(Request $request)
    {
        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $sampahData = DetailSetor::join('transaksi_setor', 'detail_setor.id_transaksi_setor', '=', 'transaksi_setor.id_transaksi_setor')
                ->join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('jenis_sampah.nama_jenis', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
                ->groupBy('jenis_sampah.id_jenis_sampah', 'jenis_sampah.nama_jenis')
                ->orderBy('total_berat', 'desc')
                ->get();

            $composition = [];
            foreach ($sampahData as $item) {
                $composition[$item->nama_jenis] = (float) $item->total_berat;
            }

            $html = view('admin.laporan-sampah-excel', [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'composition' => $composition,
                'currentDate' => now()->format('d F Y H:i')
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=laporan_sampah_' . now()->format('Y-m-d') . '.xls',
            ]);
        } catch (\Exception $e) {
            \Log::error('Export Excel Sampah Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }

    public function pdfSampah(Request $request)
    {
        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $sampahData = DetailSetor::join('transaksi_setor', 'detail_setor.id_transaksi_setor', '=', 'transaksi_setor.id_transaksi_setor')
                ->join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('jenis_sampah.nama_jenis', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
                ->groupBy('jenis_sampah.id_jenis_sampah', 'jenis_sampah.nama_jenis')
                ->orderBy('total_berat', 'desc')
                ->get();

            $composition = [];
            foreach ($sampahData as $item) {
                $composition[$item->nama_jenis] = (float) $item->total_berat;
            }

            $data = [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'composition' => $composition,
                'currentDate' => now()->format('d F Y H:i')
            ];

            return Pdf::loadView('admin.laporan-sampah-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->stream('laporan_sampah_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF Sampah Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }

    public function excelNasabah(Request $request)
    {
        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $nasabahData = Nasabah::join('transaksi_setor', 'nasabah.id_nasabah', '=', 'transaksi_setor.id_nasabah')
                ->join('detail_setor', 'transaksi_setor.id_transaksi_setor', '=', 'detail_setor.id_transaksi_setor')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('nasabah.id_nasabah', 'nasabah.nama_lengkap', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
                ->groupBy('nasabah.id_nasabah', 'nasabah.nama_lengkap')
                ->orderBy('total_berat', 'desc')
                ->limit(10)
                ->get();

            $topNasabah = $nasabahData->map(function ($item) {
                return [
                    'nama' => $item->nama_lengkap,
                    'berat' => (float) $item->total_berat,
                ];
            })->toArray();

            $html = view('admin.laporan-nasabah-excel', [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'topNasabah' => $topNasabah,
                'currentDate' => now()->format('d F Y H:i')
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=laporan_nasabah_' . now()->format('Y-m-d') . '.xls',
            ]);
        } catch (\Exception $e) {
            \Log::error('Export Excel Nasabah Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }

    public function pdfNasabah(Request $request)
    {
        try {
            $period = $request->get('periode', 'month');
            
            // Get date range
            $now = new DateTime();
            switch ($period) {
                case 'today':
                    $start = date('Y-m-d');
                    $end = date('Y-m-d');
                    $periodLabel = 'Hari Ini';
                    break;
                case 'week':
                    $start = (new DateTime('monday this week'))->format('Y-m-d');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Minggu Ini';
                    break;
                case 'year':
                    $start = $now->format('Y') . '-01-01';
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Tahun ' . $now->format('Y');
                    break;
                default:
                    $start = $now->format('Y-m-01');
                    $end = $now->format('Y-m-d');
                    $periodLabel = 'Bulan ' . $now->format('F Y');
            }
            
            $nasabahData = Nasabah::join('transaksi_setor', 'nasabah.id_nasabah', '=', 'transaksi_setor.id_nasabah')
                ->join('detail_setor', 'transaksi_setor.id_transaksi_setor', '=', 'detail_setor.id_transaksi_setor')
                ->whereBetween('transaksi_setor.tanggal_setor', [$start, $end])
                ->where('transaksi_setor.status', 'selesai')
                ->select('nasabah.id_nasabah', 'nasabah.nama_lengkap', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
                ->groupBy('nasabah.id_nasabah', 'nasabah.nama_lengkap')
                ->orderBy('total_berat', 'desc')
                ->limit(10)
                ->get();

            $topNasabah = $nasabahData->map(function ($item) {
                return [
                    'nama' => $item->nama_lengkap,
                    'berat' => (float) $item->total_berat,
                ];
            })->toArray();

            $data = [
                'periodLabel' => $periodLabel,
                'start' => $start,
                'end' => $end,
                'topNasabah' => $topNasabah,
                'currentDate' => now()->format('d F Y H:i')
            ];

            return Pdf::loadView('admin.laporan-nasabah-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->stream('laporan_nasabah_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF Nasabah Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat file. ' . $e->getMessage()], 500);
        }
    }
}
