<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahTransaksiPPOBController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('id_nasabah') ?? 1;
        $user_name = session('nama_nasabah') ?? 'Guest User';
        $hist = [];
        $filterError = null;
        $startDate = $request->get('tanggal_mulai');
        $endDate = $request->get('tanggal_akhir');
        $hasDateFilter = !empty($startDate) && !empty($endDate);

        if ($hasDateFilter) {
            $diffDays = $this->getDateDiffDays($startDate, $endDate);
            if ($diffDays === null) {
                $filterError = 'Tanggal tidak valid.';
            } elseif ($diffDays < 1) {
                $filterError = 'Rentang pencarian minimal 1 hari.';
            } elseif ($diffDays > 30) {
                $filterError = 'Rentang pencarian maksimal 30 hari.';
            }
        }

        try {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_KEY');
            if ($supabaseUrl && $supabaseKey && !$filterError) {
                $filters = [];
                $filters[] = 'id_nasabah=eq.' . intval($user_id);
                if ($hasDateFilter) {
                    $filters[] = 'tanggal_pengajuan=gte.' . $startDate;
                    $filters[] = 'tanggal_pengajuan=lte.' . $endDate;
                } else {
                    $filters[] = 'or=(status.is.null,status.eq.menunggu,status.eq.pending)';
                }

                $filters[] = 'order=tanggal_pengajuan.desc';
                $response = Http::withHeaders([
                    'apikey' => $supabaseKey,
                    'Authorization' => 'Bearer ' . $supabaseKey,
                ])->get($supabaseUrl . '/rest/v1/penarikan_saldo?select=id_penarikan,jenis_penukaran,nominal,status,tanggal_pengajuan,deskripsi&' . implode('&', $filters));

                $rows = $response->json();
                if (is_array($rows)) {
                    $hist = array_map(function ($row) {
                        return [
                            'type' => 'penarikan',
                            'id' => $row['id_penarikan'] ?? null,
                            'service' => $row['jenis_penukaran'] ?? 'E-money',
                            'amount' => (float) ($row['nominal'] ?? 0),
                            'status' => $row['status'] ?? 'menunggu',
                            'deskripsi' => $row['deskripsi'] ?? '',
                            'created_at' => $row['tanggal_pengajuan'] ?? null,
                        ];
                    }, $rows);
                }
            }
        } catch (\Exception $e) {
            \Log::error('PPOB history fetch error: ' . $e->getMessage());
        }

        $activePage = 'transaksi';

        return view('nasabah.transaksi_ppob', compact(
            'activePage',
            'user_name',
            'hist',
            'filterError',
            'startDate',
            'endDate'
        ));
    }

    private function getDateDiffDays(?string $startDate, ?string $endDate): ?int
    {
        try {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            if ($end < $start) {
                return null;
            }
            return (int) $start->diff($end)->days + 1;
        } catch (\Exception $e) {
            return null;
        }
    }
}
