<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Nasabah</title>
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
        .value {
            text-align: right;
            font-weight: 700;
        }
        .section-header {
            background: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
        }
        .muted {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Data Nasabah</div>
    <div class="subtitle">Periode: {{ $periodLabel }} ({{ $start }} hingga {{ $end }})</div>
    <div class="subtitle muted">Dibuat pada {{ $currentDate }}</div>

    <table>
        <tr class="section-header">
            <td colspan="3">Top Penabung</td>
        </tr>
        <tr>
            <th style="width: 60px;">Rank</th>
            <th>Nama Nasabah</th>
            <th style="width: 160px; text-align: right;">Total Berat (kg)</th>
        </tr>
        @php
            $totalBerat = 0;
        @endphp
        @forelse($topNasabah as $index => $nasabah)
            @php
                $totalBerat += $nasabah['berat'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $nasabah['nama'] }}</td>
                <td class="value">{{ number_format($nasabah['berat'], 1, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="muted">Tidak ada data</td>
            </tr>
        @endforelse
        <tr class="section-header">
            <td colspan="2">Total Kontribusi</td>
            <td class="value">{{ number_format($totalBerat, 1, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
