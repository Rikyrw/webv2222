<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GreenPoint • Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #d2d7dd;
            color: #1f2937;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            margin-left: 280px;
            padding: 24px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .header-content h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .header-content p {
            color: #6b7280;
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .grid.grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid.grid-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .grid.grid-2 {
                grid-template-columns: 1fr;
            }

            .grid.grid-full {
                grid-column: auto;
            }
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .metric .value {
            font-size: 32px;
            font-weight: 700;
            color: #10b981;
            display: block;
            margin-bottom: 4px;
        }

        .metric .delta {
            font-size: 12px;
            color: #10b981;
            display: block;
        }

        .ppob-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .ppob-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .ppob-card {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px;
            background: transparent;
            color: #059669;
            border: 1px solid #059669;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            flex: 0 0 calc(33.333% - 11px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ppob-card:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .ppob-card:active {
            transform: scale(0.98);
            transition: all 0.1s ease;
        }

        .ppob-card img {
            width: 48px;
            height: 48px;
            margin-bottom: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ppob-card:hover img {
            transform: scale(1.15);
            filter: brightness(0) invert(1);
        }

        .ppob-card span {
            font-size: 13px;
            font-weight: 500;
            transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ppob-card i, 
        .ppob-card svg {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ppob-card:hover i,
        .ppob-card:hover svg {
            color: white;
            transform: scale(1.15);
        }

        .transaction-list {
            list-style: none;
            space-y: 12px;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border: 1px solid #e5e7eb;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .transaction-item:last-child {
            margin-bottom: 0;
        }

        .transaction-info div {
            margin-bottom: 4px;
        }

        .transaction-info .id {
            font-size: 12px;
            color: #6b7280;
        }

        .transaction-info .title {
            font-weight: 500;
            color: #1f2937;
            font-size: 14px;
        }

        .transaction-info .desc {
            font-size: 12px;
            color: #6b7280;
        }

        .transaction-right {
            text-align: right;
        }

        .transaction-right .date {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .transaction-right .amount {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            color: #6b7280;
            font-size: 14px;
            text-align: center;
            padding: 32px;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 16px;
            }

            .ppob-card {
                flex: 0 0 calc(50% - 8px);
            }

            .header-content h2 {
                font-size: 24px;
            }

            .grid {
                gap: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <!-- SIDEBAR -->
        @include('partials.sidebarNasabah')

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="page-header">
                <div class="header-content">
                    <h2>Dashboard</h2>
                    <p>Selamat datang, {{ htmlspecialchars($user_name ?? 'User') }}! di sistem manajemen bank sampah</p>
                </div>
            </div>
            

            <!-- TOP CARDS -->
            <div class="grid grid-2">
                <!-- Card 1: Transaksi Setor Sampah -->
                <div class="card">
                    <h3>Transaksi Setor Sampah</h3>
                    <div class="metric">
                        <span class="value">{{ number_format((int)($setor_count ?? 0), 0, ',', '.') }}</span>
                        <span class="delta">+12 dari bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Transaksi PPOB -->
                <div class="card">
                    <h3>Transaksi PPOB</h3>
                    <div class="metric">
                        <span class="value">Rp {{ number_format((float)($ppob_total ?? 0), 0, ',', '.') }}</span>
                        <span class="delta">+12% dari bulan lalu</span>
                    </div>
                </div>
            </div>

            <!-- PPOB SECTION -->
            <div class="card" style="margin-bottom: 24px;">
    <div class="ppob-section">
        <h3>PPOB</h3>
        <div class="ppob-cards">
            <a href="{{ route('nasabah.emoney') }}" class="ppob-card" title="E money">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="10" width="36" height="26" rx="2" stroke="currentColor" stroke-width="2"/>
                    <line x1="6" y1="18" x2="42" y2="18" stroke="currentColor" stroke-width="2"/>
                    <circle cx="36" cy="29" r="3" fill="currentColor"/>
                </svg>
                <span>E money</span>
            </a>
            
            <a href="{{ route('nasabah.pulsa') }}" class="ppob-card" title="Pulsa">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z" fill="currentColor"/>
                    <path d="M24 10c-7.73 0-14 6.27-14 14s6.27 14 14 14 14-6.27 14-14-6.27-14-14-14zm0 24c-5.52 0-10-4.48-10-10s4.48-10 10-10 10 4.48 10 10-4.48 10-10 10z" fill="white"/>
                </svg>
                <span>Pulsa</span>
            </a>
            
            <a href="{{ route('nasabah.pln') }}" class="ppob-card" title="PLN">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 2L4 10v14c0 12 20 20 20 20s20-8 20-20V10L24 2zm0 28l-12-8v-8l12-6 12 6v8l-12 8z" fill="currentColor"/>
                </svg>
                <span>PLN</span>
            </a>
        </div>
    </div>
</div>

            <!-- RECENT TRANSACTIONS -->
            <div class="grid grid-2">
                <!-- Recent Setor -->
                <div class="card">
                    <h3>Transaksi Setor Terbaru</h3>
                    @if (empty($recent_setor))
                        <div class="empty-state">Belum ada transaksi setor.</div>
                    @else
                        <ul class="transaction-list">
                            @foreach ($recent_setor as $rs)
                                @php
                                    // Ensure $rs is an array (Supabase responses may sometimes be JSON strings)
                                    if (!is_array($rs)) {
                                        $decoded = json_decode($rs, true);
                                        if (is_array($decoded)) {
                                            $rs = $decoded;
                                        } else {
                                            // fallback: create minimal structure to avoid errors
                                            $rs = [
                                                'id_transaksi' => (string)$rs,
                                                'total_berat' => 0,
                                                'total_nilai' => 0,
                                                'tanggal_setor' => null,
                                                'status' => null,
                                            ];
                                        }
                                    }
                                    $dateVal = $rs['tanggal_setor'] ?? $rs['created_at'] ?? null;
                                    $statusVal = $rs['status'] ?? null;
                                @endphp
                                <li class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="id">ID: {{ htmlspecialchars($rs['id_transaksi'] ?? '-') }}</div>
                                        <div class="title">Total Berat: {{ htmlspecialchars(number_format((float)($rs['total_berat'] ?? 0), 2, ',', '.')) }} kg</div>
                                        <div class="desc">Nilai: Rp {{ number_format((float)($rs['total_nilai'] ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                    <div class="transaction-right">
                                        <div class="date">{{ $dateVal ? \Carbon\Carbon::parse($dateVal)->format('d M Y') : '-' }}</div>
                                        <div class="badge badge-{{ strtolower($statusVal ?? 'pending') == 'selesai' ? 'success' : 'pending' }}">
                                            {{ ucfirst($statusVal ?? 'menunggu') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Recent PPOB -->
                <div class="card">
                    <h3>Transaksi PPOB Terbaru</h3>
                    @if (empty($recent_ppob))
                        <div class="empty-state">Belum ada transaksi PPOB.</div>
                    @else
                        <ul class="transaction-list">
                            @foreach ($recent_ppob as $it)
                                @php
                                    if (!is_array($it)) {
                                        $decoded = json_decode($it, true);
                                        if (is_array($decoded)) {
                                            $it = $decoded;
                                        } else {
                                            $it = ['service' => (string)$it, 'deskripsi' => '', 'amount' => 0, 'created_at' => null];
                                        }
                                    }
                                    $createdVal = $it['created_at'] ?? $it['tanggal_pengajuan'] ?? null;
                                @endphp
                                <li class="transaction-item">
                                    <div class="transaction-info">
                                        <div class="title">{{ htmlspecialchars($it['service'] ?? '-') }}</div>
                                        <div class="desc">{{ htmlspecialchars($it['deskripsi'] ?? '') }}</div>
                                    </div>
                                    <div class="transaction-right">
                                        <div class="amount">Rp {{ number_format((float)($it['amount'] ?? 0), 0, ',', '.') }}</div>
                                        <div class="date">{{ $createdVal ? \Carbon\Carbon::parse($createdVal)->format('d M Y') : '-' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
