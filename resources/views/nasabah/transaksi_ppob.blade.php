<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenPoint • Riwayat PPOB</title>
    
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

        .header-content h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .header-content .subtle {
            font-size: 14px;
            color: #6b7280;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

        .filter-note {
            margin-top: 12px;
            color: #b91c1c;
            font-size: 14px;
        }

        .filter-note ul {
            margin-top: 6px;
            padding-left: 18px;
        }

        .filter-note li {
            margin-bottom: 4px;
        }

        .error-message {
            color: #b91c1c;
            background: #fee2e2;
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .table-actions {
            display: flex;
            gap: 8px;
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
            margin-top: 24px;
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

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            border: 2px solid #d1d5db;
        }

        .transactions-table thead {
            background: #f3f4f6;
            border-bottom: 2px solid #d1d5db;
        }

        .transactions-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            white-space: nowrap;
            border: 2px solid #d1d5db;
        }

        .transactions-table tbody tr {
            border-bottom: 2px solid #d1d5db;
        }

        .transactions-table td {
            padding: 16px 12px;
            vertical-align: middle;
            border: 2px solid #d1d5db;
        }

        .transaction-detail {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .transaction-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .service-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            color: #166534;
        }

        .service-badge svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.success {
            background: #d1fae5;
            color: #065f46;
        }

        .status.failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .transaction-detail strong {
            font-weight: 600;
            color: #1f2937;
        }

        .transaction-detail span {
            font-size: 13px;
            color: #6b7280;
        }

        .amount {
            font-weight: 600;
            color: #10b981;
            text-align: right;
        }

        .time {
            font-size: 12px;
            color: #9ca3af;
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

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 16px;
            }

            .header-content h2 {
                font-size: 24px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .table-actions {
                width: 100%;
            }

            .btn-export {
                flex: 1;
                justify-content: center;
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
    </style>
</head>
<body>
    <div class="app">
        <!-- SIDEBAR -->
        @include('partials.sidebarNasabah')

        <!-- MAIN CONTENT -->
        <main class="main">
            <!-- Header Section -->
            <div class="page-header">
                <div class="header-content">
                    <h2>Riwayat Transaksi PPOB</h2>
                    <p class="subtle">Lihat riwayat pembelian E-money, Pulsa, dan PLN</p>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card filter-section">
                    <div class="filter-header">
                        <h3>Filter Transaksi</h3>
                    </div>
                    <form method="GET" class="filter-controls" id="filterForm">
                        <div class="filter-group">
                            <label for="tanggalMulai">Tanggal Mulai</label>
                            <input type="date" id="tanggalMulai" name="tanggal_mulai" class="date-input" value="{{ htmlspecialchars($startDate ?? '') }}">
                        </div>

                        <div class="filter-group">
                            <label for="tanggalAkhir">Tanggal Akhir</label>
                            <input type="date" id="tanggalAkhir" name="tanggal_akhir" class="date-input" value="{{ htmlspecialchars($endDate ?? '') }}">
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn-export" style="margin-top: 0; transform: none;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                                Terapkan Filter
                            </button>
                            <button type="button" class="btn-export" style="margin-top: 0; transform: none;" onclick="resetFilter()">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <path d="M20.49 15a9 9 0 1 1-2-8.83"></path>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </form>
                    <div class="filter-note">
                        <div>Catatan.</div>
                        <ul>
                            <li>Periode mutasi yang dapat dipilih minimal 1 hari.</li>
                            <li>Mutasi rekening maksimum 30 hari yang lalu.</li>
                        </ul>
                    </div>
                    @if(!empty($filterError))
                        <div class="error-message">{{ $filterError }}</div>
                    @endif
                </div>

                <div class="table-header">
                    <h3>Daftar Transaksi</h3>
                    <div class="table-actions">
                        <button class="btn-export" onclick="exportData()">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Detail Transaksi</th>
                                <th>Nominal</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if (empty($hist))
                                <tr id="empty-row">
                                    <td colspan="4" style="text-align:center; padding:20px;">Belum ada transaksi PPOB.</td>
                                </tr>
                            @else
                                @foreach ($hist as $index => $item)
                                    @php
                                        $service = htmlspecialchars($item['service'] ?? 'PPOB');
                                        $amount = 'Rp ' . number_format((float)($item['amount'] ?? 0), 0, ',', '.');
                                        $status = strtolower($item['status'] ?? '');
                                        $statusClass = 'pending';
                                        $statusText = ucfirst($status ?: 'Menunggu');
                                        
                                        if (in_array($status, ['approved','success','selesai'])) {
                                            $statusClass = 'success';
                                            $statusText = 'Berhasil';
                                        }
                                        if (in_array($status, ['rejected','failed','ditolak'])) {
                                            $statusClass = 'failed';
                                            $statusText = 'Gagal';
                                        }
                                    @endphp
                                    <tr class="table-row" data-row-index="{{ $index }}" style="display:table-row;">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="transaction-detail">
                                                <div class="transaction-header">
                                                    <div class="service-badge">
                                                        <svg viewBox="0 0 24 24">
                                                            <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" fill="none" stroke-width="2"/>
                                                            <path d="M3 10h18" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        {{ $service }}
                                                    </div>
                                                    <span class="status status-{{ $statusClass }}">{{ $statusText }}</span>
                                                </div>
                                                <strong>{{ $service }}</strong>
                                                @if (!empty($item['deskripsi']))
                                                    <div style="display:block; color:#6b7280; font-size:13px; margin-top:6px;">{{ htmlspecialchars($item['deskripsi']) }}</div>
                                                @endif
                            </div>
                                        </td>
                                        <td class="amount" data-amount="{{ $item['amount'] ?? 0 }}">{{ $amount }}</td>
                                        <td>
                                            @if ($item['created_at'])
                                                {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-info">
                    <span id="paginationText">{{ count($hist) }} of {{ count($hist) }}</span>
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
        let currentPage = 1;
        let rowsPerPage = 10;
        let totalRows = {{ count($hist) }};
        let totalPages = Math.ceil(totalRows / rowsPerPage);
        let allRows = [];
        const filterForm = document.getElementById('filterForm');
        const tanggalMulaiInput = document.getElementById('tanggalMulai');
        const tanggalAkhirInput = document.getElementById('tanggalAkhir');

        function formatDate(value) {
            const year = value.getFullYear();
            const month = String(value.getMonth() + 1).padStart(2, '0');
            const day = String(value.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function setDateConstraints() {
            const today = new Date();
            const maxDate = formatDate(today);
            const minDateObj = new Date();
            minDateObj.setDate(minDateObj.getDate() - 30);
            const minDate = formatDate(minDateObj);

            tanggalMulaiInput.min = minDate;
            tanggalMulaiInput.max = maxDate;
            tanggalAkhirInput.min = minDate;
            tanggalAkhirInput.max = maxDate;
        }

        function getDateDiffDays(startValue, endValue) {
            const start = new Date(startValue + 'T00:00:00');
            const end = new Date(endValue + 'T00:00:00');
            const diffMs = end.getTime() - start.getTime();
            return Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1;
        }

        function resetFilter() {
            window.location.href = window.location.pathname;
        }

        if (filterForm) {
            setDateConstraints();
            filterForm.addEventListener('submit', function(e) {
                const startValue = tanggalMulaiInput.value;
                const endValue = tanggalAkhirInput.value;
                if (!startValue || !endValue) {
                    e.preventDefault();
                    alert('Pilih tanggal mulai dan tanggal akhir terlebih dahulu.');
                    return;
                }

                const rangeDays = getDateDiffDays(startValue, endValue);
                if (Number.isNaN(rangeDays) || rangeDays < 1) {
                    e.preventDefault();
                    alert('Rentang pencarian minimal 1 hari.');
                    return;
                }

                if (rangeDays > 30) {
                    e.preventDefault();
                    alert('Rentang pencarian maksimal 30 hari.');
                }
            });
        }

        function updateTableDisplay() {
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const rows = document.querySelectorAll('.table-row');
            
            rows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function updatePaginationDisplay() {
            const pageIndicator = document.getElementById('pageIndicator');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const paginationText = document.getElementById('paginationText');
            
            pageIndicator.textContent = currentPage + '/' + totalPages;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            
            if (totalRows > 0) {
                const startRow = (currentPage - 1) * rowsPerPage + 1;
                const endRow = Math.min(currentPage * rowsPerPage, totalRows);
                paginationText.textContent = endRow + ' of ' + totalRows;
            } else {
                paginationText.textContent = '0 of 0';
            }
            
            updateTableDisplay();
        }

        function updateRowsPerPage() {
            const select = document.getElementById('rows-per-page');
            rowsPerPage = parseInt(select.value);
            totalPages = Math.ceil(totalRows / rowsPerPage);
            currentPage = 1;
            updatePaginationDisplay();
        }

        function goToPreviousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePaginationDisplay();
            }
        }

        function goToNextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePaginationDisplay();
            }
        }

        function exportData() {
            const rows = document.querySelectorAll('.table-row');
            if (rows.length === 0) {
                alert('Tidak ada data untuk diekspor!');
                return;
            }

            let csvContent = 'data:text/csv;charset=utf-8,';
            csvContent += 'No,Layanan,Status,Nominal,Tanggal\n';

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    const id = cells[0].textContent.trim();
                    const service = cells[1].querySelector('.service-badge')?.textContent.trim() || '';
                    const status = cells[1].querySelector('.status')?.textContent.trim() || '';
                    const amount = cells[2].textContent.trim();
                    const date = cells[3].textContent.trim().replace(/\n/g, ' ');
                    
                    const csvRow = `"${id}","${service}","${status}","${amount}","${date}"`;
                    csvContent += csvRow + '\n';
                }
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'transaksi_ppob_' + new Date().toISOString().split('T')[0] + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Initialize pagination on load
        document.addEventListener('DOMContentLoaded', function() {
            updatePaginationDisplay();
        });
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
