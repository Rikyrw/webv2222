<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPenarikan;
use Illuminate\Http\Request;

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
            if (!$filterError) {
                $query = TransaksiPenarikan::where('id_nasabah', (int) $user_id)
                    ->orderByDesc('tanggal_pengajuan');

                if ($hasDateFilter) {
                    $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
                } else {
                    $query->where(function ($builder) {
                        $builder->whereNull('status')
                            ->orWhereIn('status', ['menunggu', 'pending']);
                    });
                }

                $hist = $query->get([
                    'id_penarikan',
                    'jenis_penukaran',
                    'nominal',
                    'status',
                    'tanggal_pengajuan',
                    'deskripsi',
                ])->map(fn (TransaksiPenarikan $row): array => [
                    'type' => 'penarikan',
                    'id' => $row->id_penarikan,
                    'service' => $row->jenis_penukaran ?: 'E-money',
                    'amount' => (float) $row->nominal,
                    'status' => $row->status ?: 'menunggu',
                    'deskripsi' => $row->deskripsi ?: '',
                    'created_at' => optional($row->tanggal_pengajuan)->toDateString(),
                ])->all();
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
