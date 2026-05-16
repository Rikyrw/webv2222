<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahRiwayatSetorController extends Controller
{
    public function index(Request $request)
    {
        $id_nasabah = session('id_nasabah') ?? 1;
        $user_name = session('nama_nasabah') ?? 'Guest User';

        $activePage = 'riwayat-setor';
        $transactions = [];
        $databaseError = null;
        $filterError = null;
        $startDate = $request->get('tanggal_mulai');
        $endDate = $request->get('tanggal_akhir');
        $hasDateFilter = !empty($startDate) && !empty($endDate);

        try {
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

            if (!$filterError) {
                $transactions = $this->fetchTransactionsFromSupabase(
                    $id_nasabah,
                    $hasDateFilter ? $startDate : null,
                    $hasDateFilter ? $endDate : null,
                    $hasDateFilter
                );
            }

        } catch (\Exception $e) {
            \Log::error('NasabahRiwayatSetorController Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil data transaksi. Periksa koneksi internet Anda.';
            $transactions = [];
        }

        return view('nasabah.riwayat_setor', compact(
            'activePage',
            'user_name',
            'transactions',
            'databaseError',
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

    private function fetchTransactionsFromSupabase(int $idNasabah, ?string $startDate, ?string $endDate, bool $hasDateFilter): array
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');
        if (!$supabaseUrl || !$supabaseKey) {
            return [];
        }

        $query = '/rest/v1/transaksi_setor?select=' . urlencode(
            'id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,status,' .
            'detail_setor(id_detail_setor,berat_kg,subtotal,harga_kg,status_item,catatan_admin,jenis_sampah(nama_jenis,harga_per_kg))'
        );

        $filters = [];
        $filters[] = 'id_nasabah=eq.' . intval($idNasabah);

        if ($hasDateFilter && $startDate && $endDate) {
            $filters[] = 'tanggal_setor=gte.' . $startDate;
            $filters[] = 'tanggal_setor=lte.' . $endDate;
        } else {
            $filters[] = 'or=(status.is.null,status.eq.menunggu,status.eq.pending)';
        }

        $filters[] = 'order=tanggal_setor.desc';
        $url = $supabaseUrl . $query . '&' . implode('&', $filters);

        $resp = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ])->get($url);

        if (!$resp->successful()) {
            return [];
        }

        $rows = $resp->json();
        if (!is_array($rows)) {
            return [];
        }

        $transactions = [];
        foreach ($rows as $row) {
            $detailSetor = $row['detail_setor'] ?? [];
            if (!is_array($detailSetor) || count($detailSetor) === 0) {
                continue;
            }

            $jenisList = [];
            $totalBerat = 0;
            $totalNilai = 0;
            $rejectedNotes = [];

            foreach ($detailSetor as $detail) {
                $jenis = $detail['jenis_sampah'] ?? null;
                $namaJenis = is_array($jenis) ? ($jenis['nama_jenis'] ?? 'N/A') : 'N/A';
                if (!in_array($namaJenis, $jenisList, true)) {
                    $jenisList[] = $namaJenis;
                }

                $berat = (float) ($detail['berat_kg'] ?? 0);
                $subtotal = (float) ($detail['subtotal'] ?? 0);
                $totalBerat += $berat;
                $totalNilai += $subtotal;

                $statusItem = strtolower(trim((string) ($detail['status_item'] ?? '')));
                if (in_array($statusItem, ['rejected', 'ditolak'], true)) {
                    $catatan = trim((string) ($detail['catatan_admin'] ?? ''));
                    $label = $namaJenis;
                    if ($catatan !== '') {
                        $label .= ' (' . $catatan . ')';
                    }
                    $rejectedNotes[] = $label;
                }
            }

            $transactions[] = [
                'id_transaksi' => $row['id_transaksi_setor'] ?? null,
                'nama_jenis' => implode(', ', $jenisList),
                'berat_kg' => $totalBerat,
                'subtotal' => $totalNilai,
                'tanggal_setor' => $row['tanggal_setor'] ?? null,
                'status' => $row['status'] ?? 'menunggu',
                'rejected_notes' => implode('; ', $rejectedNotes),
            ];
        }

        return $transactions;
    }
}
