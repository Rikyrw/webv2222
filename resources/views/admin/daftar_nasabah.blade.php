@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold mb-2">Daftar Nasabah</h1>
            <p class="text-muted">Kelola data dan status akun nasabah dari satu tempat.</p>
        </div>
    </div>

    <!-- Success Message -->
    @if (!empty($flash))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $flash }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Toolbar -->
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title fw-bold mb-1">Daftar Semua Nasabah</h5>
                            <p class="text-muted small mb-0">Filter data dan proses status akun.</p>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search..." />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No. HP</th>
                                    <th>Saldo</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nasabahs as $n)
                                    <tr>
                                        <td class="fw-semibold">{{ $n['id_nasabah'] }}</td>
                                        <td>{{ $n['nama_nasabah'] }}</td>
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
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                @if ($n['status_akun'] === 'menunggu')
                                                    <form method="POST" class="action-form d-inline" data-message="Aktifkan akun nasabah ini?">
                                                        @csrf
                                                        <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                                        <input type="hidden" name="action" value="aktifkan">
                                                        <button type="submit" class="btn btn-sm btn-success">Aktifkan</button>
                                                    </form>
                                                    <form method="POST" class="action-form d-inline" data-message="Tolak (nonaktifkan) akun nasabah ini?">
                                                        @csrf
                                                        <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                                        <input type="hidden" name="action" value="tolak">
                                                        <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                                    </form>
                                                @else
                                                    <a href="#" class="btn btn-sm btn-secondary">Edit</a>
                                                    <form method="POST" class="action-form d-inline" data-message="Hapus nasabah ini? Tindakan tidak dapat dibatalkan.">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $n['id_nasabah'] }}">
                                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                    </form>
                                                    <a href="#" class="btn btn-sm btn-info text-white">Riwayat</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <p class="mb-0">📭 Tidak ada data nasabah</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('tbody');
    const tableRows = tableBody.querySelectorAll('tr');
    const emptyMessageRow = tableBody.querySelector('tr td[colspan="7"]');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Skip empty message row
            if (row.querySelector('td[colspan="7"]')) {
                return;
            }

            const noRekening = row.cells[0].textContent.toLowerCase();
            const nama = row.cells[1].textContent.toLowerCase();
            const alamat = row.cells[2].textContent.toLowerCase();
            const noHp = row.cells[3].textContent.toLowerCase();

            if (noRekening.includes(searchTerm) || 
                nama.includes(searchTerm) || 
                alamat.includes(searchTerm) || 
                noHp.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty message
        if (visibleCount === 0) {
            if (!emptyMessageRow) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = '<td colspan="7" class="text-center py-5 text-muted"><p class="mb-0">📭 Tidak ada data nasabah yang cocok dengan pencarian</p></td>';
                tableBody.appendChild(emptyRow);
            } else {
                emptyMessageRow.parentElement.style.display = '';
            }
        } else {
            if (emptyMessageRow) {
                emptyMessageRow.parentElement.style.display = 'none';
            }
        }
    });

    // Form confirmation
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
