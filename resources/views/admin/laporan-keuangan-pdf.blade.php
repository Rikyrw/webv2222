@php
    $rataRataSetoran = $totalSetoranCount > 0 ? $totalSetoran / $totalSetoranCount : 0;
    $saldoBersih = $totalSetoran - $totalPenarikan;
    $totalArus = $totalSetoran + $totalPenarikan;
    $persenSetoran = $totalArus > 0 ? round(($totalSetoran / $totalArus) * 100, 2) : 0;
    $persenPenarikan = $totalArus > 0 ? round(($totalPenarikan / $totalArus) * 100, 2) : 0;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        @page {
            margin: 28px;
        }

        body {
            margin: 0;
            background: #ffffff;
            color: #253128;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        h1,
        h2,
        p {
            margin: 0;
        }

        .header-table,
        .meta-table,
        .kpi-table,
        .data-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 16px;
        }

        .header-main {
            width: 62%;
            padding: 18px 20px;
            background: #2f5f3e;
            color: #ffffff;
            vertical-align: top;
        }

        .header-main h1 {
            font-size: 23px;
            letter-spacing: 0;
            line-height: 1.15;
            font-weight: 700;
        }

        .header-main p {
            margin-top: 7px;
            color: #dcebe0;
            font-size: 12px;
        }

        .header-meta {
            width: 38%;
            padding: 14px 16px;
            background: #eef7f0;
            border: 1px solid #cfe0d3;
            vertical-align: top;
        }

        .meta-table td {
            padding: 5px 0;
            border-bottom: 1px solid #d9e7dc;
            color: #53645a;
            font-size: 10px;
        }

        .meta-table tr:last-child td {
            border-bottom: none;
        }

        .meta-table .meta-value {
            color: #1f3526;
            font-weight: 700;
            text-align: right;
        }

        .section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .section-title {
            padding: 8px 10px;
            margin-bottom: 8px;
            background: #f3f7f4;
            border-left: 4px solid #2f5f3e;
            color: #18351f;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .kpi-table {
            margin-bottom: 4px;
        }

        .kpi-table td {
            width: 50%;
            padding: 11px 12px;
            border: 1px solid #dfe8e1;
            vertical-align: top;
        }

        .kpi-label {
            color: #637266;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .kpi-value {
            margin-top: 5px;
            color: #16251a;
            font-size: 17px;
            font-weight: 700;
        }

        .kpi-note {
            margin-top: 4px;
            color: #6d7d70;
            font-size: 10px;
        }

        .kpi-green {
            background: #f1f8f3;
        }

        .kpi-blue {
            background: #f0f7fb;
        }

        .kpi-amber {
            background: #fff8eb;
        }

        .kpi-red {
            background: #fff3f1;
        }

        .data-table th {
            padding: 9px 10px;
            background: #2f5f3e;
            border: 1px solid #2f5f3e;
            color: #ffffff;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 9px 10px;
            border: 1px solid #dfe8e1;
            color: #2b372e;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background: #fbfdfb;
        }

        .data-table .value {
            color: #142418;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .data-table .total td {
            background: #eaf4ed;
            color: #18351f;
            font-weight: 700;
        }

        .positive {
            color: #24613a;
        }

        .negative {
            color: #b42318;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #eaf4ed;
            color: #275636;
            font-size: 10px;
            font-weight: 700;
        }

        .footer-table {
            margin-top: 16px;
            border-top: 1px solid #dfe8e1;
        }

        .footer-table td {
            padding-top: 9px;
            color: #768579;
            font-size: 10px;
        }

        .footer-table .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="header-main">
                <h1>Laporan Keuangan</h1>
                <p>Ringkasan pemasukan, penarikan, dan saldo bank sampah.</p>
            </td>
            <td class="header-meta">
                <table class="meta-table">
                    <tr>
                        <td>Periode</td>
                        <td class="meta-value">{{ $periodLabel }}</td>
                    </tr>
                    <tr>
                        <td>Rentang</td>
                        <td class="meta-value">{{ $start }} - {{ $end }}</td>
                    </tr>
                    <tr>
                        <td>Dibuat</td>
                        <td class="meta-value">{{ $currentDate }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Ringkasan Utama</div>
        <table class="kpi-table">
            <tr>
                <td class="kpi-green">
                    <div class="kpi-label">Total Setoran Sampah</div>
                    <div class="kpi-value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</div>
                    <div class="kpi-note">{{ $totalSetoranCount }} transaksi selesai</div>
                </td>
                <td class="kpi-red">
                    <div class="kpi-label">Total Penarikan Tunai</div>
                    <div class="kpi-value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</div>
                    <div class="kpi-note">Penarikan dengan status selesai</div>
                </td>
            </tr>
            <tr>
                <td class="kpi-blue">
                    <div class="kpi-label">Saldo Akhir Nasabah Aktif</div>
                    <div class="kpi-value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                    <div class="kpi-note">Akumulasi saldo seluruh nasabah aktif</div>
                </td>
                <td class="kpi-amber">
                    <div class="kpi-label">Saldo Bersih Periode</div>
                    <div class="kpi-value {{ $saldoBersih >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</div>
                    <div class="kpi-note">Setoran dikurangi penarikan</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detail Keuangan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 38%;">Indikator</th>
                    <th style="width: 24%; text-align: right;">Nilai</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Setoran Sampah</td>
                    <td class="value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
                    <td><span class="badge">{{ $totalSetoranCount }} transaksi</span></td>
                </tr>
                <tr>
                    <td>Rata-rata per Transaksi</td>
                    <td class="value">Rp {{ number_format($rataRataSetoran, 0, ',', '.') }}</td>
                    <td>Total setoran dibagi jumlah transaksi selesai.</td>
                </tr>
                <tr>
                    <td>Total Penarikan Tunai</td>
                    <td class="value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
                    <td>Penarikan nasabah pada periode terpilih.</td>
                </tr>
                <tr>
                    <td>Saldo Akhir</td>
                    <td class="value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                    <td>Total saldo nasabah aktif saat laporan dibuat.</td>
                </tr>
                <tr class="total">
                    <td>Saldo Bersih Periode</td>
                    <td class="value {{ $saldoBersih >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</td>
                    <td>Selisih setoran dan penarikan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Komposisi Arus Kas</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Jenis Arus Kas</th>
                    <th style="text-align: right;">Nominal</th>
                    <th style="text-align: right;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Setoran</td>
                    <td class="value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
                    <td class="value">{{ number_format($persenSetoran, 2, ',', '.') }}%</td>
                </tr>
                <tr>
                    <td>Penarikan</td>
                    <td class="value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
                    <td class="value">{{ number_format($persenPenarikan, 2, ',', '.') }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="footer-table">
        <tr>
            <td>Dokumen dibuat otomatis oleh sistem Bank Sampah.</td>
            <td class="right">{{ date('d-m-Y H:i:s') }}</td>
        </tr>
    </table>
</body>
</html>
