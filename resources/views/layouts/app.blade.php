<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>GreenPoint Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@0.469.0/font/lucide.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial; background: #f6fbf8; color: #0f172a; }

        .app { display: flex; height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #1f2937;
            color: white;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .sidebar .brand img { width: 32px; height: 32px; }
        .sidebar .brand h1 { font-size: 18px; font-weight: 700; }

        .sidebar .nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .sidebar .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .sidebar .nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar .nav a.active {
            background: #059669;
            color: white;
        }

        .sidebar .nav a .i { width: 18px; height: 18px; }

        /* Main content area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 28px;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header h1 { font-size: 24px; margin-bottom: 4px; }
        .header p { font-size: 14px; color: #6b7280; margin: 4px 0; }
        .header strong { color: #0f172a; }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 8px;
        }

        .btn-logout-header {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-logout-header:hover { background: #b91c1c; }

        /* Content */
        .content {
            flex: 1;
            overflow-y: auto;
            padding: 28px;
        }

        .admin-dashboard {
            padding: 28px;
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track,
        .content::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .app { flex-direction: column; }
            .sidebar {
                width: 100%;
                max-height: 60px;
                padding: 12px 16px;
                overflow-x: auto;
                flex-direction: row;
            }
            .sidebar .brand { margin-bottom: 0; }
            .sidebar .nav { flex-direction: row; margin-left: auto; }
            .header { flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app">
        @php $activePage = $activePage ?? 'dashboard'; @endphp
        @include('partials.sidebar')

        <div class="main-content">
            <div class="header">
                <div>
                    <h1>{{ isset($pageTitle) ? $pageTitle : 'Dashboard' }}</h1>
                    <p>
                        Selamat datang, <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                        <span class="role-badge" style="background: {{ (auth()->user()->role ?? 'operator') === 'superadmin' ? '#7c3aed' : '#059669' }}; color: white;">
                            <i class="lucide-shield"></i> {{ ucfirst(auth()->user()->role ?? 'Operator') }}
                        </span>
                        <a href="{{ url('/admin/logout') }}" class="btn-logout-header" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                            <i class="lucide-log-out"></i> Logout
                        </a>
                    </p>
                    <p style="margin-top: 5px; font-size: 14px; color: #6b7280;">
                        <i class="lucide-mail"></i> {{ auth()->user()->email ?? '' }}
                    </p>
                </div>
            </div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
