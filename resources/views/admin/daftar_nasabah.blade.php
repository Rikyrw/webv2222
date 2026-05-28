@extends('layouts.app')

@push('styles')
<style>
    .nasabah-card .card-body {
        padding: 0 !important;
    }

    .nasabah-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px 16px;
        border-bottom: 1px solid #e8eee9;
    }

    .nasabah-toolbar-title {
        min-width: 0;
    }

    .nasabah-search {
        position: relative;
        width: min(100%, 340px);
        flex: 0 0 auto;
    }

    .nasabah-search i {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #6d7a71;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .nasabah-search .form-control {
        min-height: 40px;
        padding-left: 38px !important;
    }

    .nasabah-filter-panel {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .nasabah-date-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .nasabah-date-field {
        position: relative;
        width: 146px;
    }

    .nasabah-date-field i {
        position: absolute;
        top: 50%;
        left: 11px;
        color: #6d7a71;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .nasabah-date-field .form-control {
        min-height: 40px;
        padding-left: 36px !important;
    }

    .nasabah-table-wrap {
        overflow: auto;
    }

    .nasabah-table {
        min-width: 980px;
        table-layout: fixed;
    }

    .nasabah-table th,
    .nasabah-table td {
        vertical-align: middle;
    }

    .nasabah-table th:nth-child(1),
    .nasabah-table td:nth-child(1) {
        width: 58px;
    }

    .nasabah-table th:nth-child(2),
    .nasabah-table td:nth-child(2) {
        width: 250px;
    }

    .nasabah-table th:nth-child(3),
    .nasabah-table td:nth-child(3) {
        width: 250px;
    }

    .nasabah-table th:nth-child(4),
    .nasabah-table td:nth-child(4) {
        width: 220px;
    }

    .nasabah-table th:nth-child(5),
    .nasabah-table td:nth-child(5) {
        width: 120px;
    }

    .nasabah-table th:nth-child(6),
    .nasabah-table td:nth-child(6) {
        width: 110px;
    }

    .nasabah-table th:nth-child(7),
    .nasabah-table td:nth-child(7) {
        width: 118px;
    }

    .nasabah-table th:nth-child(8),
    .nasabah-table td:nth-child(8) {
        width: 190px;
    }

    .nasabah-person {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .nasabah-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 999px;
        background: #edf5ef;
        color: #2f5f3e;
        font-size: 12px;
        font-weight: 800;
    }

    .nasabah-main,
    .nasabah-contact,
    .nasabah-address {
        min-width: 0;
    }

    .nasabah-name,
    .nasabah-email,
    .nasabah-address {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .nasabah-name {
        color: #17231b;
        font-weight: 700;
    }

    .nasabah-username,
    .nasabah-phone,
    .nasabah-date {
        color: #6d7a71;
        font-size: 12px;
    }

    .nasabah-money {
        color: #1c6b37;
        font-weight: 800;
        white-space: nowrap;
    }

    .nasabah-actions {
        display: flex;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .nasabah-actions .btn {
        min-width: 34px;
    }

    .nasabah-pager {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 22px 18px;
        border-top: 1px solid #e8eee9;
        flex-wrap: wrap;
    }

    @media (max-width: 720px) {
        .nasabah-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .nasabah-search {
            width: 100%;
        }

        .nasabah-filter-panel,
        .nasabah-date-filter {
            align-items: stretch;
            flex-direction: column;
        }

        .nasabah-date-field,
        .nasabah-date-filter .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="gp-page">
    @if (!empty($flash))
        @include('partials.toast', ['type' => $flashType ?? 'success', 'message' => $flash])
    @endif

    <div class="card nasabah-card">
        <div class="card-body">
            <div class="nasabah-toolbar">
                <div class="nasabah-toolbar-title">
                    <h2 class="gp-title">Daftar Semua Nasabah</h2>
                    <p class="gp-subtitle mb-0">Filter data dan proses status akun.</p>
                </div>
                <div class="nasabah-filter-panel">
                    <form method="GET" action="{{ route('admin.nasabah.daftar') }}" class="nasabah-date-filter">
                        <div class="nasabah-date-field">
                            <i class="bi bi-calendar3"></i>
                            <input type="date" name="tanggal_daftar" class="form-control form-control-sm" value="{{ $dateFilters['tanggal_daftar'] ?? '' }}" aria-label="Tanggal daftar">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i>Filter</button>
                        @if (!empty($paginationFilters))
                            <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i>Reset</a>
                        @endif
                    </form>
                    <div class="nasabah-search">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari nasabah..." />
                    </div>
                </div>
            </div>

            <div class="nasabah-table-wrap">
                <table class="table gp-table table-hover align-middle nasabah-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nasabah</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Daftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nasabahs as $n)
                            @php
                                $displayName = trim((string) ($n['nama_nasabah'] ?? ''));
                                $displayName = $displayName !== '' ? $displayName : ($n['user_name'] ?? 'Nasabah');
                                $initials = collect(preg_split('/\s+/', $displayName))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $initials = $initials !== '' ? $initials : 'N';
                                $searchText = strtolower(implode(' ', [
                                    $n['user_name'] ?? '',
                                    $displayName,
                                    $n['email'] ?? '',
                                    $n['alamat'] ?? '',
                                    $n['no_hp'] ?? '',
                                    $n['status_akun'] ?? '',
                                ]));
                            @endphp
                            <tr class="nasabah-row" data-search="{{ e($searchText) }}">
                                <td class="fw-semibold">{{ ($nasabahsMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                <td>
                                    <div class="nasabah-person">
                                        <span class="nasabah-avatar">{{ $initials }}</span>
                                        <div class="nasabah-main">
                                            <div class="nasabah-name" title="{{ $displayName }}">{{ $displayName }}</div>
                                            <div class="nasabah-username">{{ $n['user_name'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="nasabah-contact">
                                        <div class="nasabah-email" title="{{ $n['email'] ?? '-' }}">{{ $n['email'] ?? '-' }}</div>
                                        <div class="nasabah-phone">{{ $n['no_hp'] }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="nasabah-address" title="{{ $n['alamat'] }}">{{ $n['alamat'] }}</div>
                                </td>
                                <td><span class="nasabah-money">Rp {{ number_format($n['saldo'], 0, ',', '.') }}</span></td>
                                <td>
                                    @if ($n['status_akun'] === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif ($n['status_akun'] === 'menunggu')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif ($n['status_akun'] === 'nonaktif')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $n['status_akun'] }}</span>
                                    @endif
                                </td>
                                <td><span class="nasabah-date">{{ is_string($n['tanggal_daftar']) ? date('d M Y', strtotime($n['tanggal_daftar'])) : '-' }}</span></td>
                                <td>
                                    <div class="nasabah-actions">
                                        @if ($n['status_akun'] === 'menunggu')
                                            <form method="POST" class="action-form d-inline" data-message="Aktifkan akun nasabah ini?">
                                                @csrf
                                                <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                                <input type="hidden" name="action" value="aktifkan">
                                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check2"></i>Setujui</button>
                                            </form>
                                            <form method="POST" class="action-form d-inline" data-message="Tolak (nonaktifkan) akun nasabah ini?">
                                                @csrf
                                                <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                                <input type="hidden" name="action" value="tolak">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i>Tolak</button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.nasabah.edit', $n['id_nasabah']) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i>Edit</a>
                                            @if ($n['can_delete'] ?? false)
                                                <form method="POST" action="{{ route('admin.nasabah.delete', $n['id_nasabah']) }}" class="action-form d-inline" data-message="Hapus nasabah ini? Tindakan tidak dapat dibatalkan.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i>Hapus</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.nasabah.riwayat', $n['id_nasabah']) }}" class="btn btn-sm btn-info"><i class="bi bi-clock-history"></i>Riwayat</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="gp-empty">Tidak ada data nasabah</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="nasabah-pager">
                <span class="text-muted small">Halaman {{ $nasabahsMeta['page'] ?? 1 }}</span>
                @if (!empty($nasabahsMeta['has_prev']))
                    <a href="{{ route('admin.nasabah.daftar', array_merge($paginationFilters ?? [], ['page' => ($nasabahsMeta['page'] ?? 1) - 1])) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                @endif
                @if (!empty($nasabahsMeta['has_next']))
                    <a href="{{ route('admin.nasabah.daftar', array_merge($paginationFilters ?? [], ['page' => ($nasabahsMeta['page'] ?? 1) + 1])) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const tableRows = tableBody.querySelectorAll('.nasabah-row');
    const emptyMessageRow = tableBody.querySelector('tr td[colspan="8"]');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const rowText = row.dataset.search || '';

            if (rowText.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyMessageRow) {
            emptyMessageRow.parentElement.style.display = visibleCount === 0 ? '' : 'none';
        }
    });

    document.querySelectorAll('.action-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const message = form.dataset.message || 'Tindakan akan diproses. Lanjutkan?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
</script>

@endsection
