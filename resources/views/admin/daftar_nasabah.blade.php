@extends('layouts.app')

@section('content')
<div class="gp-page">
    @if (!empty($flash))
        @include('partials.toast', ['type' => 'success', 'message' => $flash])
    @endif

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Daftar Semua Nasabah</h2>
                    <p class="gp-subtitle mb-0">Filter data dan proses status akun.</p>
                </div>
                <div style="min-width: min(100%, 320px);">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari nasabah..." />
                </div>
            </div>

            <div class="table-responsive">
                <table class="table gp-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Tgl Daftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nasabahs as $n)
                            <tr>
                                <td class="fw-semibold">{{ ($nasabahsMeta['offset'] ?? 0) + $loop->iteration }}</td>
                                <td>{{ $n['user_name'] ?? '-' }}</td>
                                <td>{{ $n['nama_nasabah'] }}</td>
                                <td>{{ $n['email'] ?? '-' }}</td>
                                <td>
                                    <span title="{{ $n['alamat'] }}" style="display: inline-block; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $n['alamat'] }}
                                    </span>
                                </td>
                                <td>{{ $n['no_hp'] }}</td>
                                <td class="fw-semibold text-success">Rp {{ number_format($n['saldo'], 0, ',', '.') }}</td>
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
                                <td>{{ is_string($n['tanggal_daftar']) ? date('d M Y', strtotime($n['tanggal_daftar'])) : '-' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gp-actions">
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
                                            <form method="POST" action="{{ route('admin.nasabah.delete', $n['id_nasabah']) }}" class="action-form d-inline" data-message="Hapus nasabah ini? Tindakan tidak dapat dibatalkan.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i>Hapus</button>
                                            </form>
                                            <a href="{{ route('admin.nasabah.riwayat', $n['id_nasabah']) }}" class="btn btn-sm btn-info"><i class="bi bi-clock-history"></i>Riwayat</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="gp-empty">Tidak ada data nasabah</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 mt-3 flex-wrap">
                <span class="text-muted small">Halaman {{ $nasabahsMeta['page'] ?? 1 }}</span>
                @if (!empty($nasabahsMeta['has_prev']))
                    <a href="{{ route('admin.nasabah.daftar', ['page' => ($nasabahsMeta['page'] ?? 1) - 1]) }}" class="btn btn-sm btn-outline-secondary">Sebelumnya</a>
                @endif
                @if (!empty($nasabahsMeta['has_next']))
                    <a href="{{ route('admin.nasabah.daftar', ['page' => ($nasabahsMeta['page'] ?? 1) + 1]) }}" class="btn btn-sm btn-primary">Berikutnya</a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const tableRows = tableBody.querySelectorAll('tr');
    const emptyMessageRow = tableBody.querySelector('tr td[colspan="10"]');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;

        tableRows.forEach(row => {
            if (row.querySelector('td[colspan="10"]')) {
                return;
            }

            const rowText = row.textContent.toLowerCase();

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
