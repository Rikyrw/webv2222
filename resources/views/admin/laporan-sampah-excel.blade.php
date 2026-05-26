@php
    $totalBerat = array_sum($composition);
    $jumlahJenis = count($composition);
    $rataRataBerat = $jumlahJenis > 0 ? $totalBerat / $jumlahJenis : 0;
    $jenisMax = 'Tidak ada data';
    $beratMax = 0;

    foreach ($composition as $jenis => $berat) {
        if ($berat > $beratMax) {
            $jenisMax = $jenis;
            $beratMax = $berat;
        }
    }
@endphp
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="ProgId" content="Excel.Sheet">
    <title>Laporan Sampah Masuk</title>
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

        .rank {
            background: #eaf4ed;
            color: #275636;
            font-weight: 700;
            text-align: center;
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

        .blank td {
            border: none;
            height: 8px;
        }
    </style>
</head>
<body>
    <table class="sheet">
        <colgroup>
            <col style="width: 8%;">
            <col style="width: 44%;">
            <col style="width: 24%;">
            <col style="width: 24%;">
        </colgroup>
        <tr>
            <td colspan="4" class="title">Laporan Sampah Masuk</td>
        </tr>
        <tr>
            <td colspan="4" class="subtitle">Periode: {{ $periodLabel }} | Rentang: {{ $start }} - {{ $end }} | Dibuat: {{ $currentDate }}</td>
        </tr>
        <tr class="blank">
            <td colspan="4"></td>
        </tr>

        <tr>
            <td colspan="4" class="section">Ringkasan Sampah</td>
        </tr>
        <tr>
            <th class="head">Indikator</th>
            <th class="head" colspan="2">Keterangan</th>
            <th class="head" style="text-align: right;">Nilai</th>
        </tr>
        <tr>
            <td class="label" colspan="2">Total Berat</td>
            <td class="muted">Akumulasi semua jenis</td>
            <td class="number">{{ number_format($totalBerat, 1, ',', '.') }} kg</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Jumlah Jenis Sampah</td>
            <td class="muted">Kategori tercatat</td>
            <td class="number">{{ $jumlahJenis }} jenis</td>
        </tr>
        <tr>
            <td class="label" colspan="2">Rata-rata per Jenis</td>
            <td class="muted">Total berat dibagi jumlah jenis</td>
            <td class="number">{{ number_format($rataRataBerat, 1, ',', '.') }} kg</td>
        </tr>
        <tr class="total">
            <td colspan="2">Jenis Sampah Tertinggi</td>
            <td>{{ $jenisMax }}</td>
            <td class="number">{{ number_format($beratMax, 1, ',', '.') }} kg</td>
        </tr>

        <tr class="blank">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="4" class="section">Komposisi Sampah</td>
        </tr>
        <tr>
            <th class="head" style="text-align: center;">No</th>
            <th class="head">Jenis Sampah</th>
            <th class="head" style="text-align: right;">Total Berat (kg)</th>
            <th class="head" style="text-align: right;">Persentase</th>
        </tr>
        @forelse($composition as $jenis => $berat)
            <tr>
                <td class="rank">{{ $loop->iteration }}</td>
                <td>{{ $jenis }}</td>
                <td class="number">{{ number_format($berat, 1, ',', '.') }}</td>
                <td class="number">{{ $totalBerat > 0 ? number_format(($berat / $totalBerat) * 100, 2, ',', '.') : '0,00' }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="muted">Tidak ada data sampah pada periode ini.</td>
            </tr>
        @endforelse
        <tr class="total">
            <td colspan="2">Total Berat Sampah</td>
            <td class="number">{{ number_format($totalBerat, 1, ',', '.') }}</td>
            <td class="number">{{ $totalBerat > 0 ? '100,00' : '0,00' }}%</td>
        </tr>

        <tr class="blank">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td class="label">Dicetak</td>
            <td colspan="3">{{ date('d-m-Y H:i:s') }}</td>
        </tr>
    </table>
</body>
</html>
