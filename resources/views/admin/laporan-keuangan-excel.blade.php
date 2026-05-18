<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #64748b;
            margin-bottom: 10px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
        }
        th {
            background: #e2e8f0;
            text-align: left;
            font-weight: 700;
        }
        .section-header {
            background: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
        }
        .value {
            text-align: right;
            font-weight: 700;
        }
        .muted {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Keuangan</div>
    <div class="subtitle">Periode: {{ $periodLabel }} ({{ $start }} hingga {{ $end }})</div>
    <div class="subtitle muted">Dibuat pada {{ $currentDate }}</div>

    <table>
        <tr class="section-header">
            <td colspan="3">Ringkasan Keuangan</td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <th>Jumlah</th>
            <th>Catatan</th>
        </tr>
        <tr>
            <td>Total Setoran Sampah</td>
            <td class="value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
            <td>{{ $totalSetoranCount }} transaksi</td>
        </tr>
        <tr>
            <td>Rata-rata per Transaksi</td>
            <td class="value">Rp {{ number_format($totalSetoran / ($totalSetoranCount > 0 ? $totalSetoranCount : 1), 0, ',', '.') }}</td>
            <td class="muted">Total setoran dibagi jumlah transaksi</td>
        </tr>
        <tr>
            <td>Total Penarikan</td>
            <td class="value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
            <td class="muted">Penarikan tunai nasabah</td>
        </tr>
        <tr>
            <td>Saldo Akhir</td>
            <td class="value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            <td class="muted">Saldo aktif seluruh nasabah</td>
        </tr>
        <tr>
            <td>Saldo Bersih (Setoran - Penarikan)</td>
            <td class="value">Rp {{ number_format($totalSetoran - $totalPenarikan, 0, ',', '.') }}</td>
            <td class="muted">Estimasi saldo bersih</td>
        </tr>
    </table>
</body>
</html>
