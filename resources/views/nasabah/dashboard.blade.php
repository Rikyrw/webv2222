<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GreenPoint - Dashboard</title>

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

        .metric-card {
            position: relative;
            border-left: 6px solid #2b6844;
        }

        .metric-icon {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 40px;
            height: 40px;
            padding: 10px;
            border-radius: 8px;
            background: #edf3ee;
            object-fit: contain;
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
            width: 88px;
            height: 88px;
            padding: 24px;
            margin-bottom: 0;
            object-fit: contain;
            background: transparent;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            filter: brightness(0) saturate(100%) invert(38%) sepia(79%) saturate(398%) hue-rotate(126deg) brightness(100%) contrast(87%);
        }

        .ppob-card:hover img {
            transform: translateY(-2px);
            color: white;
            filter: brightness(0) saturate(100%) invert(100%) sepia(100%) saturate(10000%) hue-rotate(0deg) brightness(110%) contrast(101%);
        }

        .ppob-card:active img {
            transform: scale(0.98);
            transition: all 0.1s ease;
        }

        .ppob-card span {
            font-size: 13px;
            font-weight: 500;
            transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    @include('partials.greenpoint-theme')
</head>

<body>
    <div class="app">
        <!-- SIDEBAR -->
        @include('partials.sidebarNasabah')

        <!-- MAIN CONTENT -->
        <main class="main">
            @include('partials.nasabah-header', [
                'title' => 'Dashboard',
                'subtitle' => 'Selamat datang, ' . ($user_name ?? 'User') . '! Kelola aktivitas GreenPoint Anda.',
            ])
            

            <!-- TOP CARDS -->
            <div class="grid grid-2">
                <!-- Card 1: Transaksi Setor Sampah -->
                <div class="card metric-card">
                    <h3>Transaksi Setor Sampah</h3>
                    <img src="{{ asset('images/Health Graph.png') }}" alt="" class="metric-icon">
                    <div class="metric">
                        <span class="value">{{ number_format((int)($setor_count ?? 0), 0, ',', '.') }}</span>
                        <span class="delta">+12 dari bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Transaksi PPOB -->
                <div class="card metric-card">
                    <h3>Transaksi PPOB</h3>
                    <img src="{{ asset('images/Health Graph.png') }}" alt="" class="metric-icon">
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
                <img src="{{ asset('images/Card Wallet.png') }}" alt="" />
                <span>E money</span>
            </a>
            
            <a href="{{ route('nasabah.pulsa') }}" class="ppob-card" title="Pulsa">
                <img src="{{ asset('images/Phonelink Ring.png') }}" alt="" />
                <span>Pulsa</span>
            </a>
            
            <a href="{{ route('nasabah.pln') }}" class="ppob-card" title="PLN">
                <img src="{{ asset('images/Flash On.png') }}" alt="" />
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
