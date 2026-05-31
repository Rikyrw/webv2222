<?php

namespace App\Http\Controllers;

use App\Models\TransaksiSetor;
use Illuminate\Http\Request;

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
                $transactions = $this->fetchTransactions(
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

    private function fetchTransactions(int $idNasabah, ?string $startDate, ?string $endDate, bool $hasDateFilter): array
    {
        $query = TransaksiSetor::with('detailSetor.sampah')
            ->where('id_nasabah', $idNasabah)
            ->orderByDesc('tanggal_setor');

        if ($hasDateFilter && $startDate && $endDate) {
            $query->whereBetween('tanggal_setor', [$startDate, $endDate]);
        } else {
            $query->where(function ($builder) {
                $builder->whereNull('status')
                    ->orWhereIn('status', ['menunggu', 'pending']);
            });
        }

        $transactions = [];
        foreach ($query->get() as $row) {
            if ($row->detailSetor->isEmpty()) {
                continue;
            }

            $jenisList = [];
            $totalBerat = 0;
            $totalNilai = 0;
            $rejectedNotes = [];

            foreach ($row->detailSetor as $detail) {
                $namaJenis = $detail->sampah?->nama_jenis ?? 'N/A';
                if (!in_array($namaJenis, $jenisList, true)) {
                    $jenisList[] = $namaJenis;
                }

                $berat = (float) $detail->berat_kg;
                $subtotal = (float) $detail->subtotal;
                $totalBerat += $berat;
                $totalNilai += $subtotal;

                $statusItem = strtolower(trim((string) $detail->status_item));
                if (in_array($statusItem, ['rejected', 'ditolak'], true)) {
                    $catatan = trim((string) $detail->catatan_admin);
                    $label = $namaJenis;
                    if ($catatan !== '') {
                        $label .= ' (' . $catatan . ')';
                    }
                    $rejectedNotes[] = $label;
                }
            }

            $transactions[] = [
                'id_transaksi' => $row->id_transaksi_setor,
                'nama_jenis' => implode(', ', $jenisList),
                'berat_kg' => $totalBerat,
                'subtotal' => $totalNilai,
                'tanggal_setor' => $row->tanggal_setor?->toDateString(),
                'status' => $this->normalizeStatus($row->status ?? null),
                'rejected_notes' => implode('; ', $rejectedNotes),
            ];
        }

        return $transactions;
    }

    private function normalizeStatus(mixed $status): string
    {
        $value = strtolower(trim((string) $status));

        if ($value === '' || in_array($value, ['menunggu', 'pending', 'diproses', 'process'], true)) {
            return 'pending';
        }

        if (in_array($value, ['selesai', 'success', 'approved', 'berhasil', 'sukses'], true)) {
            return 'selesai';
        }

        if (in_array($value, ['ditolak', 'rejected', 'reject', 'failed', 'gagal', 'cancelled', 'canceled'], true)) {
            return 'ditolak';
        }

        return $value;
    }
}
