<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GreenPoint Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@0.469.0/font/lucide.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial;
            background: #f5f6f5;
            color: #122018;
            overflow: hidden;
        }

        .app {
            display: flex;
            height: 100vh;
            min-height: 0;
            background: #f5f6f5;
            overflow: hidden;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            min-height: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header {
            padding: 23px 28px 16px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            flex-shrink: 0;
        }

        .page-intro {
            min-width: 0;
        }

        .header h1 {
            margin: 0 0 6px;
            color: #2f5f3e;
            font-size: 25px;
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: 0;
        }

        .header p {
            margin: 0;
            color: #9aa09d;
            font-size: 13px;
            line-height: 1.45;
        }

        .admin-status {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            max-width: 520px;
            color: #5e6c63;
            font-size: 12px;
        }

        .admin-status span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            padding: 6px 10px;
            border: 1px solid #e2e8e3;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.72);
            white-space: nowrap;
        }

        .role-badge {
            color: #2f5f3e;
            font-weight: 800;
        }

        .content {
            flex: 1;
            min-width: 0;
            min-height: 0;
            overflow-y: auto;
            padding: 0 28px 28px;
        }

        .content .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        .card,
        .report-card,
        .report-panel {
            border-color: #e2e8e3 !important;
            border-radius: 10px !important;
            box-shadow: 0 7px 18px rgba(15, 23, 42, 0.08) !important;
        }

        .btn-primary {
            background: #2f5f3e;
            border-color: #2f5f3e;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #254d33;
            border-color: #254d33;
        }

        .content::-webkit-scrollbar {
            width: 7px;
        }

        .content::-webkit-scrollbar-track {
            background: transparent;
        }

        .content::-webkit-scrollbar-thumb {
            background: #d4dfd7;
            border-radius: 999px;
        }

        @media (max-width: 900px) {
            .header {
                flex-direction: column;
                gap: 12px;
            }

            .admin-status {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .app {
                flex-direction: column;
            }

            .main-content {
                height: auto;
                min-height: 0;
            }

            .header {
                padding: 18px 16px 14px;
            }

            .content {
                padding: 0 16px 20px;
            }

            .admin-status span {
                white-space: normal;
            }
        }
    </style>
    @include('partials.greenpoint-theme')
    @stack('styles')
</head>
<body>
    <div class="app">
        @php
            $activePage = $activePage ?? 'dashboard';
            $adminEmail = session('admin_email') ?? auth()->user()->email ?? '';
            $adminName = $adminEmail ? explode('@', $adminEmail)[0] : (auth()->user()->name ?? 'Admin');
            $adminRole = session('admin_role') ?? auth()->user()->role ?? 'operator';
            $pageSubtitles = [
                'dashboard' => 'Selamat datang di sistem manajemen bank sampah',
                'nasabah' => 'Kelola data dan status akun nasabah dari satu tempat',
                'transaksi' => 'Tinjau permintaan setor, penarikan, dan riwayat transaksi',
                'sampah' => 'Kelola jenis sampah, harga, dan stok yang tersedia',
                'laporan' => 'Ringkasan data keuangan, sampah, dan nasabah',
                'pengaturan' => 'Atur akses dan data admin GreenPoint',
            ];
            $headerSubtitle = $pageSubtitle ?? ($pageSubtitles[$activePage] ?? 'Kelola operasional GreenPoint dari satu tempat');
        @endphp
        @include('partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div class="page-intro">
                    <h1>{{ isset($pageTitle) ? $pageTitle : 'Dashboard' }}</h1>
                    <p>{{ $headerSubtitle }}</p>
                </div>
                <div class="admin-status">
                    <span class="role-badge"><i class="lucide-shield"></i> {{ ucfirst($adminRole) }}</span>
                    <span><i class="lucide-user"></i> {{ $adminName }}</span>
                    @if ($adminEmail)
                        <span><i class="lucide-mail"></i> {{ $adminEmail }}</span>
                    @endif
                    @php
                        $wibNow = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
                    @endphp
                    <span><i class="lucide-clock"></i> {{ $wibNow->translatedFormat('d M Y H:i') }} WIB</span>
                </div>
            </div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.DataTable) {
                return;
            }

            document.querySelectorAll('table.gp-data-table').forEach(function (table) {
                if (table.dataset.gpEnhanced === 'true' || table.querySelector('tbody td[colspan]')) {
                    return;
                }

                table.dataset.gpEnhanced = 'true';
                new DataTable(table, {
                    pageLength: Number(table.dataset.pageLength || 8),
                    lengthMenu: [5, 8, 10, 25],
                    order: [],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Tidak ada data',
                        zeroRecords: 'Data tidak ditemukan',
                        emptyTable: 'Data tidak tersedia',
                        paginate: {
                            first: 'Awal',
                            previous: 'Sebelumnya',
                            next: 'Berikutnya',
                            last: 'Akhir'
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
    
    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
