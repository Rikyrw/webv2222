<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\Sampah;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'dashboard';
        $pageTitle = 'Dashboard';
        
        // Get real data from database
        $nasabahCount = Nasabah::where('status', 'aktif')->count();
        
        // Top customers by saldo
        $topNasabah = Nasabah::where('status', 'aktif')
            ->orderBy('saldo', 'desc')
            ->limit(3)
            ->get(['nama_lengkap as nama_nasabah', 'saldo'])
            ->toArray();
        
        $totalSaldo = Nasabah::where('status', 'aktif')->sum('saldo');
        
        // Get waste data for today
        $today = date('Y-m-d');
        $totalSampahToday = DB::table('detail_setor')
            ->join('transaksi_setor', 'detail_setor.id_transaksi_setor', '=', 'transaksi_setor.id_transaksi_setor')
            ->where('transaksi_setor.tanggal_setor', $today)
            ->sum('detail_setor.berat_kg');
        
        $totalSampahToday = $totalSampahToday ?? 0;
        
        // Revenue this month
        $thisMonth = date('Y-m');
        $pendapatanThisMonth = TransaksiSetor::where('status', 'selesai')
            ->whereYear('tanggal_setor', date('Y'))
            ->whereMonth('tanggal_setor', date('m'))
            ->sum('total_nilai');
        
        $pendapatanThisMonth = $pendapatanThisMonth ?? 0;

        // Chart data - last 7 days
        $lineLabels = [];
        $lineData = [];
        for ($d = 6; $d >= 0; $d--) {
            $day = date('Y-m-d', strtotime("-{$d} days"));
            $lineLabels[] = date('d M', strtotime($day));
            
            $dailyTotal = TransaksiSetor::where('status', 'selesai')
                ->where('tanggal_setor', $day)
                ->sum('total_nilai');
            
            $lineData[] = $dailyTotal ? (int)$dailyTotal / 100000 : 0;
        }

        // Waste distribution pie chart
        $sampahDistribution = DB::table('detail_setor')
            ->join('jenis_sampah', 'detail_setor.id_jenis', '=', 'jenis_sampah.id_jenis_sampah')
            ->select('jenis_sampah.nama_jenis', DB::raw('SUM(detail_setor.berat_kg) as total_berat'))
            ->groupBy('jenis_sampah.id_jenis_sampah', 'jenis_sampah.nama_jenis')
            ->orderBy('total_berat', 'desc')
            ->limit(5)
            ->get();
        
        $pieLabels = $sampahDistribution->pluck('nama_jenis')->toArray();
        $pieData = $sampahDistribution->pluck('total_berat')->toArray();

        // If no data, use empty arrays
        if (empty($pieLabels)) {
            $pieLabels = ['Data Kosong'];
            $pieData = [0];
        }

        return view('admin.dashboard', compact(
            'activePage', 'pageTitle', 'nasabahCount', 'topNasabah', 'totalSaldo', 'totalSampahToday', 'pendapatanThisMonth', 'lineLabels', 'lineData', 'pieLabels', 'pieData'
        ));
    }
}
