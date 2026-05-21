@extends('layouts.app')

@section('content')
<div class="gp-page transaksi-page">
    <div class="card">
        <div class="card-body p-0">
            <nav class="gp-tabs" aria-label="Tab transaksi">
                <a href="?tab=setor" class="gp-tab {{ ($tab == 'setor') ? 'is-active' : '' }}">
                    <i class="bi bi-recycle"></i>Permintaan Setor
                </a>
                <a href="?tab=penarikan" class="gp-tab {{ ($tab == 'penarikan') ? 'is-active' : '' }}">
                    <i class="bi bi-wallet2"></i>Permintaan Penarikan
                </a>
                <a href="?tab=history" class="gp-tab {{ ($tab == 'history') ? 'is-active' : '' }}">
                    <i class="bi bi-clock-history"></i>Riwayat
                </a>
            </nav>
        </div>
    </div>

    @if ($tab == 'setor')
        <div class="card">
            <div class="card-body">
                <div class="gp-card-header">
                    <div>
                        <h2 class="gp-title">Permintaan Setor Sampah</h2>
                        <p class="gp-subtitle mb-0">Tinjau dan proses permintaan setor sampah dari nasabah.</p>
                    </div>
                </div>

                @if (count($setorRequests) == 0)
                    <div class="gp-empty">Tidak ada permintaan setor sampah yang menunggu.</div>
                @else
                    <div id="setor-table-wrap">
                        <div class="table-responsive">
                            <table class="table gp-table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nasabah</th>
                                        <th>Jenis</th>
                                        <th>Total Berat</th>
                                        <th>Total Nilai</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($setorRequests as $tx)
                                        <tr>
                                            <td class="fw-semibold">{{ ($setorRequestsMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                            <td>{{ $tx['nama_nasabah'] }}</td>
                                            <td>{{ $tx['jenis'] }}</td>
                                            <td>{{ number_format($tx['total_berat'], 2, ',', '.') }} kg</td>
                                            <td>Rp {{ number_format($tx['total_nilai'], 0, ',', '.') }}</td>
                                            <td>{{ $tx['tanggal_setor'] ? \Carbon\Carbon::parse($tx['tanggal_setor'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($tx['status'] === 'menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @elseif (in_array($tx['status'], ['approved', 'selesai', 'success']))
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif (in_array($tx['status'], ['ditolak', 'rejected']))
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($tx['status']) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.transaksi.setor.detail', ['id' => $tx['id_transaksi']]) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>{{ $tx['status'] === 'menunggu' ? 'Proses' : 'Detail' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="gp-pagination">
                            <span class="text-muted small">Halaman {{ $setorRequestsMeta['page'] ?? 1 }}</span>
                            @if (!empty($setorRequestsMeta['has_prev']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'setor', 'page_setor_req' => $setorRequestsMeta['page'] - 1]) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                            @endif
                            @if (!empty($setorRequestsMeta['has_next']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'setor', 'page_setor_req' => $setorRequestsMeta['page'] + 1]) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @elseif ($tab == 'penarikan')
        <div class="card">
            <div class="card-body">
                <div class="gp-card-header">
                    <div>
                        <h2 class="gp-title">Permintaan Penarikan</h2>
                        <p class="gp-subtitle mb-0">Tinjau dan proses permintaan penarikan saldo nasabah.</p>
                    </div>
                </div>

                @if (count($penarikanRequests) == 0)
                    <div class="gp-empty">Tidak ada permintaan penarikan yang menunggu.</div>
                @else
                    <div id="penarikan-table-wrap">
                        <div class="table-responsive">
                            <table class="table gp-table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nasabah</th>
                                        <th>Jenis</th>
                                        <th>Nominal (Rp)</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penarikanRequests as $p)
                                        <tr>
                                            <td class="fw-semibold">{{ ($penarikanRequestsMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                            <td>{{ $p['nama_nasabah'] }}</td>
                                            <td>{{ $p['jenis_penukaran'] }}</td>
                                            <td>{{ number_format($p['nominal'], 0, ',', '.') }}</td>
                                            <td>{{ $p['deskripsi'] }}</td>
                                            <td>{{ $p['tanggal_pengajuan'] ? \Carbon\Carbon::parse($p['tanggal_pengajuan'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($p['status'] === 'menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @elseif (in_array($p['status'], ['approved', 'selesai', 'success']))
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif (in_array($p['status'], ['ditolak', 'rejected']))
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($p['status']) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($p['status'] === 'menunggu')
                                                    <div class="d-flex justify-content-center flex-wrap gp-actions">
                                                        <button type="button" class="btn btn-sm btn-primary btn-action-penarikan" data-id="{{ $p['id_penukaran'] }}" data-action="approve" data-url="{{ route('admin.transaksi.penarikan.action', ['id' => $p['id_penukaran']]) }}">
                                                            <i class="bi bi-check2"></i>Setujui
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger btn-action-penarikan" data-id="{{ $p['id_penukaran'] }}" data-action="reject" data-url="{{ route('admin.transaksi.penarikan.action', ['id' => $p['id_penukaran']]) }}">
                                                            <i class="bi bi-x-lg"></i>Tolak
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Proses selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="gp-pagination">
                            <span class="text-muted small">Halaman {{ $penarikanRequestsMeta['page'] ?? 1 }}</span>
                            @if (!empty($penarikanRequestsMeta['has_prev']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'penarikan', 'page_penarikan_req' => $penarikanRequestsMeta['page'] - 1]) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                            @endif
                            @if (!empty($penarikanRequestsMeta['has_next']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'penarikan', 'page_penarikan_req' => $penarikanRequestsMeta['page'] + 1]) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="gp-card-header">
                    <div>
                        <h2 class="gp-title">Riwayat Penarikan & Setor</h2>
                        <p class="gp-subtitle mb-0">Riwayat terakhir nasabah menarik saldo atau setor sampah.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.transaksi') }}" class="gp-filter-form">
                        <input type="hidden" name="tab" value="history">
                        <label class="form-label mb-0">Status</label>
                        <select name="history_status" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="all" {{ ($historyStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="selesai" {{ ($historyStatus ?? 'all') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="sebagian" {{ ($historyStatus ?? 'all') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                            <option value="ditolak" {{ ($historyStatus ?? 'all') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <label class="form-label mb-0">Tanggal</label>
                        <input type="date" name="history_date" value="{{ $historyDate ?? '' }}" onchange="this.form.submit()" class="form-control form-control-sm">
                    </form>
                </div>

                <div id="history-setor-wrap">
                    <h3 class="gp-title mb-3">Riwayat Setor Sampah</h3>
                    @if (count($historySetor) == 0)
                        <div class="gp-empty">Belum ada riwayat setor.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table gp-table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nasabah</th>
                                        <th>Jenis</th>
                                        <th>Total Berat</th>
                                        <th>Total Nilai</th>
                                        <th>Disetujui</th>
                                        <th>Ditolak</th>
                                        <th>Catatan Admin</th>
                                        <th>Tanggal Proses</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historySetor as $h)
                                        <tr>
                                            <td class="fw-semibold">{{ ($historySetorMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                            <td>{{ $h['nama_nasabah'] }}</td>
                                            <td>{{ $h['jenis'] }}</td>
                                            <td>{{ number_format($h['total_berat'], 2, ',', '.') }} kg</td>
                                            <td>Rp {{ number_format($h['total_nilai'], 0, ',', '.') }}</td>
                                            <td>
                                                @forelse ($h['approved_items'] ?? [] as $item)
                                                    <div class="small">{{ $item }}</div>
                                                @empty
                                                    <span class="text-muted small">-</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @forelse ($h['rejected_items'] ?? [] as $item)
                                                    <div class="small">{{ $item }}</div>
                                                @empty
                                                    <span class="text-muted small">-</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @forelse ($h['catatan_admin'] ?? [] as $note)
                                                    <div class="text-muted small">{{ $note }}</div>
                                                @empty
                                                    <span class="text-muted small">-</span>
                                                @endforelse
                                            </td>
                                            <td>{{ $h['tanggal_proses'] ? \Carbon\Carbon::parse($h['tanggal_proses'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($h['status'] === 'menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @elseif (in_array($h['status'], ['approved', 'selesai', 'success']))
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif (in_array($h['status'], ['ditolak', 'rejected']))
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($h['status']) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="gp-pagination">
                            <span class="text-muted small">Halaman {{ $historySetorMeta['page'] ?? 1 }}</span>
                            @if (!empty($historySetorMeta['has_prev']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_setor' => $historySetorMeta['page'] - 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                            @endif
                            @if (!empty($historySetorMeta['has_next']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_setor' => $historySetorMeta['page'] + 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div id="history-penarikan-wrap" class="mt-4">
                    <h3 class="gp-title mb-3">Riwayat Penarikan</h3>
                    @if (count($historyPenarikan) == 0)
                        <div class="gp-empty">Belum ada riwayat penarikan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table gp-table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nasabah</th>
                                        <th>Jenis</th>
                                        <th>Nominal (Rp)</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal Proses</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historyPenarikan as $p)
                                        <tr>
                                            <td class="fw-semibold">{{ ($historyPenarikanMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                            <td>{{ $p['nama_nasabah'] }}</td>
                                            <td>{{ $p['jenis_penukaran'] }}</td>
                                            <td>{{ number_format($p['nominal'], 0, ',', '.') }}</td>
                                            <td>{{ $p['deskripsi'] }}</td>
                                            <td>{{ $p['tanggal_proses'] ? \Carbon\Carbon::parse($p['tanggal_proses'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($p['status'] === 'menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                @elseif (in_array($p['status'], ['approved', 'selesai', 'success']))
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif (in_array($p['status'], ['ditolak', 'rejected']))
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($p['status']) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="gp-pagination">
                            <span class="text-muted small">Halaman {{ $historyPenarikanMeta['page'] ?? 1 }}</span>
                            @if (!empty($historyPenarikanMeta['has_prev']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_penarikan_hist' => $historyPenarikanMeta['page'] - 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                            @endif
                            @if (!empty($historyPenarikanMeta['has_next']))
                                <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_penarikan_hist' => $historyPenarikanMeta['page'] + 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="rejectModalSetor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectFormSetor">
                <div class="modal-header">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectNoteSetor" class="form-label">Catatan</label>
                    <textarea id="rejectNoteSetor" class="form-control" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModalPenarikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectFormPenarikan">
                <div class="modal-header">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectNotePenarikan" class="form-label">Catatan</label>
                    <textarea id="rejectNotePenarikan" class="form-control" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gp-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
    }

    .gp-tab {
        flex: 1 1 220px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 48px;
        padding: 12px 16px;
        color: #526158;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        border-bottom: 2px solid transparent;
    }

    .gp-tab:hover {
        background: #f4f8f5;
        color: #2f5f3e;
    }

    .gp-tab.is-active {
        color: #2f5f3e;
        border-bottom-color: #2f5f3e;
        background: #fbfcfb;
    }

    .gp-pagination,
    .gp-filter-form {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .gp-filter-form {
        margin-top: 0;
    }
</style>
@endsection

@push('scripts')
<script>
let pendingSetorId = null;
let pendingPenarikanId = null;
let pendingPenarikanUrl = null;
const rejectModalSetorEl = document.getElementById('rejectModalSetor');
const rejectModalPenarikanEl = document.getElementById('rejectModalPenarikan');
const rejectModalSetor = rejectModalSetorEl ? new bootstrap.Modal(rejectModalSetorEl) : null;
const rejectModalPenarikan = rejectModalPenarikanEl ? new bootstrap.Modal(rejectModalPenarikanEl) : null;

function bindPenarikanActions(scope = document) {
    scope.querySelectorAll('.btn-action-penarikan').forEach(btn => {
        if (btn.dataset.bound === 'true') {
            return;
        }
        btn.dataset.bound = 'true';
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const action = this.dataset.action;
            const url = this.dataset.url;

            if (action === 'reject') {
                pendingPenarikanId = id;
                pendingPenarikanUrl = url;
                if (rejectModalPenarikan) {
                    rejectModalPenarikan.show();
                }
            } else if (action === 'approve' && confirm('Setujui permintaan penarikan ini?')) {
                submitPenarikanAction(url, 'approve', '');
            }
        });
    });
}

document.querySelectorAll('.btn-action-setor').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const action = this.dataset.action;

        if (action === 'reject') {
            pendingSetorId = id;
            if (rejectModalSetor) {
                rejectModalSetor.show();
            }
        } else if (action === 'approve' && confirm('Setujui permintaan setor sampah ini?')) {
            console.log('Approve setor:', id);
            alert('Permintaan setor sampah berhasil disetujui');
            location.reload();
        }
    });
});

bindPenarikanActions();

document.getElementById('rejectFormSetor').addEventListener('submit', function(e) {
    e.preventDefault();
    const note = document.getElementById('rejectNoteSetor').value;
    if (!note.trim()) {
        alert('Alasan penolakan harus diisi');
        return;
    }
    console.log('Reject setor:', pendingSetorId, 'Reason:', note);
    alert('Permintaan setor sampah berhasil ditolak');
    if (rejectModalSetor) {
        rejectModalSetor.hide();
    }
    location.reload();
});

document.getElementById('rejectFormPenarikan').addEventListener('submit', function(e) {
    e.preventDefault();
    const note = document.getElementById('rejectNotePenarikan').value;
    if (!note.trim()) {
        alert('Alasan penolakan harus diisi');
        return;
    }
    submitPenarikanAction(pendingPenarikanUrl, 'reject', note);
});

function submitPenarikanAction(url, action, note) {
    if (!url) {
        alert('URL aksi penarikan tidak ditemukan.');
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]');
    const csrf = token ? token.getAttribute('content') : '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action, note })
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                throw new Error(data && data.message ? data.message : 'Gagal memproses penarikan.');
            }
            alert(data.message || 'Permintaan penarikan berhasil diproses.');
            if (rejectModalPenarikan) {
                rejectModalPenarikan.hide();
            }
            location.reload();
        })
        .catch(error => {
            alert(error.message || 'Terjadi kesalahan saat memproses penarikan.');
        });
}

function refreshTransaksiTables() {
    const activeElement = document.activeElement;
    if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'SELECT' || activeElement.tagName === 'TEXTAREA')) {
        return;
    }

    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            ['setor-table-wrap', 'penarikan-table-wrap'].forEach(function(id) {
                const current = document.getElementById(id);
                const incoming = doc.getElementById(id);
                if (current && incoming) {
                    current.innerHTML = incoming.innerHTML;
                    bindPenarikanActions(current);
                }
            });
        })
        .catch(() => {
            // Skip refresh errors to avoid interrupting user flow.
        });
}

refreshTransaksiTables();
setInterval(refreshTransaksiTables, 5000);
</script>
@endpush
