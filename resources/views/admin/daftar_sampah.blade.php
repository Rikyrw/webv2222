@extends('layouts.app')

@section('content')
<div class="page-shell">
    <div class="section-header">
        <div>
            <div class="section-title">Daftar Sampah</div>
            <div class="section-subtitle">Kelola jenis sampah, harga, dan stok yang tersedia.</div>
        </div>

        <a href="#" class="btn btn-primary">
            <i class="lucide-plus"></i>
            Tambah Sampah
        </a>
    </div>

    @if (!empty($flash))
        @if ($flashType == 'success')
            <div class="section-card" style="padding: 14px 16px; border-left: 4px solid #10b981; background: #f0fdf4; color: #166534; font-weight: 600;">
                {{ $flash }}
            </div>
        @else
            <div class="section-card" style="padding: 14px 16px; border-left: 4px solid #ef4444; background: #fef2f2; color: #991b1b; font-weight: 600;">
                {{ $flash }}
            </div>
        @endif
    @endif

    <div class="section-card">
        <div class="toolbar" style="margin-bottom: 16px;">
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #0f172a;">Data Jenis Sampah</div>
                <div class="section-subtitle">Atur harga dan stok sampah secara cepat.</div>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jenis Sampah</th>
                        <th>Harga per kg (Rp)</th>
                        <th>Stok (kg)</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sampahList as $item)
                        @if ($item['status'] !== 'nonaktif')
                            <tr>
                                <td>{{ $item['id_jenis'] }}</td>
                                <td>{{ $item['nama_jenis'] }}</td>
                                <td>{{ number_format($item['harga_per_kg'], 0, ',', '.') }}</td>
                                @if ((float)$item['stok_kg'] < 5)
                                    <td style="font-weight:700; color:#dc2626;">{{ number_format($item['stok_kg'], 1, ',', '.') }}</td>
                                @else
                                    <td style="font-weight:400; color:#0f172a;">{{ number_format($item['stok_kg'], 1, ',', '.') }}</td>
                                @endif
                                <td>
                                    @if ($item['status'] === 'aktif')
                                        <span class="status-pill status-success">Aktif</span>
                                    @else
                                        <span class="status-pill status-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                                        <a href="#" class="btn btn-info" style="padding: 8px 12px;">Edit</a>
                                        <form method="POST" class="delete-form" data-confirm="Hapus sampah {{ $item['nama_jenis'] }}?">
                                            @csrf
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="{{ $item['id_jenis'] }}">
                                            <button type="submit" class="btn btn-danger" style="padding: 8px 12px;">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 24px; color: #64748b;">Tidak ada data sampah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
