<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

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
                $start = (new DateTime('today'))->format('Y-m-d 00:00:00');
                $end = $now->format('Y-m-d H:i:s');
                break;
            case 'week':
                $start = (new DateTime('monday this week'))->format('Y-m-d 00:00:00');
                $end = $now->format('Y-m-d H:i:s');
                break;
            case 'year':
                $start = (new DateTime($now->format('Y') . '-01-01'))->format('Y-m-d 00:00:00');
                $end = $now->format('Y-m-d H:i:s');
                break;
            case 'month':
            default:
                $start = (new DateTime($now->format('Y-m-01')))->format('Y-m-d 00:00:00');
                $end = $now->format('Y-m-d H:i:s');
        }
        
        // Dummy financial data (from Supabase in real app)
        $totalSetoran = 45250000.00; // Rp 45.25 juta (setor sampah)
        $totalSetoranCount = 142;
        $totalPenarikan = 28500000.00; // Rp 28.5 juta (tarik tunai)
        $saldoAkhir = 125750000.00; // Rp 125.75 juta (total saldo nasabah)
        
        // Dummy waste composition data (last 30 days)
        $composition = [
            'Plastik' => 850.5,
            'Kertas' => 620.3,
            'Kaca' => 145.2,
            'Logam' => 89.8,
            'Organik' => 1250.7,
        ];
        
        // Dummy top nasabah
        $topNasabah = [
            ['id' => 1, 'nama' => 'Budi Santoso', 'berat' => 145.5],
            ['id' => 2, 'nama' => 'Siti Nurhaliza', 'berat' => 128.3],
            ['id' => 3, 'nama' => 'Ahmad Wijaya', 'berat' => 112.7],
            ['id' => 4, 'nama' => 'Dewi Lestari', 'berat' => 98.4],
            ['id' => 5, 'nama' => 'Rudi Gunawan', 'berat' => 87.2],
        ];
        
        return view('admin.laporan', compact(
            'activePage',
            'pageTitle',
            'period',
            'totalSetoran',
            'totalSetoranCount',
            'totalPenarikan',
            'saldoAkhir',
            'composition',
            'topNasabah'
        ));
    }

    public function excelKeuangan(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement Excel export for financial data
        return response()->json(['message' => 'Excel export for keuangan - period: ' . $period]);
    }

    public function pdfKeuangan(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement PDF export for financial data
        return response()->json(['message' => 'PDF export for keuangan - period: ' . $period]);
    }

    public function excelSampah(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement Excel export for waste data
        return response()->json(['message' => 'Excel export for sampah - period: ' . $period]);
    }

    public function pdfSampah(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement PDF export for waste data
        return response()->json(['message' => 'PDF export for sampah - period: ' . $period]);
    }

    public function excelNasabah(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement Excel export for nasabah data
        return response()->json(['message' => 'Excel export for nasabah - period: ' . $period]);
    }

    public function pdfNasabah(Request $request)
    {
        $period = $request->get('periode', 'month');
        // TODO: Implement PDF export for nasabah data
        return response()->json(['message' => 'PDF export for nasabah - period: ' . $period]);
    }
}
