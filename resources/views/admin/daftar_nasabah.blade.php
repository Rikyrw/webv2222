@extends('layouts.app')

@section('content')
<div class="page-shell">
    <div class="section-header">
        <div>
            <div class="section-title">Daftar Nasabah</div>
            <div class="section-subtitle">Kelola data dan status akun nasabah dari satu tempat.</div>
        </div>
    </div>

    @if (!empty($flash))
        <div class="section-card" style="padding: 14px 16px; border-left: 4px solid #10b981; background: #f0fdf4; color: #166534; font-weight: 600;">
            {{ $flash }}
        </div>
    @endif

    <div class="section-card">
        <div class="toolbar" style="margin-bottom: 16px;">
            <div>
                <div style="font-size: 18px; font-weight: 700; color: #0f172a;">Daftar Semua Nasabah</div>
                <div class="section-subtitle">Filter data dan proses status akun.</div>
            </div>

            <div class="toolbar-group">
                <select class="select">
                    <option>Semua</option>
                    <option>Aktif</option>
                    <option>Menunggu</option>
                    <option>Nonaktif</option>
                </select>
                <input type="text" class="input" placeholder="Cari nasabah..." style="min-width: 220px;" />
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Rekening</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nasabahs as $n)
                        <tr>
                            <td>{{ $n['id_nasabah'] }}</td>
                            <td>{{ $n['nama_nasabah'] }}</td>
                            <td style="max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $n['alamat'] }}</td>
                            <td>{{ $n['no_hp'] }}</td>
                            <td>Rp {{ number_format($n['saldo'], 0, ',', '.') }}</td>
                            <td>
                                @if ($n['status_akun'] === 'aktif')
                                    <span class="status-pill status-success">Aktif</span>
                                @elseif ($n['status_akun'] === 'menunggu')
                                    <span class="status-pill status-warning">Menunggu</span>
                                @elseif ($n['status_akun'] === 'nonaktif')
                                    <span class="status-pill status-danger">Ditolak</span>
                                @else
                                    <span class="status-pill status-neutral">{{ $n['status_akun'] }}</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                                    @if ($n['status_akun'] === 'menunggu')
                                        <form method="POST" class="action-form" data-message="Aktifkan akun nasabah ini?">
                                            @csrf
                                            <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                            <input type="hidden" name="action" value="aktifkan">
                                            <button type="submit" class="btn btn-primary" style="padding: 8px 12px;">Aktifkan</button>
                                        </form>
                                        <form method="POST" class="action-form" data-message="Tolak (nonaktifkan) akun nasabah ini?">
                                            @csrf
                                            <input type="hidden" name="id_nasabah" value="{{ $n['id_nasabah'] }}">
                                            <input type="hidden" name="action" value="tolak">
                                            <button type="submit" class="btn btn-danger" style="padding: 8px 12px;">Tolak</button>
                                        </form>
                                    @else
                                        <a href="#" class="btn btn-secondary" style="padding: 8px 12px;">Edit</a>
                                        <form method="POST" class="action-form" data-message="Hapus nasabah ini? Tindakan tidak dapat dibatalkan.">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $n['id_nasabah'] }}">
                                            <button type="submit" class="btn btn-danger" style="padding: 8px 12px;">Hapus</button>
                                        </form>
                                        <a href="#" class="btn btn-info" style="padding: 8px 12px;">Riwayat</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align: center; color: #64748b;">Tidak ada data nasabah</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
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
