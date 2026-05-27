<?php

namespace App\Http\Controllers;

use App\Models\DetailSetor;
use App\Models\FotoSetor;
use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use App\Models\TransaksiSetor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'transaksi';
        $pageTitle = 'Transaksi';
        $tab = $request->get('tab', 'setor');
        $databaseError = null;
        $historyStatus = $request->get('history_status', 'all');
        $historyDate = $request->get('history_date');
        $historyDate = is_string($historyDate) && $historyDate !== '' ? $historyDate : date('Y-m-d');

        try {
            $setorRequestsResult = $this->fetchSetorRequests(max(1, (int) $request->get('page_setor_req', 1)), 10);
            $historySetorResult = $this->fetchSetorHistory(max(1, (int) $request->get('page_setor', 1)), 5, $historyStatus, $historyDate);
            $penarikanRequestsResult = $this->fetchPenarikanRequests(max(1, (int) $request->get('page_penarikan_req', 1)), 5);
            $historyPenarikanResult = $this->fetchPenarikanHistory(max(1, (int) $request->get('page_penarikan_hist', 1)), 5, $historyStatus, $historyDate);

            $setorRequests = $setorRequestsResult['items'];
            $setorRequestsMeta = $setorRequestsResult['meta'];
            $historySetor = $historySetorResult['items'];
            $historySetorMeta = $historySetorResult['meta'];
            $penarikanRequests = $penarikanRequestsResult['items'];
            $penarikanRequestsMeta = $penarikanRequestsResult['meta'];
            $historyPenarikan = $historyPenarikanResult['items'];
            $historyPenarikanMeta = $historyPenarikanResult['meta'];
        } catch (\Exception $e) {
            \Log::error('Transaksi Database Error: '.$e->getMessage());
            $databaseError = 'Tidak dapat mengambil data transaksi. Periksa koneksi database.';
            $setorRequests = $historySetor = $penarikanRequests = $historyPenarikan = [];
            $setorRequestsMeta = $historySetorMeta = $penarikanRequestsMeta = $historyPenarikanMeta = $this->emptyMeta();
        }

        return view('admin.transaksi', compact(
            'activePage',
            'pageTitle',
            'tab',
            'setorRequests',
            'setorRequestsMeta',
            'penarikanRequests',
            'penarikanRequestsMeta',
            'historySetor',
            'historySetorMeta',
            'historyPenarikan',
            'historyPenarikanMeta',
            'historyStatus',
            'historyDate',
            'databaseError'
        ));
    }

    public function showSetorDetail(int $id)
    {
        $activePage = 'transaksi';
        $pageTitle = 'Detail Setor Sampah';
        $flash = session()->pull('flash_setor_detail');
        $databaseError = null;
        $transaksi = null;
        $detailItems = [];
        $fotoSetorItems = [];

        try {
            $transaksi = $this->fetchTransaksiSetor($id);
            $detailItems = $this->fetchDetailSetorItems($id);
            $fotoSetorItems = $this->fetchFotoSetorItems($id, $detailItems);
        } catch (\Exception $e) {
            \Log::error('Transaksi setor detail error: '.$e->getMessage());
            $databaseError = 'Tidak dapat mengambil detail transaksi. Periksa koneksi database.';
        }

        return view('admin.transaksi_setor_detail', compact(
            'activePage',
            'pageTitle',
            'flash',
            'transaksi',
            'detailItems',
            'fotoSetorItems',
            'databaseError'
        ));
    }

    public function updateSetorDetail(Request $request, int $id)
    {
        $decisions = $request->input('decisions', []);
        $notes = $request->input('notes', []);

        if (!is_array($decisions) || count($decisions) === 0) {
            return redirect()->back()->with('flash_setor_detail', 'Tidak ada item yang diproses.');
        }

        try {
            DB::transaction(function () use ($id, $decisions, $notes): void {
                $transaksi = TransaksiSetor::with(['nasabah', 'detailSetor'])->lockForUpdate()->find($id);
                if (!$transaksi) {
                    throw new \RuntimeException('Data transaksi tidak ditemukan.');
                }

                $detailItems = $transaksi->detailSetor;
                if ($detailItems->isEmpty()) {
                    throw new \RuntimeException('Detail setor tidak ditemukan.');
                }

                $approvedSum = 0;
                $approvedCount = 0;
                $rejectedCount = 0;

                foreach ($detailItems as $detail) {
                    $decision = $decisions[$detail->id_detail_setor] ?? null;
                    if ($decision === null) {
                        continue;
                    }

                    $statusItem = strtolower(trim((string) $decision)) === 'approve' ? 'approved' : 'rejected';
                    if ($statusItem === 'approved') {
                        $approvedCount++;
                        $approvedSum += (float) $detail->subtotal;
                    } else {
                        $rejectedCount++;
                    }

                    $detail->update([
                        'status_item' => $statusItem,
                        'catatan_admin' => isset($notes[$detail->id_detail_setor]) ? (string) $notes[$detail->id_detail_setor] : null,
                    ]);
                }

                $totalItems = $detailItems->count();
                $decidedCount = $approvedCount + $rejectedCount;
                $newStatus = 'menunggu';
                if ($decidedCount === $totalItems) {
                    $newStatus = $approvedCount === 0
                        ? 'ditolak'
                        : ($approvedCount < $totalItems ? 'sebagian' : 'selesai');
                }

                $currentStatus = $this->normalizeSetorStatus($transaksi->status ?? 'menunggu');
                $transaksi->update([
                    'status' => $newStatus,
                    'tanggal_proses' => date('Y-m-d'),
                    'total_nilai' => $approvedSum,
                    'id_admin' => session('admin_id'),
                ]);

                if ($decidedCount === $totalItems && in_array($currentStatus, ['menunggu', 'pending'], true) && $approvedSum > 0 && $transaksi->nasabah) {
                    $transaksi->nasabah->increment('saldo', $approvedSum);
                }
            });

            return redirect()->route('admin.transaksi', ['tab' => 'setor'])->with('flash_setor_detail', 'Permintaan setor berhasil diproses.');
        } catch (\Exception $e) {
            \Log::error('Update setor detail error: '.$e->getMessage());

            return redirect()->back()->with('flash_setor_detail', $e->getMessage() ?: 'Terjadi kesalahan saat memproses transaksi.');
        }
    }

    public function handlePenarikanAction(Request $request, int $id)
    {
        $action = strtolower(trim((string) $request->input('action', '')));
        $note = trim((string) $request->input('note', ''));

        if (!in_array($action, ['approve', 'reject'], true)) {
            return response()->json(['message' => 'Aksi tidak valid.'], 422);
        }

        try {
            DB::transaction(function () use ($id, $action, $note): void {
                $penarikan = TransaksiPenarikan::with('nasabah')->lockForUpdate()->find($id);
                if (!$penarikan) {
                    throw new \RuntimeException('Data penarikan tidak ditemukan.');
                }

                $status = strtolower(trim((string) ($penarikan->status ?? '')));
                if (!in_array($status, ['pending', 'menunggu', ''], true)) {
                    throw new \RuntimeException('Penarikan sudah diproses.');
                }

                if ($action === 'approve') {
                    if (!$penarikan->nasabah || (float) $penarikan->nasabah->saldo < (float) $penarikan->nominal) {
                        throw new \RuntimeException('Saldo nasabah tidak mencukupi.');
                    }

                    $penarikan->update([
                        'status' => 'approved',
                        'tanggal_proses' => date('Y-m-d'),
                        'id_admin' => session('admin_id'),
                    ]);
                    $penarikan->nasabah->decrement('saldo', (float) $penarikan->nominal);

                    return;
                }

                $deskripsi = trim((string) $penarikan->deskripsi);
                if ($note !== '') {
                    $label = 'Catatan admin: '.$note;
                    $deskripsi = $deskripsi !== '' ? $deskripsi.' | '.$label : $label;
                }

                $penarikan->update([
                    'status' => 'rejected',
                    'tanggal_proses' => date('Y-m-d'),
                    'deskripsi' => $deskripsi,
                    'id_admin' => session('admin_id'),
                ]);
            });

            return response()->json(['message' => 'Permintaan penarikan berhasil diproses.']);
        } catch (\Exception $e) {
            \Log::error('Handle penarikan action error: '.$e->getMessage());

            $status = str_contains($e->getMessage(), 'tidak mencukupi') ? 422 : (str_contains($e->getMessage(), 'sudah diproses') ? 409 : 500);

            return response()->json(['message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses penarikan.'], $status);
        }
    }

    private function fetchSetorRequests(int $page, int $perPage): array
    {
        $query = TransaksiSetor::with(['nasabah', 'detailSetor.sampah'])
            ->whereIn('status', ['menunggu', 'pending'])
            ->orderByDesc('tanggal_setor');

        return $this->paginateMapped($query, $page, $perPage, fn (TransaksiSetor $item): array => [
            'id_transaksi' => $item->id_transaksi_setor,
            'id_nasabah' => $item->id_nasabah,
            'nama_nasabah' => $item->nasabah?->nama_lengkap ?: '-',
            'total_berat' => $item->detailSetor->sum(fn ($detail) => (float) $detail->berat_kg),
            'total_nilai' => (float) $item->total_nilai,
            'jenis' => $this->jenisLabel($item),
            'tanggal_setor' => $item->tanggal_setor?->toDateString(),
            'status' => $this->normalizeSetorStatus($item->status ?? 'menunggu'),
        ]);
    }

    private function fetchSetorHistory(int $page, int $perPage, string $statusFilter, string $historyDate): array
    {
        $query = TransaksiSetor::with(['nasabah', 'detailSetor.sampah'])
            ->whereDate('tanggal_proses', $historyDate)
            ->orderByDesc('tanggal_proses')
            ->orderByDesc('id_transaksi_setor');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['sebagian', 'selesai', 'ditolak']);
        }

        return $this->paginateMapped($query, $page, $perPage, fn (TransaksiSetor $item): array => [
            'id_transaksi' => $item->id_transaksi_setor,
            'nama_nasabah' => $item->nasabah?->nama_lengkap ?: '-',
            'jenis' => $this->jenisLabel($item),
            'total_berat' => $item->detailSetor->sum(fn ($detail) => (float) $detail->berat_kg),
            'total_nilai' => (float) $item->total_nilai,
            'tanggal_setor' => $item->tanggal_setor?->toDateString(),
            'tanggal_proses' => $item->tanggal_proses?->toDateString(),
            'status' => $this->normalizeSetorStatus($item->status ?? 'menunggu'),
            'approved_items' => $this->detailHistoryItems($item, 'approved'),
            'rejected_items' => $this->detailHistoryItems($item, 'rejected'),
            'catatan_admin' => $item->detailSetor
                ->pluck('catatan_admin')
                ->filter(fn ($note) => trim((string) $note) !== '')
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    private function fetchPenarikanRequests(int $page, int $perPage): array
    {
        $query = TransaksiPenarikan::with('nasabah')
            ->whereIn('status', ['pending', 'menunggu'])
            ->orderByDesc('tanggal_pengajuan');

        return $this->paginateMapped($query, $page, $perPage, fn (TransaksiPenarikan $item): array => [
            'id_penukaran' => $item->id_penarikan,
            'id_nasabah' => $item->id_nasabah,
            'nama_nasabah' => $item->nasabah?->nama_lengkap ?: '-',
            'jenis_penukaran' => $item->jenis_penukaran ?: 'Transfer Bank',
            'nominal' => (float) $item->nominal,
            'deskripsi' => $item->deskripsi ?: 'Permintaan penarikan saldo',
            'tanggal_pengajuan' => $item->tanggal_pengajuan?->toDateString(),
            'status' => $this->normalizePenarikanStatus($item->status ?? 'menunggu'),
        ]);
    }

    private function fetchPenarikanHistory(int $page, int $perPage, string $statusFilter, string $historyDate): array
    {
        $query = TransaksiPenarikan::with('nasabah')
            ->whereDate('tanggal_proses', $historyDate)
            ->orderByDesc('tanggal_proses')
            ->orderByDesc('id_penarikan');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter === 'selesai' ? 'approved' : ($statusFilter === 'ditolak' ? 'rejected' : $statusFilter));
        } else {
            $query->whereNotIn('status', ['pending', 'menunggu']);
        }

        return $this->paginateMapped($query, $page, $perPage, fn (TransaksiPenarikan $item): array => [
            'id_penarikan' => $item->id_penarikan,
            'nama_nasabah' => $item->nasabah?->nama_lengkap ?: '-',
            'jenis_penukaran' => $item->jenis_penukaran ?: 'Transfer Bank',
            'nominal' => (float) $item->nominal,
            'deskripsi' => $item->deskripsi ?: 'Permintaan penarikan saldo',
            'tanggal_pengajuan' => $item->tanggal_pengajuan?->toDateString(),
            'tanggal_proses' => $item->tanggal_proses?->toDateString(),
            'status' => $this->normalizePenarikanStatus($item->status ?? 'menunggu'),
        ]);
    }

    private function fetchTransaksiSetor(int $id): ?array
    {
        $row = TransaksiSetor::with('nasabah')->find($id);
        if (!$row) {
            return null;
        }

        return [
            'id_transaksi_setor' => $row->id_transaksi_setor,
            'id_nasabah' => $row->id_nasabah,
            'nama_nasabah' => $row->nasabah?->nama_lengkap ?: '-',
            'saldo' => (float) ($row->nasabah?->saldo ?? 0),
            'total_nilai' => (float) $row->total_nilai,
            'tanggal_setor' => $row->tanggal_setor?->toDateString(),
            'tanggal_proses' => $row->tanggal_proses?->toDateString(),
            'status' => $this->normalizeSetorStatus($row->status ?? 'menunggu'),
        ];
    }

    private function fetchDetailSetorItems(int $transaksiId): array
    {
        return DetailSetor::with('sampah')
            ->where('id_transaksi_setor', $transaksiId)
            ->orderBy('id_detail_setor')
            ->get()
            ->map(fn (DetailSetor $item): array => [
                'id_detail_setor' => $item->id_detail_setor,
                'id_jenis' => $item->id_jenis,
                'nama_jenis' => $item->sampah?->nama_jenis ?: '-',
                'berat_kg' => (float) $item->berat_kg,
                'harga_kg' => (int) round((float) $item->harga_kg),
                'subtotal' => (int) round((float) $item->subtotal),
                'status_item' => $item->status_item ?: 'pending',
                'catatan_admin' => $item->catatan_admin ?: '',
            ])
            ->all();
    }

    private function fetchFotoSetorItems(int $transaksiId, array $detailItems): array
    {
        $detailsById = collect($detailItems)->keyBy('id_detail_setor');

        return FotoSetor::with('sampah')
            ->where('id_transaksi_setor', $transaksiId)
            ->orderBy('id_foto_setor')
            ->get()
            ->map(function (FotoSetor $item, int $index) use ($detailsById, $detailItems): ?array {
                $fotoUrl = trim((string) $item->foto_url);
                if ($fotoUrl === '') {
                    return null;
                }

                $detail = $item->id_detail_setor ? $detailsById->get($item->id_detail_setor) : ($detailItems[$index] ?? null);

                return [
                    'urutan' => $index + 1,
                    'foto_url' => $fotoUrl,
                    'nama_jenis' => is_array($detail) ? ($detail['nama_jenis'] ?? '-') : ($item->sampah?->nama_jenis ?: '-'),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function paginateMapped($query, int $page, int $perPage, callable $mapper): array
    {
        $offset = ($page - 1) * $perPage;
        $items = $query->offset($offset)->limit($perPage + 1)->get();
        $hasNext = $items->count() > $perPage;

        return [
            'items' => $items->take($perPage)->map($mapper)->all(),
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
                'offset' => $offset,
            ],
        ];
    }

    private function detailHistoryItems(TransaksiSetor $setor, string $status): array
    {
        return $setor->detailSetor
            ->filter(fn ($detail) => strtolower((string) $detail->status_item) === $status)
            ->map(function ($detail): string {
                $namaJenis = $detail->sampah?->nama_jenis ?: '-';

                return $namaJenis.' ('.number_format((float) $detail->berat_kg, 2, ',', '.').' kg, Rp '.number_format((float) $detail->subtotal, 0, ',', '.').')';
            })
            ->unique()
            ->values()
            ->all();
    }

    private function jenisLabel(TransaksiSetor $setor): string
    {
        $approvedCount = $setor->detailSetor->filter(fn ($detail) => strtolower((string) $detail->status_item) === 'approved')->count();
        $items = $setor->detailSetor
            ->filter(fn ($detail) => $approvedCount === 0 || strtolower((string) $detail->status_item) === 'approved')
            ->map(fn ($detail) => $detail->sampah?->nama_jenis)
            ->filter()
            ->unique()
            ->values();

        return $items->isEmpty() ? 'N/A' : $items->implode(', ');
    }

    private function normalizeSetorStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));
        if ($value === '' || $value === 'pending') {
            return 'menunggu';
        }

        if ($value === 'approved') {
            return 'selesai';
        }

        if ($value === 'rejected') {
            return 'ditolak';
        }

        return $value;
    }

    private function normalizePenarikanStatus(?string $status): string
    {
        $value = strtolower(trim((string) $status));
        if ($value === '' || $value === 'pending') {
            return 'menunggu';
        }

        if ($value === 'approved') {
            return 'selesai';
        }

        if ($value === 'rejected') {
            return 'ditolak';
        }

        return $value;
    }

    private function emptyMeta(): array
    {
        return [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
            'offset' => 0,
        ];
    }
}
