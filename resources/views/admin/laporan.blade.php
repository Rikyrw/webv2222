@extends('layouts.app')

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Filter Laporan</h2>
                    <p class="gp-subtitle mb-0">Pilih periode sebelum mengunduh laporan.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="periode" class="form-label mb-0"><i class="bi bi-calendar3"></i> Periode</label>
                    <select id="periode" name="periode" class="form-select form-select-sm" style="min-width: 170px;">
                        <option value="today" {{ ($period == 'today') ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ ($period == 'week') ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ ($period == 'month') ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ ($period == 'year') ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card">
            <div class="report-card-header">
                <div class="report-card-title">Laporan Keuangan</div>
                <div class="report-card-subtitle">Ringkasan pemasukan dan pengeluaran.</div>
            </div>

            <div class="report-panel">
                <div class="report-panel-label">Ringkasan</div>
                <div class="report-list">
                    <div class="report-metric">
                        <div class="report-metric-label">Total Setoran</div>
                        <div class="report-metric-value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</div>
                        <div class="report-metric-meta">{{ $totalSetoranCount }} transaksi</div>
                    </div>
                    <div class="report-metric">
                        <div class="report-metric-label">Total Penarikan</div>
                        <div class="report-metric-value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</div>
                    </div>
                    <div class="report-metric">
                        <div class="report-metric-label">Saldo Akhir</div>
                        <div class="report-metric-value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="report-actions">
                <a href="{{ route('admin.laporan.excel.keuangan', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="bi bi-file-earmark-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.keuangan', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <div class="report-card-title">Laporan Sampah Masuk</div>
                <div class="report-card-subtitle">Detail jenis dan berat sampah yang masuk.</div>
            </div>

            <div class="report-panel">
                <div class="report-panel-label">Komposisi Sampah</div>
                <div class="report-list">
                    @forelse($composition as $jenis => $berat)
                        <div class="report-list-item">
                            <span class="report-list-name">{{ $jenis }}</span>
                            <span class="report-list-value">{{ number_format($berat, 1, ',', '.') }} kg</span>
                        </div>
                    @empty
                        <div class="gp-empty">Tidak ada data</div>
                    @endforelse
                </div>
            </div>

            <div class="report-actions">
                <a href="{{ route('admin.laporan.excel.sampah', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="bi bi-file-earmark-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.sampah', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <div class="report-card-title">Laporan Data Nasabah</div>
                <div class="report-card-subtitle">Top penabung dan data kontribusi nasabah.</div>
            </div>

            <div class="report-panel">
                <div class="report-panel-label">Top Penabung</div>
                <div class="report-list">
                    @forelse($topNasabah as $index => $nasabah)
                        <div class="report-list-item">
                            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span class="rank-dot">{{ $index + 1 }}</span>
                                <span class="report-list-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $nasabah['nama'] }}</span>
                            </div>
                            <span class="report-list-value">{{ number_format($nasabah['berat'], 1, ',', '.') }} kg</span>
                        </div>
                    @empty
                        <div class="gp-empty">Tidak ada data</div>
                    @endforelse
                </div>
            </div>

            <div class="report-actions">
                <a href="{{ route('admin.laporan.excel.nasabah', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="bi bi-file-earmark-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.nasabah', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="bi bi-file-earmark-pdf"></i>PDF</a>
            </div>
        </div>
    </div>
</div>

<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .report-card {
        padding: 20px;
    }

    .report-card-header {
        margin-bottom: 16px;
    }

    .report-panel {
        background: #fbfcfb;
        border: 1px solid #dfe7e1;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .report-panel-label,
    .report-metric-label {
        color: #6d7a71;
        font-size: 12px;
        font-weight: 800;
    }

    .report-metric {
        padding: 10px 0;
        border-bottom: 1px solid #e8eee9;
    }

    .report-metric:last-child,
    .report-list-item:last-child {
        border-bottom: 0;
    }

    .report-metric-value,
    .report-list-value {
        color: #17231b;
        font-size: 14px;
        font-weight: 800;
    }

    .report-metric-meta {
        color: #6d7a71;
        font-size: 12px;
        margin-top: 3px;
    }

    .report-list {
        display: grid;
        gap: 6px;
    }

    .report-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #e8eee9;
    }

    .report-list-name {
        color: #17231b;
        font-size: 13px;
        font-weight: 700;
    }

    .report-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rank-dot {
        display: grid;
        place-items: center;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #edf5ef;
        color: #2f5f3e;
        font-size: 12px;
        font-weight: 800;
        flex-shrink: 0;
    }
</style>

<script>
    document.getElementById('periode').addEventListener('change', function() {
        window.location.href = '{{ url("/admin/laporan") }}?periode=' + this.value;
    });
</script>
@endsection
