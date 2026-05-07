@extends('layouts.app')

@section('content')
<style>
    .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .report-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .report-card-header {
        margin-bottom: 16px;
    }

    .report-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .report-card-subtitle {
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
    }

    .report-panel {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .report-panel-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 12px;
    }

    .report-metric {
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-metric:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .report-metric-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
    }

    .report-metric-value {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
    }

    .report-metric-meta {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .report-list {
        display: grid;
        gap: 10px;
    }

    .report-list-item {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-list-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .report-list-name {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        min-width: 0;
    }

    .report-list-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        flex-shrink: 0;
    }

    .report-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .report-actions .btn {
        min-width: 112px;
        justify-content: center;
    }

    .filter-container {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .filter-container label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-container select {
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
    }

    @media (max-width: 640px) {
        .report-card {
            padding: 16px;
        }

        .report-actions .btn {
            width: 100%;
        }

        .filter-container {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
<div class="page-shell">
    <div class="section-header">
        <div>
            <div class="section-title">Laporan</div>
            <div class="section-subtitle">Ringkasan data keuangan, sampah, dan nasabah dalam satu tampilan.</div>
        </div>

        <div class="filter-container">
            <label for="periode"><i class="lucide-calendar"></i>Periode</label>
            <select id="periode" name="periode" class="select">
                <option value="today" {{ ($period == 'today') ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ ($period == 'week') ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ ($period == 'month') ? 'selected' : '' }}>Bulan Ini</option>
                <option value="year" {{ ($period == 'year') ? 'selected' : '' }}>Tahun Ini</option>
            </select>
        </div>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card">
            <div class="report-card-header">
                <div>
                    <div class="report-card-title">Laporan Keuangan</div>
                    <div class="report-card-subtitle">Ringkasan pemasukan dan pengeluaran.</div>
                </div>
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
                <a href="{{ route('admin.laporan.excel.keuangan', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="lucide-file-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.keuangan', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="lucide-file-text"></i>PDF</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <div>
                    <div class="report-card-title">Laporan Sampah Masuk</div>
                    <div class="report-card-subtitle">Detail jenis dan berat sampah yang masuk.</div>
                </div>
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
                        <div style="color:#64748b; font-weight:600; font-size:14px;">Tidak ada data</div>
                    @endforelse
                </div>
            </div>

            <div class="report-actions">
                <a href="{{ route('admin.laporan.excel.sampah', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="lucide-file-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.sampah', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="lucide-file-text"></i>PDF</a>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-header">
                <div>
                    <div class="report-card-title">Laporan Data Nasabah</div>
                    <div class="report-card-subtitle">Top penabung dan data kontribusi nasabah.</div>
                </div>
            </div>

            <div class="report-panel">
                <div class="report-panel-label">Top Penabung</div>
                <div class="report-list">
                    @forelse($topNasabah as $index => $nasabah)
                        <div class="report-list-item">
                            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span style="width:24px; height:24px; border-radius:999px; display:grid; place-items:center; background:#e2e8f0; color:#0f172a; font-size:12px; font-weight:700; flex-shrink:0;">{{ $index + 1 }}</span>
                                <span class="report-list-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $nasabah['nama'] }}</span>
                            </div>
                            <span class="report-list-value">{{ number_format($nasabah['berat'], 1, ',', '.') }} kg</span>
                        </div>
                    @empty
                        <div style="color:#64748b; font-weight:600; font-size:14px;">Tidak ada data</div>
                    @endforelse
                </div>
            </div>

            <div class="report-actions">
                <a href="{{ route('admin.laporan.excel.nasabah', ['periode' => $period]) }}" class="btn btn-primary" target="_blank"><i class="lucide-file-spreadsheet"></i>Excel</a>
                <a href="{{ route('admin.laporan.pdf.nasabah', ['periode' => $period]) }}" class="btn btn-danger" target="_blank"><i class="lucide-file-text"></i>PDF</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('periode').addEventListener('change', function() {
        const periode = this.value;
        window.location.href = '{{ url("/admin/laporan") }}?periode=' + periode;
    });
</script>
@endsection
