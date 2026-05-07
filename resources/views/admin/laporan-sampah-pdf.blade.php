<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sampah Masuk</title>
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

        .composition-box,
        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .composition-row,
        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            padding: 11px 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .composition-row:last-child,
        .info-row:last-child {
            border-bottom: none;
        }

        .composition-row:nth-child(even),
        .info-row:nth-child(even) {
            background: #fcfcfd;
        }

        .composition-label,
        .info-label {
            font-weight: 600;
            color: #1f2937;
            font-size: 13px;
        }

        .composition-value,
        .info-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
            text-align: right;
            flex-shrink: 0;
        }

        .total-row {
            background: #f8fafc;
            font-weight: 700;
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
        <h1>Laporan Sampah Masuk</h1>
        <p>Detail jenis dan berat sampah yang masuk</p>
        <p>Periode: {{ ucfirst($period) }} | {{ date('d-m-Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Komposisi Sampah</div>

        <div class="composition-box">
            @php
                $totalBerat = array_sum($composition);
                $jenisMax = array_key_first($composition);
                $beratMax = max($composition);
                foreach($composition as $jenis => $berat) {
                    if ($berat == $beratMax) {
                        $jenisMax = $jenis;
                        break;
                    }
                }
            @endphp

            @foreach($composition as $jenis => $berat)
                <div class="composition-row">
                    <span class="composition-label">{{ $jenis }}</span>
                    <span class="composition-value">{{ number_format($berat, 1, ',', '.') }} kg</span>
                </div>
            @endforeach

            <div class="composition-row total-row">
                <span class="composition-label">Total Berat Sampah</span>
                <span class="composition-value">{{ number_format($totalBerat, 1, ',', '.') }} kg</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statistik Sampah</div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Jenis Sampah</span>
                <span class="info-value">{{ count($composition) }} jenis</span>
            </div>

            <div class="info-row">
                <span class="info-label">Total Berat</span>
                <span class="info-value">{{ number_format($totalBerat, 1, ',', '.') }} kg</span>
            </div>

            <div class="info-row">
                <span class="info-label">Rata-rata Berat per Jenis</span>
                <span class="info-value">{{ number_format($totalBerat / count($composition), 1, ',', '.') }} kg</span>
            </div>

            <div class="info-row">
                <span class="info-label">Sampah Tertinggi</span>
                <span class="info-value">{{ $jenisMax }} ({{ number_format($beratMax, 1, ',', '.') }} kg)</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Persentase Komposisi</div>

        <div class="info-box">
            @foreach($composition as $jenis => $berat)
                <div class="info-row">
                    <span class="info-label">{{ $jenis }}</span>
                    <span class="info-value">{{ round(($berat / $totalBerat) * 100, 2) }}%</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat otomatis pada {{ date('d-m-Y H:i:s') }}.</p>
    </div>
</body>
</html>
