@extends('layouts.app')

@section('content')
<div class="admin-dashboard">
	<style>
		.admin-dashboard {
			padding: 0;
		}

		.dashboard-alert {
			margin-bottom: 18px;
			padding: 12px 14px;
			border: 1px solid #f4c7c7;
			border-radius: 8px;
			background: #fff5f5;
			color: #9f1d1d;
			font-size: 13px;
			font-weight: 600;
		}

		.stats-grid {
			display: grid;
			grid-template-columns: repeat(4, minmax(0, 1fr));
			gap: 20px;
			margin-bottom: 24px;
		}

		.stat-card,
		.dashboard-panel {
			background: #ffffff;
			border: 1px solid #e1e7e2;
			border-radius: 10px;
			box-shadow: 0 7px 16px rgba(15, 23, 42, 0.12);
		}

		.stat-card {
			position: relative;
			min-height: 126px;
			padding: 26px 22px 18px;
			border-left: 5px solid #2f5f3e;
		}

		.stat-icon {
			position: absolute;
			top: 22px;
			right: 22px;
			width: 35px;
			height: 35px;
			display: grid;
			place-items: center;
			border-radius: 8px;
			background: #e4e7e5;
		}

		.stat-icon img {
			width: 18px;
			height: 18px;
			object-fit: contain;
		}

		.stat-label {
			padding-right: 42px;
			color: #a0a5a2;
			font-size: 12px;
			font-weight: 800;
			line-height: 1.25;
		}

		.stat-value {
			margin-top: 26px;
			color: #2f5f3e;
			font-size: 25px;
			font-weight: 800;
			line-height: 1.05;
			letter-spacing: 0;
			white-space: nowrap;
		}

		.stat-note {
			margin-top: 12px;
			color: #a0a5a2;
			font-size: 11px;
			font-weight: 500;
			line-height: 1.35;
		}

		.dashboard-main-grid {
			display: grid;
			grid-template-columns: minmax(0, 2fr) minmax(260px, 0.78fr);
			gap: 24px;
			align-items: stretch;
			margin-bottom: 24px;
		}

		.dashboard-bottom-grid {
			display: grid;
			grid-template-columns: minmax(0, 1fr);
			gap: 24px;
		}

		.dashboard-panel {
			padding: 24px;
			min-width: 0;
		}

		.panel-header {
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
			gap: 16px;
			margin-bottom: 18px;
		}

		.panel-title {
			margin: 0;
			color: #2f5f3e;
			font-size: 15px;
			font-weight: 800;
			line-height: 1.25;
		}

		.panel-subtitle {
			margin-top: 5px;
			color: #9aa09d;
			font-size: 12px;
			line-height: 1.4;
		}

		.panel-actions {
			display: inline-flex;
			align-items: center;
			gap: 7px;
			flex-shrink: 0;
		}

		.panel-chip {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-height: 23px;
			padding: 5px 10px;
			border: 1px solid #d8e3da;
			border-radius: 5px;
			background: #ffffff;
			color: #2f5f3e;
			font-size: 10px;
			font-weight: 800;
			line-height: 1;
		}

		.panel-chip.is-active {
			border-color: #2f5f3e;
			background: #2f5f3e;
			color: #ffffff;
		}

		.chart-shell {
			position: relative;
			width: 100%;
			height: 238px;
		}

		.chart-shell.is-pie {
			height: 252px;
		}

		.chart-shell canvas {
			width: 100% !important;
			height: 100% !important;
		}

		.top-table-wrap {
			overflow: auto;
			border: 1px solid #eef2ef;
			border-radius: 8px;
		}

		.top-table {
			width: 100%;
			min-width: 520px;
			border-collapse: collapse;
			font-size: 13px;
		}

		.top-table thead {
			background: #f6f8f6;
			color: #68766c;
			font-size: 12px;
			font-weight: 800;
		}

		.top-table th,
		.top-table td {
			padding: 12px 14px;
			border-bottom: 1px solid #eef2ef;
			vertical-align: middle;
		}

		.top-table tbody tr:last-child td {
			border-bottom: 0;
		}

		.top-table td:last-child,
		.top-table th:last-child {
			text-align: right;
		}

		.rank-badge {
			display: inline-grid;
			place-items: center;
			width: 26px;
			height: 26px;
			border-radius: 999px;
			background: #eef6f0;
			color: #2f5f3e;
			font-size: 12px;
			font-weight: 800;
		}

		@media (max-width: 1260px) {
			.stats-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}

		@media (max-width: 1050px) {
			.dashboard-main-grid {
				grid-template-columns: 1fr;
			}
		}

		@media (max-width: 640px) {
			.stats-grid {
				grid-template-columns: 1fr;
				gap: 14px;
			}

			.stat-value {
				white-space: normal;
			}

			.dashboard-panel {
				padding: 18px;
			}

			.panel-header {
				flex-direction: column;
			}

			.chart-shell,
			.chart-shell.is-pie {
				height: 230px;
			}
		}
	</style>

	@if (!empty($databaseError))
		@include('partials.toast', ['type' => 'danger', 'message' => $databaseError])
	@endif

	<div class="stats-grid">
		<div class="stat-card">
			<div class="stat-label">Nasabah Aktif</div>
			<div class="stat-icon">
				<img src="{{ asset('images/Person.png') }}" alt="" aria-hidden="true">
			</div>
			<div class="stat-value">{{ number_format($nasabahCount ?? 0, 0, ',', '.') }}</div>
			<div class="stat-note">Akun nasabah aktif terdaftar</div>
		</div>

		<div class="stat-card">
			<div class="stat-label">Total Sampah Hari Ini</div>
			<div class="stat-icon">
				<img src="{{ asset('images/Trash.png') }}" alt="" aria-hidden="true">
			</div>
			<div class="stat-value">{{ number_format($totalSampahToday ?? 0, 2, ',', '.') }} kg</div>
			<div class="stat-note">Akumulasi setoran sampah hari ini</div>
		</div>

		<div class="stat-card">
			<div class="stat-label">Pendapatan Bulan Ini</div>
			<div class="stat-icon">
				<img src="{{ asset('images/Health Graph.png') }}" alt="" aria-hidden="true">
			</div>
			<div class="stat-value">Rp {{ number_format($pendapatanThisMonth ?? 0, 0, ',', '.') }}</div>
			<div class="stat-note">Akumulasi transaksi selesai bulan ini</div>
		</div>

		<div class="stat-card">
			<div class="stat-label">Total Saldo Nasabah</div>
			<div class="stat-icon">
				<img src="{{ asset('images/Card Wallet.png') }}" alt="" aria-hidden="true">
			</div>
			<div class="stat-value">Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</div>
			<div class="stat-note">Saldo aktif seluruh nasabah</div>
		</div>
	</div>

	<div class="dashboard-main-grid">
		<div class="dashboard-panel">
			<div class="panel-header">
				<div>
					<h2 class="panel-title">Grafik Sampah</h2>
					<div class="panel-subtitle">Total transaksi setoran selama 7 hari terakhir</div>
				</div>
				<div class="panel-actions" aria-label="Rentang grafik">
					<span class="panel-chip is-active">Minggu Ini</span>
					<span class="panel-chip">7 Hari</span>
				</div>
			</div>
			<div class="chart-shell">
				<canvas id="lineChart"></canvas>
			</div>
		</div>

		<div class="dashboard-panel">
			<div class="panel-header">
				<div>
					<h2 class="panel-title">Komposisi Sampah</h2>
					<div class="panel-subtitle">Distribusi jenis sampah yang paling sering masuk</div>
				</div>
				<div class="panel-actions" aria-label="Rentang komposisi">
					<span class="panel-chip is-active">Bulan Ini</span>
				</div>
			</div>
			<div class="chart-shell is-pie">
				<canvas id="pieChart"></canvas>
			</div>
		</div>
	</div>

	<div class="dashboard-bottom-grid">
		<div class="dashboard-panel">
			<div class="panel-header">
				<div>
					<h2 class="panel-title">Nasabah Paling Aktif</h2>
					<div class="panel-subtitle">Daftar akun aktif dengan saldo tertinggi</div>
				</div>
			</div>
			<div class="top-table-wrap">
				<table class="top-table">
					<thead>
						<tr>
							<th>Peringkat</th>
							<th>Nama</th>
							<th>Saldo</th>
						</tr>
					</thead>
					<tbody>
						@forelse($topNasabah as $i => $u)
							<tr>
								<td><span class="rank-badge">{{ $i + 1 }}</span></td>
								<td>{{ $u['nama_nasabah'] ?? '-' }}</td>
								<td>Rp {{ number_format((float)($u['saldo'] ?? 0), 0, ',', '.') }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="3" style="padding: 18px 14px; color: #8a948d; text-align: center;">Data tidak tersedia</td>
							</tr>
						@endforelse
					</tbody>
				</table>
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
					label: 'Total',
					data: lineData,
					borderColor: '#2f5f3e',
					backgroundColor: 'rgba(47, 95, 62, 0.10)',
					tension: 0.42,
					fill: true,
					borderWidth: 3,
					pointRadius: 3.5,
					pointHoverRadius: 5,
					pointBackgroundColor: '#ffffff',
					pointBorderColor: '#58b86b',
					pointBorderWidth: 2
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						backgroundColor: '#2f5f3e',
						titleColor: '#ffffff',
						bodyColor: '#ffffff',
						displayColors: false
					}
				},
				scales: {
					x: {
						grid: { color: 'rgba(148, 163, 154, 0.18)' },
						ticks: { color: '#7a867d', font: { size: 10 } }
					},
					y: {
						beginAtZero: true,
						grid: { color: 'rgba(148, 163, 154, 0.22)' },
						ticks: { color: '#7a867d', font: { size: 10 } }
					}
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
					backgroundColor: ['#2f5f3e', '#4e7b52', '#6f9f70', '#8fc58c', '#c5d7a7', '#d0b36f'],
					borderColor: '#ffffff',
					borderWidth: 2
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'left',
						labels: {
							boxWidth: 8,
							boxHeight: 8,
							color: '#516156',
							font: { size: 10, weight: '600' },
							padding: 8
						}
					}
				}
			}
		});
	} catch (e) {
		console.error(e);
	}
</script>

@endsection
