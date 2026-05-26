@php
    $rataRataSetoran = $totalSetoranCount > 0 ? $totalSetoran / $totalSetoranCount : 0;
    $saldoBersih = $totalSetoran - $totalPenarikan;
    $totalArus = $totalSetoran + $totalPenarikan;
    $persenSetoran = $totalArus > 0 ? round(($totalSetoran / $totalArus) * 100, 2) : 0;
    $persenPenarikan = $totalArus > 0 ? round(($totalPenarikan / $totalArus) * 100, 2) : 0;
@endphp
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="ProgId" content="Excel.Sheet">
    <title>Laporan Keuangan</title>
    <style>
        body {
            margin: 0;
            font-family: Calibri, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #253128;
        }

        table.sheet {
            width: 100%;
            border-collapse: collapse;
        }

        .sheet th,
        .sheet td {
            border: 1px solid #d9e7dc;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .title {
            background: #2f5f3e;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            height: 34px;
        }

        .subtitle {
            background: #eef7f0;
            color: #405246;
            font-weight: 600;
        }

        .section {
            background: #dcefe2;
            color: #18351f;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .head {
            background: #2f5f3e;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
        }

        .label {
            font-weight: 600;
        }

        .number {
            text-align: right;
            font-weight: 700;
            mso-number-format: "\@";
        }

        .muted {
            color: #647267;
        }

        .total td,
        .total {
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

        .blank td {
            border: none;
            height: 8px;
        }
    </style>
</head>
<body>
    <table class="sheet">
        <colgroup>
            <col style="width: 34%;">
            <col style="width: 24%;">
            <col style="width: 24%;">
            <col style="width: 18%;">
        </colgroup>
        <tr>
            <td colspan="4" class="title">Laporan Keuangan</td>
        </tr>
        <tr>
            <td colspan="4" class="subtitle">Periode: {{ $periodLabel }} | Rentang: {{ $start }} - {{ $end }} | Dibuat: {{ $currentDate }}</td>
        </tr>
        <tr class="blank">
            <td colspan="4"></td>
        </tr>

        <tr>
            <td colspan="4" class="section">Ringkasan Utama</td>
        </tr>
        <tr>
            <th class="head">Indikator</th>
            <th class="head" style="text-align: right;">Nilai</th>
            <th class="head">Catatan</th>
            <th class="head" style="text-align: right;">Persentase</th>
        </tr>
        <tr>
            <td class="label">Total Setoran Sampah</td>
            <td class="number">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
            <td>{{ $totalSetoranCount }} transaksi selesai</td>
            <td class="number">{{ number_format($persenSetoran, 2, ',', '.') }}%</td>
        </tr>
        <tr>
            <td class="label">Total Penarikan Tunai</td>
            <td class="number">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
            <td class="muted">Penarikan status selesai</td>
            <td class="number">{{ number_format($persenPenarikan, 2, ',', '.') }}%</td>
        </tr>
        <tr>
            <td class="label">Saldo Akhir Nasabah Aktif</td>
            <td class="number">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            <td class="muted">Total saldo saat laporan dibuat</td>
            <td class="number">-</td>
        </tr>
        <tr class="total">
            <td>Saldo Bersih Periode</td>
            <td class="number {{ $saldoBersih >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</td>
            <td>Setoran dikurangi penarikan</td>
            <td class="number">-</td>
        </tr>

        <tr class="blank">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="4" class="section">Detail Perhitungan</td>
        </tr>
        <tr>
            <th class="head">Deskripsi</th>
            <th class="head" style="text-align: right;">Jumlah</th>
            <th class="head" colspan="2">Keterangan</th>
        </tr>
        <tr>
            <td class="label">Rata-rata per Transaksi</td>
            <td class="number">Rp {{ number_format($rataRataSetoran, 0, ',', '.') }}</td>
            <td colspan="2" class="muted">Total setoran dibagi jumlah transaksi selesai</td>
        </tr>
        <tr>
            <td class="label">Total Arus Kas</td>
            <td class="number">Rp {{ number_format($totalArus, 0, ',', '.') }}</td>
            <td colspan="2" class="muted">Setoran ditambah penarikan</td>
        </tr>
        <tr>
            <td class="label">Dicetak Pada</td>
            <td colspan="3">{{ date('d-m-Y H:i:s') }}</td>
        </tr>
    </table>
</body>
</html>
