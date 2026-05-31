@extends('layouts.app')

@section('content')
<div class="gp-page">
    @if (!empty($flash))
        @include('partials.toast', ['type' => $flashType == 'success' ? 'success' : 'danger', 'message' => $flash])
    @endif

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Data Jenis Sampah</h2>
                    <p class="gp-subtitle mb-0">Atur harga, stok, dan status sampah secara cepat.</p>
                </div>
                <a href="{{ route('admin.sampah.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>Tambah Sampah
                </a>
            </div>

            <div class="table-responsive">
                <table class="table gp-table gp-data-table table-hover align-middle" data-page-length="8">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis Sampah</th>
                            <th>Harga per kg (Rp)</th>
                            <th>Stok (kg)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sampahList as $item)
                            <tr>
                                <td class="fw-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $item['nama_jenis'] }}</td>
                                <td>Rp {{ number_format($item['harga_per_kg'], 0, ',', '.') }}</td>
                                <td class="{{ (float)$item['stok_kg'] < 5 ? 'fw-bold text-danger' : '' }}">
                                    {{ number_format($item['stok_kg'], 1, ',', '.') }} kg
                                </td>
                                <td>
                                    @if ($item['status'] === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gp-actions">
                                        <a href="{{ route('admin.sampah.edit', $item['id_jenis']) }}" class="btn btn-sm btn-secondary"><i class="bi bi-pencil"></i>Edit</a>
                                        <form method="POST" class="delete-form d-inline" data-confirm="Hapus sampah {{ $item['nama_jenis'] }}?">
                                            @csrf
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="{{ $item['id_jenis'] }}">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i>Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="gp-empty">Tidak ada data sampah</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
