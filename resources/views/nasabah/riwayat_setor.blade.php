<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Setor - GreenPoint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .page-header .subtle {
            font-size: 14px;
            color: #6b7280;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card.table-section {
            margin-bottom: 0;
        }

        .table-header {
            margin-bottom: 20px;
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .filter-section {
            margin-bottom: 20px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .filter-header {
            margin-bottom: 16px;
        }

        .filter-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 0 0 auto;
            min-width: 180px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 500;
            color: #4b5563;
        }

        .date-input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: white;
        }

        .date-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .btn-secondary {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            /* transform: translate(0, -50%); */
            background: transparent;
            color: #059669;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -5%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-secondary:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            /* transform: translate(0, -50%); */
            background: transparent;
            color: #059669;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-outline:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -5%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-outline:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .table-actions {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-icon svg {
            width: 18px;
            height: 18px;
        }

        .search-input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            background: white;
        }

        .search-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
        }

        .btn-export {
            transform: translate(0, -50%);
            background: transparent;
            color: #059669;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #059669;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 26px;
        }

        .btn-export:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-export:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        td {
            padding: 16px 12px;
            font-size: 14px;
            color: #1f2937;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status.success {
            background: #d1fae5;
            color: #065f46;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #4b5563;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pagination-controls label {
            font-size: 14px;
            color: #4b5563;
        }

        .pagination-controls select {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            font-family: inherit;
        }

        .pagination-controls button {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .pagination-controls button:hover:not(:disabled) {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .pagination-controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-controls button svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .pagination-controls span {
            font-size: 14px;
            color: #4b5563;
            min-width: 40px;
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 16px;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                min-width: auto;
            }

            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-actions button {
                width: 100%;
            }

            .table-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                max-width: none;
            }

            table {
                font-size: 12px;
            }

            td, th {
                padding: 8px;
            }

            .pagination-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .pagination-controls {
                flex-wrap: wrap;
                gap: 8px;
            }
        }

        .icon {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
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
                    <h1>Riwayat Setor Sampah</h1>
                    <p class="subtle">Daftar Riwayat Setor Sampah Anda</p>
                </div>
            </div>

            <div class="card table-section">
                <div class="table-header">
                    <h3>Riwayat Transaksi Setor</h3>
                </div>

                <div class="card filter-section">
                    <div class="filter-header">
                        <h3>Filter Transaksi</h3>
                    </div>
                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="tanggalMulai">Tanggal Mulai</label>
                            <input type="date" id="tanggalMulai" class="date-input">
                        </div>

                        <div class="filter-group">
                            <label for="tanggalAkhir">Tanggal Akhir</label>
                            <input type="date" id="tanggalAkhir" class="date-input">
                        </div>

                        <div class="filter-actions">
                            <button type="button" class="btn-secondary" onclick="applyFilter()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                                Terapkan Filter
                            </button>
                            <button type="button" class="btn-outline" onclick="resetFilter()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <path d="M20.49 15a9 9 0 1 1-2-8.83"></path>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-actions">
                    <div class="search-container">
                        <div class="search-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </div>
                        <input type="text" class="search-input" placeholder="Search..." id="searchInput" aria-label="Cari riwayat">
                    </div>

                    <button class="btn-export" aria-label="Export data" onclick="exportData()">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        Export
                    </button>
                </div>

                <table role="table" aria-label="Riwayat Setor Sampah">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (kg)</th>
                            <th>Harga/kg</th>
                            <th>Subtotal</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @if (empty($transactions))
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5.5 1h13a1.5 1.5 0 0 1 1.5 1.5v21a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 23.5v-21A1.5 1.5 0 0 1 5.5 1z"></path>
                                        <path d="M9 1v22M15 1v22M5 5h14M5 11h14"></path>
                                    </svg>
                                    <p>Belum ada riwayat setor sampah</p>
                                </td>
                            </tr>
                        @else
                            @foreach ($transactions as $transaction)
                                @php
                                    $status_class = 'pending';
                                    if ($transaction['status'] == 'selesai') {
                                        $status_class = 'success';
                                    } elseif ($transaction['status'] == 'ditolak') {
                                        $status_class = 'failed';
                                    }
                                @endphp
                                <tr>
                                    <td>#{{ $transaction['id_transaksi'] }}</td>
                                    <td>{{ htmlspecialchars($transaction['nama_jenis'] ?? 'N/A') }}</td>
                                    <td>{{ number_format($transaction['berat_kg'] ?? 0, 2) }}</td>
                                    <td>Rp {{ number_format($transaction['harga_per_kg'] ?? 0, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($transaction['subtotal'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ date('d/m/Y', strtotime($transaction['tanggal_setor'])) }}</td>
                                    <td>
                                        <span class="status status-{{ $status_class }}">
                                            {{ ucfirst($transaction['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="pagination-info">
                    <span id="paginationText">{{ count($transactions) }} dari {{ count($transactions) }} transaksi</span>
                    <div class="pagination-controls">
                        <label for="rows-per-page">Baris per halaman:</label>
                        <select id="rows-per-page" aria-label="Baris per halaman" onchange="updateRowsPerPage()">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                        </select>
                        <button aria-label="Halaman sebelumnya" onclick="goToPreviousPage()" id="prevBtn" disabled>
                            <svg viewBox="0 0 24 24">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        <span id="pageIndicator">1/1</span>
                        <button aria-label="Halaman berikutnya" onclick="goToNextPage()" id="nextBtn">
                            <svg viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('tableBody');
        const allRows = Array.from(tableBody.querySelectorAll('tr:not(.empty-state)'));
        const totalRows = allRows.length;
        const paginationText = document.getElementById('paginationText');
        const rowsPerPageSelect = document.getElementById('rows-per-page');
        const pageIndicator = document.getElementById('pageIndicator');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentPage = 1;
        let rowsPerPage = 10;
        let filteredRows = [...allRows];

        // Initialize
        paginationText.textContent = `${totalRows} dari ${totalRows} transaksi`;

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            filteredRows = allRows.filter(row => {
                const rowText = row.textContent.toLowerCase();
                return searchTerm === '' || rowText.includes(searchTerm);
            });

            currentPage = 1;
            updatePaginationDisplay();
        });

        // Export functionality
        function exportData() {
            const visibleRows = filteredRows.length > 0 ? filteredRows : allRows;
            if (!visibleRows.length) {
                alert('Tidak ada data untuk diexport.');
                return;
            }

            const headers = Array.from(document.querySelectorAll('thead th')).map(th => th.textContent.trim());
            const csv = [
                headers.join(','),
                ...visibleRows.map(row => Array.from(row.children).map(td => `"${td.textContent.trim().replace(/"/g, '""')}"`).join(','))
            ].join('\n');

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'riwayat_setor.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Filter functions
        function applyFilter() {
            alert('Filter diterapkan');
        }

        function resetFilter() {
            document.getElementById('tanggalMulai').value = '';
            document.getElementById('tanggalAkhir').value = '';
            searchInput.value = '';
            filteredRows = [...allRows];
            currentPage = 1;
            updatePaginationDisplay();
        }

        // Pagination functions
        function updateRowsPerPage() {
            rowsPerPage = parseInt(rowsPerPageSelect.value, 10);
            currentPage = 1;
            updatePaginationDisplay();
        }

        function updatePaginationDisplay() {
            const visibleCount = filteredRows.length;
            const totalPages = Math.ceil(visibleCount / rowsPerPage);
            
            pageIndicator.textContent = `${currentPage}/${totalPages}`;
            prevBtn.disabled = currentPage === 1 || totalPages === 0;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            
            const startRow = (currentPage - 1) * rowsPerPage + 1;
            const endRow = Math.min(currentPage * rowsPerPage, visibleCount);
            paginationText.textContent = `${endRow} dari ${totalRows} transaksi`;

            // Show/hide rows
            allRows.forEach((row, index) => {
                const isVisible = filteredRows.includes(row);
                const isInCurrentPage = isVisible && index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage;
                row.style.display = isInCurrentPage ? '' : 'none';
            });
        }

        function goToPreviousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePaginationDisplay();
            }
        }

        function goToNextPage() {
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePaginationDisplay();
            }
        }

        // Initialize pagination
        updatePaginationDisplay();
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
