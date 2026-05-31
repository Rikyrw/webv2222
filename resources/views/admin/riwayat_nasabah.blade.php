@extends('layouts.app')

@push('styles')
<style>
    .riwayat-penarikan-table {
        min-width: 920px;
        table-layout: fixed;
    }

    .riwayat-penarikan-table th:nth-child(1),
    .riwayat-penarikan-table td:nth-child(1) {
        width: 58px;
    }

    .riwayat-penarikan-table th:nth-child(2),
    .riwayat-penarikan-table td:nth-child(2) {
        width: 130px;
    }

    .riwayat-penarikan-table th:nth-child(3),
    .riwayat-penarikan-table td:nth-child(3) {
        width: 140px;
    }

    .riwayat-penarikan-table th:nth-child(5),
    .riwayat-penarikan-table td:nth-child(5),
    .riwayat-penarikan-table th:nth-child(6),
    .riwayat-penarikan-table td:nth-child(6) {
        width: 150px;
    }

    .riwayat-penarikan-table th:nth-child(7),
    .riwayat-penarikan-table td:nth-child(7) {
        width: 120px;
    }

    .riwayat-description {
        color: #2f3b35;
        line-height: 1.45;
        max-width: 100%;
        overflow-wrap: anywhere;
        white-space: normal;
        word-break: normal;
    }
</style>
@endpush

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Ringkasan Nasabah</h2>
                    <p class="gp-subtitle mb-0">Riwayat transaksi untuk {{ $nasabah['nama_lengkap'] ?? 'Nasabah' }}.</p>
                </div>
                <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>

            @if (!empty($databaseError))
                @include('partials.toast', ['type' => 'danger', 'message' => $databaseError])
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <h2 class="gp-title">Riwayat Setor Sampah</h2>
            </div>
            <div class="table-responsive">
                <table class="table gp-table gp-data-table table-hover align-middle" data-page-length="8">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis</th>
                            <th>Total Berat</th>
                            <th>Total Nilai</th>
                            <th>Tanggal Setor</th>
                            <th>Tanggal Proses</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($setorList as $item)
                            <tr>
                                <td class="fw-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $item['jenis'] }}</td>
                                <td>{{ number_format($item['total_berat'], 2, ',', '.') }} kg</td>
                                <td>Rp {{ number_format($item['total_nilai'], 0, ',', '.') }}</td>
                                <td>{{ $item['tanggal'] ? date('d M Y', strtotime($item['tanggal'])) : '-' }}</td>
                                <td>{{ $item['tanggal_proses'] ? date('d M Y', strtotime($item['tanggal_proses'])) : '-' }}</td>
                                <td>
                                    @php $status = strtolower((string) ($item['status'] ?? '')); @endphp
                                    @if (in_array($status, ['selesai', 'approved', 'success']))
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif (in_array($status, ['ditolak', 'rejected']))
                                        <span class="badge bg-danger">Ditolak</span>
                                    @elseif (in_array($status, ['menunggu', 'pending']))
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="gp-empty">Belum ada riwayat setor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <h2 class="gp-title">Riwayat Penarikan Saldo</h2>
            </div>
            <div class="table-responsive">
                <table class="table gp-table gp-data-table table-hover align-middle riwayat-penarikan-table" data-page-length="8">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Tanggal Proses</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penarikanList as $item)
                            <tr>
                                <td class="fw-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $item['jenis'] }}</td>
                                <td>Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                <td>
                                    <div class="riwayat-description">{{ $item['deskripsi'] }}</div>
                                </td>
                                <td>{{ $item['tanggal'] ? date('d M Y', strtotime($item['tanggal'])) : '-' }}</td>
                                <td>{{ $item['tanggal_proses'] ? date('d M Y', strtotime($item['tanggal_proses'])) : '-' }}</td>
                                <td>
                                    @php $status = strtolower((string) ($item['status'] ?? '')); @endphp
                                    @if (in_array($status, ['approved', 'selesai', 'success']))
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif (in_array($status, ['ditolak', 'rejected']))
                                        <span class="badge bg-danger">Ditolak</span>
                                    @elseif (in_array($status, ['menunggu', 'pending']))
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="gp-empty">Belum ada riwayat penarikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
