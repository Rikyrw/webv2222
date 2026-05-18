@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-6 fw-bold mb-2">Riwayat Nasabah</h1>
            <p class="text-muted">Ringkasan transaksi untuk {{ $nasabah['nama_lengkap'] ?? 'Nasabah' }}.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">
                <i class="lucide-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if (!empty($databaseError))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    {{ $databaseError }}
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Riwayat Setor Sampah</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
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
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat setor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Riwayat Penarikan Saldo</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
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
                                        <td style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item['deskripsi'] }}">
                                            {{ $item['deskripsi'] }}
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
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat penarikan.</td>
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
@endsection
