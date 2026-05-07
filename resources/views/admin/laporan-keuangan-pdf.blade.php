<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #d1d5db;
        }

        .header h1 {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 700;
        }

        .header p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            align-items: center;
            gap: 16px;
        }

        .info-label {
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
        }

        .info-value {
            color: #0f172a;
            font-weight: 700;
            font-size: 14px;
            text-align: right;
        }

        .total-row {
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            margin-top: 8px;
        }

        .footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>Ringkasan pemasukan dan pengeluaran</p>
        <p>Periode: {{ ucfirst($period) }} | {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Keuangan</div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Total Setoran Sampah</span>
                <span class="info-value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Jumlah Transaksi Setoran</span>
                <span class="info-value">{{ $totalSetoranCount }} transaksi</span>
            </div>

            <div class="info-row">
                <span class="info-label">Rata-rata per Transaksi</span>
                <span class="info-value">Rp {{ number_format($totalSetoran / ($totalSetoranCount > 0 ? $totalSetoranCount : 1), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Transaksi</div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Total Penarikan Tunai</span>
                <span class="info-value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Saldo Akhir</span>
                <span class="info-value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</span>
            </div>

            <div class="info-row total-row">
                <span class="info-label">Saldo Bersih (Setoran - Penarikan)</span>
                <span class="info-value">Rp {{ number_format($totalSetoran - $totalPenarikan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Persentase Aliran Kas</div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Persentase Setoran dari Total</span>
                <span class="info-value">{{ round(($totalSetoran / ($totalSetoran + $totalPenarikan)) * 100, 2) }}%</span>
            </div>

            <div class="info-row">
                <span class="info-label">Persentase Penarikan dari Total</span>
                <span class="info-value">{{ round(($totalPenarikan / ($totalSetoran + $totalPenarikan)) * 100, 2) }}%</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat otomatis pada {{ date('d-m-Y H:i:s') }}.</p>
    </div>
</body>
</html>
