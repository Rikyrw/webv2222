@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header with Button -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-6 fw-bold mb-2">Daftar Sampah</h1>
            <p class="text-muted">Kelola jenis sampah, harga, dan stok yang tersedia.</p>
        </div>
        <div class="col-auto">
            <a href="#" class="btn btn-primary">
                <i class="lucide-plus"></i> Tambah Sampah
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (!empty($flash))
        <div class="row mb-3">
            <div class="col-12">
                @if ($flashType == 'success')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $flash }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $flash }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Card Header -->
                    <div class="mb-4">
                        <h5 class="card-title fw-bold mb-1">Data Jenis Sampah</h5>
                        <p class="text-muted small mb-0">Atur harga dan stok sampah secara cepat.</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Jenis Sampah</th>
                        <th>Harga per kg (Rp)</th>
                        <th>Stok (kg)</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sampahList as $item)
                        @if ($item['status'] !== 'nonaktif')
                            <tr>
                                <td class="fw-semibold">{{ $item['id_jenis'] }}</td>
                                <td>{{ $item['nama_jenis'] }}</td>
                                <td>Rp {{ number_format($item['harga_per_kg'], 0, ',', '.') }}</td>
                                @if ((float)$item['stok_kg'] < 5)
                                    <td class="fw-bold text-danger">{{ number_format($item['stok_kg'], 1, ',', '.') }} kg</td>
                                @else
                                    <td class="fw-normal">{{ number_format($item['stok_kg'], 1, ',', '.') }} kg</td>
                                @endif
                                <td>
                                    @if ($item['status'] === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                                        <a href="#" class="btn btn-sm btn-info text-white">Edit</a>
                                        <form method="POST" class="delete-form d-inline" data-confirm="Hapus sampah {{ $item['nama_jenis'] }}?">
                                            @csrf
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="{{ $item['id_jenis'] }}">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p class="mb-0">📭 Tidak ada data sampah</p>
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
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const message = form.dataset.confirm || 'Hapus item ini?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
</script>
@endsection
