<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'transaksi';
        $pageTitle = 'Transaksi';
        $tab = $request->get('tab', 'setor'); // Default tab: setor
        
        $setorRequests = [];
        $setorRequestsMeta = [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
        ];
        $penarikanRequests = [];
        $penarikanRequestsMeta = [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
        ];
        $historySetor = [];
        $historySetorMeta = [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
        ];
        $historyPenarikan = [];
        $historyPenarikanMeta = [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
        ];
        $databaseError = null;

        try {
            // Get setor requests from Supabase
            $pageSetorReq = max(1, (int) $request->get('page_setor_req', 1));
            $setorRequestsResult = $this->fetchSetorRequests($pageSetorReq, 10);
            $setorRequests = $setorRequestsResult['items'];
            $setorRequestsMeta = $setorRequestsResult['meta'];

            // Get setor history from Supabase
            $historyStatus = $request->get('history_status', 'all');
            $pageSetor = max(1, (int) $request->get('page_setor', 1));
            $historySetorResult = $this->fetchSetorHistory($pageSetor, 5, $historyStatus);
            $historySetor = $historySetorResult['items'];
            $historySetorMeta = $historySetorResult['meta'];

            // Get penarikan requests from database
            $pagePenarikanReq = max(1, (int) $request->get('page_penarikan_req', 1));
            $penarikanRequestsResult = $this->fetchPenarikanRequests($pagePenarikanReq, 5);
            $penarikanRequests = $penarikanRequestsResult['items'];
            $penarikanRequestsMeta = $penarikanRequestsResult['meta'];

            // Get penarikan history (today only)
            $pagePenarikanHist = max(1, (int) $request->get('page_penarikan_hist', 1));
            $historyPenarikanResult = $this->fetchPenarikanHistory($pagePenarikanHist, 5, $historyStatus);
            $historyPenarikan = $historyPenarikanResult['items'];
            $historyPenarikanMeta = $historyPenarikanResult['meta'];
        } catch (\Exception $e) {
            \Log::error('Transaksi Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil data transaksi. Periksa koneksi database.';
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

        try {
            $response = $this->supabaseRequest(
                'get',
                '/rest/v1/transaksi_setor?select=id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,tanggal_proses,status,nasabah(nama_lengkap,saldo)&id_transaksi_setor=eq.' . $id . '&limit=1',
                null,
                false
            );

            if ($response->successful()) {
                $items = $response->json();
                if (is_array($items) && count($items) > 0) {
                    $row = $items[0];
                    $transaksi = [
                        'id_transaksi_setor' => $row['id_transaksi_setor'] ?? $id,
                        'id_nasabah' => $row['id_nasabah'] ?? null,
                        'nama_nasabah' => isset($row['nasabah']['nama_lengkap']) ? $row['nasabah']['nama_lengkap'] : '-',
                        'saldo' => isset($row['nasabah']['saldo']) ? (float) $row['nasabah']['saldo'] : 0,
                        'total_nilai' => isset($row['total_nilai']) ? (float) $row['total_nilai'] : 0,
                        'tanggal_setor' => $row['tanggal_setor'] ?? null,
                        'tanggal_proses' => $row['tanggal_proses'] ?? null,
                        'status' => $this->normalizeSetorStatus($row['status'] ?? 'menunggu'),
                    ];
                }
            }

            $detailItems = $this->fetchDetailSetorItems($id);
        } catch (\Exception $e) {
            \Log::error('Transaksi setor detail error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat mengambil detail transaksi. Periksa koneksi database.';
        }

        return view('admin.transaksi_setor_detail', compact(
            'activePage',
            'pageTitle',
            'flash',
            'transaksi',
            'detailItems',
            'databaseError'
        ));
    }

    public function updateSetorDetail(Request $request, int $id)
    {
        $decisions = $request->input('decisions', []);
        $notes = $request->input('notes', []);

        if (!$this->supportsDetailStatusColumns()) {
            return redirect()->back()->with('flash_setor_detail', 'Kolom status_item/catatan_admin belum ada di detail_setor. Tambahkan kolom tersebut terlebih dahulu.');
        }

        if (!is_array($decisions) || count($decisions) === 0) {
            return redirect()->back()->with('flash_setor_detail', 'Tidak ada item yang diproses.');
        }

        try {
            $transaksi = $this->fetchTransaksiSetor($id);
            if (!$transaksi) {
                return redirect()->back()->with('flash_setor_detail', 'Data transaksi tidak ditemukan.');
            }

            $detailItems = $this->fetchDetailSetorItems($id);
            if (count($detailItems) === 0) {
                return redirect()->back()->with('flash_setor_detail', 'Detail setor tidak ditemukan.');
            }

            $detailMap = [];
            foreach ($detailItems as $item) {
                $detailMap[$item['id_detail_setor']] = $item;
            }

            $approvedSum = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            foreach ($detailMap as $detailId => $item) {
                $decision = $decisions[$detailId] ?? null;
                if ($decision === null) {
                    continue;
                }

                $normalized = strtolower(trim((string) $decision));
                $statusItem = $normalized === 'approve' ? 'approved' : 'rejected';

                if ($statusItem === 'approved') {
                    $approvedCount++;
                    $approvedSum += (float) $item['subtotal'];
                } else {
                    $rejectedCount++;
                }

                $payload = [
                    'status_item' => $statusItem,
                    'catatan_admin' => isset($notes[$detailId]) ? (string) $notes[$detailId] : null,
                ];

                $this->supabaseRequest(
                    'patch',
                    '/rest/v1/detail_setor?id_detail_setor=eq.' . intval($detailId),
                    $payload,
                    true
                );
            }

            $totalItems = count($detailMap);
            $decidedCount = $approvedCount + $rejectedCount;
            $newStatus = 'menunggu';
            if ($decidedCount === $totalItems) {
                if ($approvedCount === 0) {
                    $newStatus = 'ditolak';
                } elseif ($approvedCount < $totalItems) {
                    $newStatus = 'sebagian';
                } else {
                    $newStatus = 'selesai';
                }
            }

            $updateTransaksi = [
                'status' => $newStatus,
                'tanggal_proses' => date('Y-m-d'),
                'total_nilai' => $approvedSum,
            ];

            $this->supabaseRequest(
                'patch',
                '/rest/v1/transaksi_setor?id_transaksi_setor=eq.' . intval($id),
                $updateTransaksi,
                true
            );

            $currentStatus = $this->normalizeSetorStatus($transaksi['status'] ?? 'menunggu');
            if ($decidedCount === $totalItems && in_array($currentStatus, ['menunggu', 'pending'], true) && $approvedSum > 0) {
                $newSaldo = (float) $transaksi['saldo'] + $approvedSum;
                $this->supabaseRequest(
                    'patch',
                    '/rest/v1/nasabah?id_nasabah=eq.' . intval($transaksi['id_nasabah']),
                    ['saldo' => $newSaldo],
                    true
                );
            }

            return redirect()->route('admin.transaksi', ['tab' => 'setor'])->with('flash_setor_detail', 'Permintaan setor berhasil diproses.');
        } catch (\Exception $e) {
            \Log::error('Update setor detail error: ' . $e->getMessage());
            return redirect()->back()->with('flash_setor_detail', 'Terjadi kesalahan saat memproses transaksi.');
        }
    }

    private function fetchSetorRequests(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $limit = $perPage + 1;
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/transaksi_setor?select=id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,status,nasabah(nama_lengkap),detail_setor(berat_kg,status_item,jenis_sampah(nama_jenis))&status=in.(menunggu,pending)&order=tanggal_setor.asc&limit=' . $limit . '&offset=' . $offset,
            null,
            false
        );

        if (!$response->successful()) {
            $response = $this->supabaseRequest(
                'get',
                '/rest/v1/transaksi_setor?select=id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,status,nasabah(nama_lengkap),detail_setor(berat_kg,jenis_sampah(nama_jenis))&status=in.(menunggu,pending)&order=tanggal_setor.asc&limit=' . $limit . '&offset=' . $offset,
                null,
                false
            );
        }
        if (!$response->successful()) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                ],
            ];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                ],
            ];
        }

        $hasNext = count($items) > $perPage;
        if ($hasNext) {
            $items = array_slice($items, 0, $perPage);
        }

        $transaksiIds = [];
        $nasabahIds = [];
        foreach ($items as $item) {
            if (isset($item['id_transaksi_setor'])) {
                $transaksiIds[] = (int) $item['id_transaksi_setor'];
            }
            if (isset($item['id_nasabah'])) {
                $nasabahIds[] = (int) $item['id_nasabah'];
            }
        }

        $nasabahMap = $this->fetchNasabahMap($nasabahIds);
        $detailMap = $this->fetchDetailSetorSummary($transaksiIds);

        $mapped = [];
        foreach ($items as $item) {
            $transaksiId = $item['id_transaksi_setor'] ?? null;
            $nasabahId = $item['id_nasabah'] ?? null;
            $nasabahName = isset($item['nasabah']['nama_lengkap']) ? $item['nasabah']['nama_lengkap'] : null;
            if (!$nasabahName && $nasabahId !== null && isset($nasabahMap[$nasabahId])) {
                $nasabahName = $nasabahMap[$nasabahId];
            }

            $totalBerat = 0;
            $jenisLabel = 'N/A';
            $detailItems = isset($item['detail_setor']) && is_array($item['detail_setor']) ? $item['detail_setor'] : [];
            $detailSummary = $transaksiId !== null && isset($detailMap[$transaksiId]) ? $detailMap[$transaksiId] : null;

            if (count($detailItems) > 0) {
                $jenisList = [];
                $approvedCount = 0;
                foreach ($detailItems as $detail) {
                    $berat = isset($detail['berat_kg']) ? (float) $detail['berat_kg'] : 0;
                    $totalBerat += $berat;

                    $statusItem = strtolower(trim((string) ($detail['status_item'] ?? 'pending')));
                    if ($statusItem === 'approved') {
                        $approvedCount++;
                    }

                    $namaJenis = isset($detail['jenis_sampah']['nama_jenis']) ? $detail['jenis_sampah']['nama_jenis'] : null;
                    if ($namaJenis) {
                        $jenisList[] = [
                            'nama' => $namaJenis,
                            'status' => $statusItem,
                        ];
                    }
                }

                $onlyApproved = $approvedCount > 0;
                $jenisFiltered = [];
                foreach ($jenisList as $jenisItem) {
                    if ($onlyApproved && ($jenisItem['status'] ?? '') !== 'approved') {
                        continue;
                    }
                    $jenisFiltered[] = $jenisItem['nama'] ?? '-';
                }
                $jenisFiltered = array_values(array_unique($jenisFiltered));
                $jenisLabel = count($jenisFiltered) > 0 ? implode(', ', $jenisFiltered) : 'N/A';
            } elseif ($detailSummary) {
                $totalBerat = $detailSummary['total_berat'] ?? 0;
                $jenisLabel = $detailSummary['jenis'] ?? 'N/A';
            }

            $mapped[] = [
                'id_transaksi' => $transaksiId,
                'id_nasabah' => $nasabahId,
                'nama_nasabah' => $nasabahName ?: '-',
                'total_berat' => $totalBerat,
                'total_nilai' => isset($item['total_nilai']) ? (float) $item['total_nilai'] : 0,
                'jenis' => $jenisLabel,
                'tanggal_setor' => $item['tanggal_setor'] ?? null,
                'status' => $this->normalizeSetorStatus($item['status'] ?? 'menunggu'),
            ];
        }

        return [
            'items' => $mapped,
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
            ],
        ];
    }

    private function fetchSetorHistory(int $page, int $perPage, string $statusFilter): array
    {
        $today = date('Y-m-d');
        $offset = ($page - 1) * $perPage;
        $limit = $perPage + 1;
        $statusClause = $statusFilter !== 'all'
            ? '&status=eq.' . urlencode($statusFilter)
            : '&status=in.(sebagian,selesai,ditolak)';
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/transaksi_setor?select=id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,tanggal_proses,status,nasabah(nama_lengkap)' . $statusClause . '&tanggal_proses=eq.' . $today . '&order=tanggal_proses.desc&limit=' . $limit . '&offset=' . $offset,
            null,
            false
        );

        if (!$response->successful()) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                ],
            ];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [
                'items' => [],
                'meta' => [
                    'page' => $page,
                    'has_next' => false,
                    'has_prev' => $page > 1,
                ],
            ];
        }

        $hasNext = count($items) > $perPage;
        if ($hasNext) {
            $items = array_slice($items, 0, $perPage);
        }

        $transaksiIds = [];
        foreach ($items as $item) {
            if (isset($item['id_transaksi_setor'])) {
                $transaksiIds[] = (int) $item['id_transaksi_setor'];
            }
        }

        $detailMap = $this->fetchDetailSetorSummary($transaksiIds);
        $detailHistoryMap = $this->fetchDetailSetorHistoryItems($transaksiIds);

        $mapped = [];
        foreach ($items as $item) {
            $transaksiId = $item['id_transaksi_setor'] ?? null;
            $detail = $transaksiId !== null && isset($detailMap[$transaksiId]) ? $detailMap[$transaksiId] : null;
            $detailHistory = $transaksiId !== null && isset($detailHistoryMap[$transaksiId]) ? $detailHistoryMap[$transaksiId] : [
                'approved_items' => [],
                'rejected_items' => [],
                'notes' => [],
            ];

            $mapped[] = [
                'id_transaksi' => $transaksiId,
                'nama_nasabah' => isset($item['nasabah']['nama_lengkap']) ? $item['nasabah']['nama_lengkap'] : '-',
                'jenis' => $detail['jenis'] ?? 'N/A',
                'total_berat' => $detail['total_berat'] ?? 0,
                'total_nilai' => isset($item['total_nilai']) ? (float) $item['total_nilai'] : 0,
                'tanggal_setor' => $item['tanggal_setor'] ?? null,
                'tanggal_proses' => $item['tanggal_proses'] ?? null,
                'status' => $this->normalizeSetorStatus($item['status'] ?? 'menunggu'),
                'approved_items' => $detailHistory['approved_items'],
                'rejected_items' => $detailHistory['rejected_items'],
                'catatan_admin' => $detailHistory['notes'],
            ];
        }

        return [
            'items' => $mapped,
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
            ],
        ];
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

    private function fetchNasabahMap(array $nasabahIds): array
    {
        $uniqueIds = array_values(array_unique(array_filter($nasabahIds)));
        if (count($uniqueIds) === 0) {
            return [];
        }

        $idList = implode(',', array_map('intval', $uniqueIds));
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/nasabah?select=id_nasabah,nama_lengkap,nama_nasabah&id_nasabah=in.(' . $idList . ')',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $map = [];
        foreach ($items as $item) {
            $id = $item['id_nasabah'] ?? null;
            if ($id === null) {
                continue;
            }
            $map[(int) $id] = $item['nama_lengkap'] ?? $item['nama_nasabah'] ?? '-';
        }

        return $map;
    }

    private function fetchPenarikanRequests(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $limit = $perPage + 1;

        $items = DB::table('penarikan_saldo')
            ->join('nasabah', 'penarikan_saldo.id_nasabah', '=', 'nasabah.id_nasabah')
            ->where('penarikan_saldo.status', 'pending')
            ->select(
                'penarikan_saldo.id_penarikan',
                'penarikan_saldo.id_nasabah',
                'nasabah.nama_lengkap as nama_nasabah',
                'penarikan_saldo.jenis_penukaran',
                'penarikan_saldo.nominal',
                'penarikan_saldo.deskripsi',
                'penarikan_saldo.tanggal_pengajuan',
                'penarikan_saldo.status'
            )
            ->orderBy('penarikan_saldo.tanggal_pengajuan', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $hasNext = $items->count() > $perPage;
        if ($hasNext) {
            $items = $items->slice(0, $perPage);
        }

        $mapped = $items->map(function ($item) {
            return [
                'id_penukaran' => $item->id_penarikan,
                'id_nasabah' => $item->id_nasabah,
                'nama_nasabah' => $item->nama_nasabah,
                'jenis_penukaran' => $item->jenis_penukaran ?? 'Transfer Bank',
                'nominal' => $item->nominal,
                'deskripsi' => $item->deskripsi ?? 'Permintaan penarikan saldo',
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'status' => $item->status,
            ];
        })->values()->toArray();

        return [
            'items' => $mapped,
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
            ],
        ];
    }

    private function fetchPenarikanHistory(int $page, int $perPage, string $statusFilter): array
    {
        $today = date('Y-m-d');
        $offset = ($page - 1) * $perPage;
        $limit = $perPage + 1;

        $query = DB::table('penarikan_saldo')
            ->join('nasabah', 'penarikan_saldo.id_nasabah', '=', 'nasabah.id_nasabah')
            ->whereNotIn('penarikan_saldo.status', ['pending', 'menunggu'])
            ->whereDate('penarikan_saldo.tanggal_proses', $today)
            ->select(
                'penarikan_saldo.id_penarikan',
                'nasabah.nama_lengkap as nama_nasabah',
                'penarikan_saldo.jenis_penukaran',
                'penarikan_saldo.nominal',
                'penarikan_saldo.deskripsi',
                'penarikan_saldo.tanggal_pengajuan',
                'penarikan_saldo.tanggal_proses',
                'penarikan_saldo.status'
            );

        if ($statusFilter !== 'all') {
            $query->where('penarikan_saldo.status', $statusFilter);
        }

        $items = $query->orderBy('penarikan_saldo.tanggal_proses', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $hasNext = $items->count() > $perPage;
        if ($hasNext) {
            $items = $items->slice(0, $perPage);
        }

        $mapped = $items->map(function ($item) {
            return [
                'id_penarikan' => $item->id_penarikan,
                'nama_nasabah' => $item->nama_nasabah,
                'jenis_penukaran' => $item->jenis_penukaran ?? 'Transfer Bank',
                'nominal' => $item->nominal,
                'deskripsi' => $item->deskripsi ?? 'Permintaan penarikan saldo',
                'tanggal_pengajuan' => $item->tanggal_pengajuan,
                'tanggal_proses' => $item->tanggal_proses,
                'status' => $item->status,
            ];
        })->values()->toArray();

        return [
            'items' => $mapped,
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
            ],
        ];
    }

    private function fetchDetailSetorSummary(array $transaksiIds): array
    {
        $uniqueIds = array_values(array_unique(array_filter($transaksiIds)));
        if (count($uniqueIds) === 0) {
            return [];
        }

        return $this->fetchDetailSetorByColumn('id_transaksi_setor', $uniqueIds);
    }

    private function fetchDetailSetorHistoryItems(array $transaksiIds): array
    {
        $uniqueIds = array_values(array_unique(array_filter($transaksiIds)));
        if (count($uniqueIds) === 0) {
            return [];
        }

        $idList = implode(',', array_map('intval', $uniqueIds));
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/detail_setor?select=id_transaksi_setor,berat_kg,subtotal,status_item,catatan_admin,jenis_sampah(nama_jenis)&id_transaksi_setor=in.(' . $idList . ')',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $map = [];
        foreach ($items as $item) {
            $txId = $item['id_transaksi_setor'] ?? null;
            if ($txId === null) {
                continue;
            }

            $txId = (int) $txId;
            if (!isset($map[$txId])) {
                $map[$txId] = [
                    'approved_items' => [],
                    'rejected_items' => [],
                    'notes' => [],
                ];
            }

            $statusItem = strtolower(trim((string) ($item['status_item'] ?? 'pending')));
            $namaJenis = isset($item['jenis_sampah']['nama_jenis']) ? $item['jenis_sampah']['nama_jenis'] : '-';
            $berat = isset($item['berat_kg']) ? (float) $item['berat_kg'] : 0;
            $subtotal = isset($item['subtotal']) ? (float) $item['subtotal'] : 0;
            $note = isset($item['catatan_admin']) ? trim((string) $item['catatan_admin']) : '';

            $label = $namaJenis . ' (' . number_format($berat, 2, ',', '.') . ' kg, Rp ' . number_format($subtotal, 0, ',', '.') . ')';

            if ($statusItem === 'approved') {
                $map[$txId]['approved_items'][] = $label;
            } elseif ($statusItem === 'rejected') {
                $map[$txId]['rejected_items'][] = $label;
            }

            if ($note !== '') {
                $map[$txId]['notes'][] = $note;
            }
        }

        foreach ($map as $txId => $data) {
            $map[$txId]['approved_items'] = array_values(array_unique($data['approved_items']));
            $map[$txId]['rejected_items'] = array_values(array_unique($data['rejected_items']));
            $map[$txId]['notes'] = array_values(array_unique($data['notes']));
        }

        return $map;
    }

    private function fetchTransaksiSetor(int $id): ?array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/transaksi_setor?select=id_transaksi_setor,id_nasabah,total_nilai,tanggal_setor,tanggal_proses,status,nasabah(nama_lengkap,saldo)&id_transaksi_setor=eq.' . $id . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return null;
        }

        $items = $response->json();
        if (!is_array($items) || count($items) === 0) {
            return null;
        }

        $row = $items[0];

        return [
            'id_transaksi_setor' => $row['id_transaksi_setor'] ?? $id,
            'id_nasabah' => $row['id_nasabah'] ?? null,
            'nama_nasabah' => isset($row['nasabah']['nama_lengkap']) ? $row['nasabah']['nama_lengkap'] : '-',
            'saldo' => isset($row['nasabah']['saldo']) ? (float) $row['nasabah']['saldo'] : 0,
            'total_nilai' => isset($row['total_nilai']) ? (float) $row['total_nilai'] : 0,
            'tanggal_setor' => $row['tanggal_setor'] ?? null,
            'tanggal_proses' => $row['tanggal_proses'] ?? null,
            'status' => $row['status'] ?? 'menunggu',
        ];
    }

    private function fetchDetailSetorItems(int $transaksiId): array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/detail_setor?select=id_detail_setor,id_jenis,berat_kg,harga_kg,subtotal,status_item,catatan_admin,jenis_sampah(nama_jenis)&id_transaksi_setor=eq.' . $transaksiId,
            null,
            false
        );

        if (!$response->successful()) {
            $response = $this->supabaseRequest(
                'get',
                '/rest/v1/detail_setor?select=id_detail_setor,id_jenis,berat_kg,harga_kg,subtotal,jenis_sampah(nama_jenis)&id_transaksi_setor=eq.' . $transaksiId,
                null,
                false
            );
        }

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $mapped = [];
        foreach ($items as $item) {
            $mapped[] = [
                'id_detail_setor' => $item['id_detail_setor'] ?? null,
                'id_jenis' => $item['id_jenis'] ?? null,
                'nama_jenis' => isset($item['jenis_sampah']['nama_jenis']) ? $item['jenis_sampah']['nama_jenis'] : '-',
                'berat_kg' => isset($item['berat_kg']) ? (float) $item['berat_kg'] : 0,
                'harga_kg' => isset($item['harga_kg']) ? (float) $item['harga_kg'] : 0,
                'subtotal' => isset($item['subtotal']) ? (float) $item['subtotal'] : 0,
                'status_item' => $item['status_item'] ?? 'pending',
                'catatan_admin' => $item['catatan_admin'] ?? '',
            ];
        }

        return $mapped;
    }

    private function fetchDetailSetorByColumn(string $column, array $transaksiIds): array
    {
        $idList = implode(',', array_map('intval', $transaksiIds));
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/detail_setor?select=' . $column . ',id_jenis,berat_kg,status_item,jenis_sampah(nama_jenis)&' . $column . '=in.(' . $idList . ')',
            null,
            false
        );

        if (!$response->successful()) {
            $response = $this->supabaseRequest(
                'get',
                '/rest/v1/detail_setor?select=' . $column . ',id_jenis,berat_kg,jenis_sampah(nama_jenis)&' . $column . '=in.(' . $idList . ')',
                null,
                false
            );
        }

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $summary = [];
        $pendingJenis = [];
        $approvedCount = [];

        foreach ($items as $item) {
            $txId = $item[$column] ?? null;
            if ($txId === null) {
                continue;
            }

            $txId = (int) $txId;
            if (!isset($summary[$txId])) {
                $summary[$txId] = [
                    'jenis' => [],
                    'total_berat' => 0,
                ];
                $approvedCount[$txId] = 0;
            }

            $berat = isset($item['berat_kg']) ? (float) $item['berat_kg'] : 0;
            $summary[$txId]['total_berat'] += $berat;

            $namaJenis = null;
            if (isset($item['jenis_sampah']) && is_array($item['jenis_sampah'])) {
                $namaJenis = $item['jenis_sampah']['nama_jenis'] ?? null;
            }

            $statusItem = strtolower(trim((string) ($item['status_item'] ?? 'pending')));
            if ($statusItem === 'approved') {
                $approvedCount[$txId]++;
            }

            if ($namaJenis) {
                $summary[$txId]['jenis'][] = [
                    'nama' => $namaJenis,
                    'status' => $statusItem,
                ];
            } else {
                $idJenis = $item['id_jenis'] ?? $item['id_jenis_sampah'] ?? null;
                if ($idJenis !== null) {
                    $pendingJenis[$txId][] = [
                        'id' => (int) $idJenis,
                        'status' => $statusItem,
                    ];
                }
            }
        }

        if (count($pendingJenis) > 0) {
            $jenisMap = $this->fetchJenisSampahMap($pendingJenis);
            foreach ($pendingJenis as $txId => $jenisItems) {
                foreach ($jenisItems as $jenisItem) {
                    $jenisId = $jenisItem['id'] ?? null;
                    if ($jenisId !== null && isset($jenisMap[$jenisId])) {
                        $summary[$txId]['jenis'][] = [
                            'nama' => $jenisMap[$jenisId],
                            'status' => $jenisItem['status'] ?? 'pending',
                        ];
                    }
                }
            }
        }

        foreach ($summary as $txId => $data) {
            $jenisList = [];
            $onlyApproved = ($approvedCount[$txId] ?? 0) > 0;
            foreach ($data['jenis'] as $jenisItem) {
                if (!is_array($jenisItem)) {
                    continue;
                }
                if ($onlyApproved && ($jenisItem['status'] ?? '') !== 'approved') {
                    continue;
                }
                $jenisList[] = $jenisItem['nama'] ?? '-';
            }
            $jenisList = array_values(array_unique($jenisList));
            $summary[$txId]['jenis'] = count($jenisList) > 0 ? implode(', ', $jenisList) : 'N/A';
        }

        return $summary;
    }

    private function fetchJenisSampahMap(array $pendingJenisByTx): array
    {
        $ids = [];
        foreach ($pendingJenisByTx as $jenisItems) {
            foreach ($jenisItems as $jenisItem) {
                $jenisId = is_array($jenisItem) ? ($jenisItem['id'] ?? null) : $jenisItem;
                if ($jenisId !== null) {
                    $ids[] = (int) $jenisId;
                }
            }
        }

        $uniqueIds = array_values(array_unique(array_filter($ids)));
        if (count($uniqueIds) === 0) {
            return [];
        }

        $idList = implode(',', array_map('intval', $uniqueIds));
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/jenis_sampah?select=id_jenis_sampah,nama_jenis&id_jenis_sampah=in.(' . $idList . ')',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $map = [];
        foreach ($items as $item) {
            $id = $item['id_jenis_sampah'] ?? null;
            if ($id === null) {
                continue;
            }
            $map[(int) $id] = $item['nama_jenis'] ?? '-';
        }

        return $map;
    }

    private function supportsDetailStatusColumns(): bool
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/detail_setor?select=id_detail_setor,status_item,catatan_admin&limit=1',
            null,
            false
        );

        return $response->successful();
    }

    private function supabaseRequest(string $method, string $path, ?array $payload, bool $returnRepresentation)
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');

        $request = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ]);

        if ($returnRepresentation) {
            $request = $request->withHeaders([
                'Prefer' => 'return=representation',
            ]);
        }

        $url = $supabaseUrl . $path;

        if ($method === 'get') {
            return $request->get($url);
        }

        if ($method === 'post') {
            return $request->post($url, $payload ?? []);
        }

        if ($method === 'patch') {
            return $request->patch($url, $payload ?? []);
        }

        if ($method === 'delete') {
            return $request->delete($url);
        }

        return $request->get($url);
    }
}
