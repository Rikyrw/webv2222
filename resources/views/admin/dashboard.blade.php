@extends('layouts.app')

@section('content')
<div class="admin-dashboard">
	<style>
		.admin-dashboard-grid {
			display: grid;
			grid-template-columns: 1fr 2fr;
			gap: 20px;
			align-items: start;
		}

		.metric-stack {
			display: grid;
			gap: 16px;
		}

		.metric-card,
		.panel-card {
			background: rgba(255, 255, 255, 0.96);
			border: 1px solid rgba(229, 231, 235, 0.96);
			border-radius: 18px;
			box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
			padding: 18px;
		}

		.metric-label {
			font-size: 13px;
			color: #64748b;
			margin-bottom: 8px;
		}

		.metric-value {
			font-size: 26px;
			font-weight: 800;
			letter-spacing: -0.03em;
			color: #0f172a;
		}

		.metric-value.is-green {
			color: #059669;
		}

		.metric-value.is-blue {
			color: #2563eb;
		}

		.metric-value.is-amber {
			color: #d97706;
		}

		.chart-header,
		.panel-header {
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
			gap: 12px;
			margin-bottom: 12px;
		}

		.chart-title,
		.panel-title {
			font-size: 18px;
			font-weight: 700;
			color: #0f172a;
			margin-bottom: 4px;
		}

		.chart-subtitle,
		.panel-subtitle {
			font-size: 13px;
			color: #64748b;
			line-height: 1.5;
		}

		.pill {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 8px 12px;
			border-radius: 999px;
			background: #eefaf4;
			color: #047857;
			font-size: 12px;
			font-weight: 600;
		}

		.top-table-wrap {
			max-height: 240px;
			overflow: auto;
			border-radius: 14px;
		}

		.top-table {
			width: 100%;
			border-collapse: collapse;
			font-size: 14px;
		}

		.top-table thead {
			color: #64748b;
			font-size: 13px;
		}

		.top-table th,
		.top-table td {
			padding: 10px 0;
		}

		.top-table tbody tr {
			border-top: 1px solid #eef2f6;
		}

		.top-table td:last-child,
		.top-table th:last-child {
			text-align: right;
		}

		@media (max-width: 1100px) {
			.admin-dashboard-grid {
				grid-template-columns: 1fr;
			}

			.dual-panels {
				grid-template-columns: 1fr !important;
			}
		}
	</style>

	<div class="admin-dashboard-grid">
		<div class="metric-stack">
			<div class="metric-card">
				<div class="metric-label">Nasabah Terdaftar</div>
				<div class="metric-value is-green">{{ number_format($nasabahCount ?? 0,0,',','.') }}</div>
			</div>

			<div class="metric-card">
				<div class="metric-label">Total Sampah Hari Ini</div>
				<div class="metric-value is-blue">{{ number_format($totalSampahToday ?? 0,2,',','.') }} kg</div>
			</div>

			<div class="metric-card">
				<div class="metric-label">Pendapatan Bulan Ini</div>
				<div class="metric-value is-amber">Rp {{ number_format($pendapatanThisMonth ?? 0,0,',','.') }}</div>
			</div>

			<div class="metric-card">
				<div class="metric-label">Total Saldo Nasabah</div>
				<div class="metric-value">Rp {{ number_format($totalSaldo ?? 0,0,',','.') }}</div>
			</div>
		</div>

		<div style="display:grid;gap:16px;">
			<div class="panel-card">
				<div class="chart-header">
					<div>
						<div class="chart-title">Grafik Sampah (7 Hari)</div>
						<div class="chart-subtitle">Total kg per hari selama 7 hari terakhir</div>
					</div>
					<span class="pill"><i class="lucide-sparkles"></i> Realtime</span>
				</div>
				<canvas id="lineChart" height="120"></canvas>
			</div>

			<div class="dual-panels" style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
				<div class="panel-card">
					<div class="panel-header">
						<div>
							<div class="panel-title">Komposisi Sampah (30 Hari)</div>
							<div class="panel-subtitle">Distribusi jenis sampah yang paling sering masuk</div>
						</div>
					</div>
					<canvas id="pieChart" height="120"></canvas>
				</div>

				<div class="panel-card">
					<div class="panel-header">
						<div>
							<div class="panel-title">Nasabah Paling Aktif</div>
							<div class="panel-subtitle">Daftar akun dengan saldo tertinggi</div>
						</div>
					</div>
					<div class="top-table-wrap">
						<table class="top-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Nama</th>
									<th>Saldo</th>
								</tr>
							</thead>
							<tbody>
								@forelse($topNasabah as $i => $u)
									<tr>
										<td>{{ $i+1 }}</td>
										<td>{{ $u['nama_nasabah'] ?? '—' }}</td>
										<td>Rp {{ number_format((float)($u['saldo'] ?? 0),0,',','.') }}</td>
									</tr>
								@empty
									<tr>
										<td colspan="3" style="padding:12px 0;color:#64748b;">Data tidak tersedia</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="chart-data" 
	 data-line-labels='@json($lineLabels ?? [])'
	 data-line-data='@json($lineData ?? [])'
	 data-pie-labels='@json($pieLabels ?? [])'
	 data-pie-data='@json($pieData ?? [])'
	 hidden></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
	const chartDataElement = document.getElementById('chart-data');
	const lineLabels = JSON.parse(chartDataElement.dataset.lineLabels || '[]');
	const lineData = JSON.parse(chartDataElement.dataset.lineData || '[]');
	const pieLabels = JSON.parse(chartDataElement.dataset.pieLabels || '[]');
	const pieData = JSON.parse(chartDataElement.dataset.pieData || '[]');

	try {
		const ctxLine = document.getElementById('lineChart').getContext('2d');
		new Chart(ctxLine, {
			type: 'line',
			data: {
				labels: lineLabels,
				datasets: [{
					label: 'Kg',
					data: lineData,
					borderColor: '#10b981',
					backgroundColor: 'rgba(16,185,129,0.10)',
					tension: 0.35,
					fill: true,
					pointRadius: 3,
					pointHoverRadius: 5
				}]
			},
			options: {
				responsive: true,
				plugins: { legend: { display: false } },
				scales: {
					x: { grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#64748b' } },
					y: { grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#64748b' } }
				}
			}
		});

		const ctxPie = document.getElementById('pieChart').getContext('2d');
		new Chart(ctxPie, {
			type: 'pie',
			data: {
				labels: pieLabels,
				datasets: [{
					data: pieData,
					backgroundColor: ['#10b981', '#60a5fa', '#f97316', '#f43f5e', '#a78bfa', '#f59e0b']
				}]
			},
			options: { responsive: true }
		});
	} catch (e) {
		console.error(e);
	}
</script>

@endsection

