<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Nasabah</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
        }

        td {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            color: #1f2937;
        }

        tr:nth-child(even) {
            background: #fcfcfd;
        }

        .rank {
            display: inline-block;
            min-width: 24px;
            padding: 3px 8px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #0f172a;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
        }

        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 10px;
        }

        .info-label {
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
        }

        .info-value {
            color: #0f172a;
            font-weight: 700;
            font-size: 13px;
            text-align: right;
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
        <h1>Laporan Data Nasabah</h1>
        <p>Top penabung dan data kontribusi nasabah</p>
        <p>Periode: {{ ucfirst($period) }} | {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Top 5 Penabung</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Rank</th>
                    <th>Nama Nasabah</th>
                    <th style="text-align: right; width: 150px;">Berat Sampah (kg)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBerat = 0;
                @endphp
                @forelse($topNasabah as $index => $nasabah)
                    @php
                        $totalBerat += $nasabah['berat'];
                    @endphp
                    <tr>
                        <td><span class="rank">{{ $index + 1 }}</span></td>
                        <td>{{ $nasabah['nama'] }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($nasabah['berat'], 1, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">Tidak ada data</td>
                    </tr>
                @endforelse
                @if($topNasabah)
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td colspan="2">Total Kontribusi Top Nasabah</td>
                        <td style="text-align: right;">{{ number_format($totalBerat, 1, ',', '.') }} kg</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Statistik Kontribusi</div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Jumlah Nasabah (Top 5)</span>
                <span class="info-value">{{ count($topNasabah) }} nasabah</span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Kontribusi</span>
                <span class="info-value">{{ number_format($totalBerat, 1, ',', '.') }} kg</span>
            </div>

            <div class="info-row">
                <span class="info-label">Rata-rata Kontribusi</span>
                <span class="info-value">{{ number_format($totalBerat / (count($topNasabah) > 0 ? count($topNasabah) : 1), 1, ',', '.') }} kg</span>
            </div>

            @if($topNasabah)
                @php
                    $maxBerat = max(array_column($topNasabah, 'berat'));
                    $topNama = '';
                    foreach($topNasabah as $nasabah) {
                        if($nasabah['berat'] == $maxBerat) {
                            $topNama = $nasabah['nama'];
                            break;
                        }
                    }
                @endphp
                <div class="info-row">
                    <span class="info-label">Kontribusi Tertinggi</span>
                    <span class="info-value">{{ $topNama }} ({{ number_format($maxBerat, 1, ',', '.') }} kg)</span>
                </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Persentase Kontribusi</div>

        <div class="info-box">
            @foreach($topNasabah as $index => $nasabah)
                <div class="info-row">
                    <span class="info-label">{{ $index + 1 }}. {{ $nasabah['nama'] }}</span>
                    <span class="info-value">{{ round(($nasabah['berat'] / $totalBerat) * 100, 2) }}% ({{ number_format($nasabah['berat'], 1, ',', '.') }} kg)</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat otomatis pada {{ date('d-m-Y H:i:s') }}.</p>
    </div>
</body>
</html>
