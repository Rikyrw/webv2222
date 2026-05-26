@php
    $totalBerat = array_sum(array_column($topNasabah, 'berat'));
    $jumlahNasabah = count($topNasabah);
    $rataRataBerat = $jumlahNasabah > 0 ? $totalBerat / $jumlahNasabah : 0;
    $topNama = 'Tidak ada data';
    $topBerat = 0;

    foreach ($topNasabah as $nasabah) {
        if ($nasabah['berat'] > $topBerat) {
            $topNama = $nasabah['nama'];
            $topBerat = $nasabah['berat'];
        }
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Nasabah</title>
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
            width: 33.33%;
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

        .data-table .number {
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

        .rank {
            display: inline-block;
            width: 24px;
            padding: 3px 0;
            background: #eaf4ed;
            color: #275636;
            font-size: 10px;
            font-weight: 700;
            text-align: center;
        }

        .empty {
            padding: 18px 10px;
            color: #768579;
            text-align: center;
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
                <h1>Laporan Data Nasabah</h1>
                <p>Peringkat nasabah berdasarkan kontribusi berat sampah pada periode laporan.</p>
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
        <div class="section-title">Ringkasan Kontribusi</div>
        <table class="kpi-table">
            <tr>
                <td class="kpi-green">
                    <div class="kpi-label">Total Kontribusi</div>
                    <div class="kpi-value">{{ number_format($totalBerat, 1, ',', '.') }} kg</div>
                    <div class="kpi-note">Akumulasi kontribusi top nasabah</div>
                </td>
                <td class="kpi-blue">
                    <div class="kpi-label">Jumlah Nasabah</div>
                    <div class="kpi-value">{{ $jumlahNasabah }} orang</div>
                    <div class="kpi-note">Nasabah dalam daftar peringkat</div>
                </td>
                <td class="kpi-amber">
                    <div class="kpi-label">Rata-rata</div>
                    <div class="kpi-value">{{ number_format($rataRataBerat, 1, ',', '.') }} kg</div>
                    <div class="kpi-note">Rata-rata kontribusi per nasabah</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Top Penabung</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 54px;">Rank</th>
                    <th>Nama Nasabah</th>
                    <th style="width: 130px; text-align: right;">Berat Sampah</th>
                    <th style="width: 110px; text-align: right;">Kontribusi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topNasabah as $index => $nasabah)
                    <tr>
                        <td><span class="rank">{{ $index + 1 }}</span></td>
                        <td>{{ $nasabah['nama'] }}</td>
                        <td class="number">{{ number_format($nasabah['berat'], 1, ',', '.') }} kg</td>
                        <td class="number">{{ $totalBerat > 0 ? number_format(($nasabah['berat'] / $totalBerat) * 100, 2, ',', '.') : '0,00' }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">Tidak ada data nasabah pada periode ini.</td>
                    </tr>
                @endforelse
                <tr class="total">
                    <td colspan="2">Total Kontribusi</td>
                    <td class="number">{{ number_format($totalBerat, 1, ',', '.') }} kg</td>
                    <td class="number">{{ $totalBerat > 0 ? '100,00' : '0,00' }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Catatan Statistik</div>
        <table class="data-table">
            <tbody>
                <tr>
                    <td style="width: 42%;">Kontribusi tertinggi</td>
                    <td>{{ $topNama }}</td>
                    <td class="number">{{ number_format($topBerat, 1, ',', '.') }} kg</td>
                </tr>
                <tr>
                    <td>Rata-rata kontribusi</td>
                    <td colspan="2" class="number">{{ number_format($rataRataBerat, 1, ',', '.') }} kg</td>
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
