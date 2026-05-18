@extends('layouts.app')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    
    <style>
        .tab-links { display: flex; gap: 0; padding: 0; }
        .tab-link { flex: 1; padding: 16px 20px; text-align: center; text-decoration: none; color: #6b7280; font-weight: 500; border-bottom: 3px solid transparent; transition: all 0.2s; display: inline-block; }
        .tab-link.is-active { color: #10b981; font-weight: 600; border-bottom-color: #10b981; }
        .table-grid { width: 100%; border-collapse: collapse; font-size: 14px; border: 2px solid #d1d5db; }
        .table-grid th, .table-grid td { border: 2px solid #d1d5db; }
    </style>

    <!-- Tab Navigation -->
    <div style="background: white; padding: 0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-bottom: 1px solid #e5e7eb;">
        <div class="tab-links">
            <a href="?tab=setor" class="tab-link {{ ($tab == 'setor') ? 'is-active' : '' }}">
                <i class="lucide" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
                Permintaan Setor Sampah
            </a>
            <a href="?tab=penarikan" class="tab-link {{ ($tab == 'penarikan') ? 'is-active' : '' }}">
                <i class="lucide" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
                Permintaan Penarikan
            </a>
            <a href="?tab=history" class="tab-link {{ ($tab == 'history') ? 'is-active' : '' }}">
                <i class="lucide" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 6px;"></i>
                Riwayat Penarikan & Setor
            </a>
        </div>
    </div>

    <!-- TAB: Setor Sampah -->
    @if ($tab == 'setor')
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Permintaan Setor Sampah</h3>
        <p style="margin: 0 0 20px; color: #6b7280; font-size: 14px;">Tinjau dan proses permintaan setor sampah dari nasabah</p>

        @if (count($setorRequests) == 0)
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                <p>Tidak ada permintaan setor sampah yang menunggu.</p>
            </div>
        @else
            <div id="setor-table-wrap">
                <div style="overflow-x: auto;">
                    <table class="table-grid">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">No.</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nasabah</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Jenis</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Total Berat</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Total Nilai</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Tanggal</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Status</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($setorRequests as $tx)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;"><strong>{{ ($setorRequestsMeta['offset'] ?? 0) + $loop->iteration }}</strong></td>
                            <td style="padding: 12px 16px;">{{ $tx['nama_nasabah'] }}</td>
                            <td style="padding: 12px 16px;">{{ $tx['jenis'] }}</td>
                            <td style="padding: 12px 16px;">{{ number_format($tx['total_berat'], 2, ',', '.') }} kg</td>
                            <td style="padding: 12px 16px;">Rp {{ number_format($tx['total_nilai'], 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px;">
                                {{ $tx['tanggal_setor'] ? \Carbon\Carbon::parse($tx['tanggal_setor'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if ($tx['status'] === 'menunggu')
                                    <span style="display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 12px; font-weight: 600;">Menunggu</span>
                                @elseif (in_array($tx['status'], ['approved', 'selesai', 'success']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 20px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                @elseif (in_array($tx['status'], ['ditolak', 'rejected']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                @else
                                    <span style="display: inline-block; padding: 4px 12px; background: #e6f0ff; color: #1e3a8a; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ ucfirst($tx['status']) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <a href="{{ route('admin.transaksi.setor.detail', ['id' => $tx['id_transaksi']]) }}" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;">
                                    {{ $tx['status'] === 'menunggu' ? 'Proses' : 'Detail' }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px;">
                    <span style="align-self: center; font-size: 12px; color: #6b7280;">Halaman {{ $setorRequestsMeta['page'] ?? 1 }}</span>
                    @if (!empty($setorRequestsMeta['has_prev']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'setor', 'page_setor_req' => $setorRequestsMeta['page'] - 1]) }}" style="padding: 6px 12px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Sebelumnya</a>
                    @endif
                    @if (!empty($setorRequestsMeta['has_next']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'setor', 'page_setor_req' => $setorRequestsMeta['page'] + 1]) }}" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Berikutnya</a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- TAB: Penarikan -->
    @elseif ($tab == 'penarikan')
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Permintaan Penarikan</h3>
        <p style="margin: 0 0 20px; color: #6b7280; font-size: 14px;">Tinjau dan proses permintaan penarikan saldo nasabah</p>

        @if (count($penarikanRequests) == 0)
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                <p>Tidak ada permintaan penarikan yang menunggu.</p>
            </div>
        @else
            <div id="penarikan-table-wrap">
                <div style="overflow-x: auto;">
                    <table class="table-grid">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">No.</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nasabah</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Jenis</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nominal (Rp)</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Deskripsi</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Tanggal</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Status</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penarikanRequests as $p)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;"><strong>{{ ($penarikanRequestsMeta['offset'] ?? 0) + $loop->iteration }}</strong></td>
                            <td style="padding: 12px 16px;">{{ $p['nama_nasabah'] }}</td>
                            <td style="padding: 12px 16px;">{{ $p['jenis_penukaran'] }}</td>
                            <td style="padding: 12px 16px;">{{ number_format($p['nominal'], 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px; white-space: normal; overflow: visible;">{{ $p['deskripsi'] }}</td>
                            <td style="padding: 12px 16px;">
                                {{ $p['tanggal_pengajuan'] ? \Carbon\Carbon::parse($p['tanggal_pengajuan'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if ($p['status'] === 'menunggu')
                                    <span style="display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 12px; font-weight: 600;">Menunggu</span>
                                @elseif (in_array($p['status'], ['approved', 'selesai', 'success']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 20px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                @elseif (in_array($p['status'], ['ditolak', 'rejected']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                @else
                                    <span style="display: inline-block; padding: 4px 12px; background: #e6f0ff; color: #1e3a8a; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ ucfirst($p['status']) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if ($p['status'] === 'menunggu')
                                    <button type="button" class="btn-action-penarikan" data-id="{{ $p['id_penukaran'] }}" data-action="approve" data-url="{{ route('admin.transaksi.penarikan.action', ['id' => $p['id_penukaran']]) }}" style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; margin-right: 6px;">Setujui</button>
                                    <button type="button" class="btn-action-penarikan" data-id="{{ $p['id_penukaran'] }}" data-action="reject" data-url="{{ route('admin.transaksi.penarikan.action', ['id' => $p['id_penukaran']]) }}" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">Tolak</button>
                                @else
                                    <span style="color: #6b7280; font-size: 12px;">Proses Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px;">
                    <span style="align-self: center; font-size: 12px; color: #6b7280;">Halaman {{ $penarikanRequestsMeta['page'] ?? 1 }}</span>
                    @if (!empty($penarikanRequestsMeta['has_prev']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'penarikan', 'page_penarikan_req' => $penarikanRequestsMeta['page'] - 1]) }}" style="padding: 6px 12px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Sebelumnya</a>
                    @endif
                    @if (!empty($penarikanRequestsMeta['has_next']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'penarikan', 'page_penarikan_req' => $penarikanRequestsMeta['page'] + 1]) }}" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Berikutnya</a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- TAB: History -->
    @else
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Riwayat Penarikan & Setor</h3>
        <p style="margin: 0 0 20px; color: #6b7280; font-size: 14px;">Riwayat terakhir nasabah menarik atau setor sampah</p>

        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin: 0 0 12px;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 600;">Riwayat Setor Sampah</h4>
            <form method="GET" action="{{ route('admin.transaksi') }}" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <input type="hidden" name="tab" value="history">
                <label style="font-size: 12px; color: #6b7280;">Filter status</label>
                <select name="history_status" onchange="this.form.submit()" style="padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px;">
                    <option value="all" {{ ($historyStatus ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="selesai" {{ ($historyStatus ?? 'all') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="sebagian" {{ ($historyStatus ?? 'all') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="ditolak" {{ ($historyStatus ?? 'all') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <label style="font-size: 12px; color: #6b7280;">Tanggal</label>
                <input type="date" name="history_date" value="{{ $historyDate ?? '' }}" onchange="this.form.submit()" style="padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px;">
            </form>
        </div>
        <div id="history-setor-wrap">
            @if (count($historySetor) == 0)
                <div style="text-align: center; padding: 32px 16px; color: #6b7280;">
                    <p>Belum ada riwayat setor.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="table-grid">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">No.</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nasabah</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Jenis</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Total Berat</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Total Nilai</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Disetujui</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Ditolak</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Catatan Admin</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Tanggal Proses</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historySetor as $h)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;"><strong>{{ ($historySetorMeta['offset'] ?? 0) + $loop->iteration }}</strong></td>
                            <td style="padding: 12px 16px;">{{ $h['nama_nasabah'] }}</td>
                            <td style="padding: 12px 16px;">{{ $h['jenis'] }}</td>
                            <td style="padding: 12px 16px;">{{ number_format($h['total_berat'], 2, ',', '.') }} kg</td>
                            <td style="padding: 12px 16px;">Rp {{ number_format($h['total_nilai'], 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px;">
                                @if (count($h['approved_items'] ?? []) > 0)
                                    @foreach ($h['approved_items'] as $item)
                                        <div style="font-size: 12px;">{{ $item }}</div>
                                    @endforeach
                                @else
                                    <span style="color: #6b7280; font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                @if (count($h['rejected_items'] ?? []) > 0)
                                    @foreach ($h['rejected_items'] as $item)
                                        <div style="font-size: 12px;">{{ $item }}</div>
                                    @endforeach
                                @else
                                    <span style="color: #6b7280; font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                @if (count($h['catatan_admin'] ?? []) > 0)
                                    @foreach ($h['catatan_admin'] as $note)
                                        <div style="font-size: 12px; color: #6b7280;">{{ $note }}</div>
                                    @endforeach
                                @else
                                    <span style="color: #6b7280; font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                {{ $h['tanggal_proses'] ? \Carbon\Carbon::parse($h['tanggal_proses'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if ($h['status'] === 'menunggu')
                                    <span style="display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 12px; font-weight: 600;">Menunggu</span>
                                @elseif (in_array($h['status'], ['approved', 'selesai', 'success']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 20px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                @elseif (in_array($h['status'], ['ditolak', 'rejected']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                @else
                                    <span style="display: inline-block; padding: 4px 12px; background: #e6f0ff; color: #1e3a8a; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ ucfirst($h['status']) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px;">
                    <span style="align-self: center; font-size: 12px; color: #6b7280;">Halaman {{ $historySetorMeta['page'] ?? 1 }}</span>
                    @if (!empty($historySetorMeta['has_prev']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_setor' => $historySetorMeta['page'] - 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" style="padding: 6px 12px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Sebelumnya</a>
                    @endif
                    @if (!empty($historySetorMeta['has_next']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_setor' => $historySetorMeta['page'] + 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Berikutnya</a>
                    @endif
                </div>
            @endif
        </div>

        <h4 style="margin: 24px 0 12px; font-size: 16px; font-weight: 600;">Riwayat Penarikan</h4>
        <div id="history-penarikan-wrap">
            @if (count($historyPenarikan) == 0)
                <div style="text-align: center; padding: 32px 16px; color: #6b7280;">
                    <p>Belum ada riwayat penarikan.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="table-grid">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">No.</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nasabah</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Jenis</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Nominal (Rp)</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Deskripsi</th>
                            <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Tanggal Proses</th>
                            <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyPenarikan as $p)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px 16px;"><strong>{{ ($historyPenarikanMeta['offset'] ?? 0) + $loop->iteration }}</strong></td>
                            <td style="padding: 12px 16px;">{{ $p['nama_nasabah'] }}</td>
                            <td style="padding: 12px 16px;">{{ $p['jenis_penukaran'] }}</td>
                            <td style="padding: 12px 16px;">{{ number_format($p['nominal'], 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px; white-space: normal; overflow: visible;">{{ $p['deskripsi'] }}</td>
                            <td style="padding: 12px 16px;">
                                {{ $p['tanggal_proses'] ? \Carbon\Carbon::parse($p['tanggal_proses'])->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if ($p['status'] === 'menunggu')
                                    <span style="display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 12px; font-weight: 600;">Menunggu</span>
                                @elseif (in_array($p['status'], ['approved', 'selesai', 'success']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #d1fae5; color: #065f46; border-radius: 20px; font-size: 12px; font-weight: 600;">Disetujui</span>
                                @elseif (in_array($p['status'], ['ditolak', 'rejected']))
                                    <span style="display: inline-block; padding: 4px 12px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-size: 12px; font-weight: 600;">Ditolak</span>
                                @else
                                    <span style="display: inline-block; padding: 4px 12px; background: #e6f0ff; color: #1e3a8a; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ ucfirst($p['status']) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px;">
                    <span style="align-self: center; font-size: 12px; color: #6b7280;">Halaman {{ $historyPenarikanMeta['page'] ?? 1 }}</span>
                    @if (!empty($historyPenarikanMeta['has_prev']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_penarikan_hist' => $historyPenarikanMeta['page'] - 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" style="padding: 6px 12px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Sebelumnya</a>
                    @endif
                    @if (!empty($historyPenarikanMeta['has_next']))
                        <a href="{{ route('admin.transaksi', ['tab' => 'history', 'page_penarikan_hist' => $historyPenarikanMeta['page'] + 1, 'history_status' => $historyStatus ?? 'all', 'history_date' => $historyDate ?? null]) }}" style="padding: 6px 12px; background: #2563eb; color: white; border: none; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">Berikutnya</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @endif

</div>

<!-- Modal for Reject Setor -->
<div id="rejectModalSetor" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 90%; max-width: 400px;">
        <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Alasan Penolakan</h3>
        <form id="rejectFormSetor" style="display: flex; flex-direction: column; gap: 16px;">
            <textarea id="rejectNoteSetor" placeholder="Masukkan alasan penolakan..." required style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; min-height: 100px; font-family: inherit;"></textarea>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('rejectModalSetor').style.display='none'" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Reject Penarikan -->
<div id="rejectModalPenarikan" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 90%; max-width: 400px;">
        <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Alasan Penolakan</h3>
        <form id="rejectFormPenarikan" style="display: flex; flex-direction: column; gap: 16px;">
            <textarea id="rejectNotePenarikan" placeholder="Masukkan alasan penolakan..." required style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; min-height: 100px; font-family: inherit;"></textarea>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('rejectModalPenarikan').style.display='none'" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
let pendingSetorId = null;
let pendingPenarikanId = null;
let pendingPenarikanUrl = null;

// Setor actions
document.querySelectorAll('.btn-action-setor').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const action = this.dataset.action;

        if (action === 'reject') {
            pendingSetorId = id;
            document.getElementById('rejectModalSetor').style.display = 'flex';
        } else if (action === 'approve') {
            if (confirm('Setujui permintaan setor sampah ini?')) {
                console.log('Approve setor:', id);
                alert('Permintaan setor sampah berhasil disetujui');
                location.reload();
            }
        }
    });
});

// Penarikan actions
document.querySelectorAll('.btn-action-penarikan').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const action = this.dataset.action;
        const url = this.dataset.url;

        if (action === 'reject') {
            pendingPenarikanId = id;
            pendingPenarikanUrl = url;
            document.getElementById('rejectModalPenarikan').style.display = 'flex';
        } else if (action === 'approve') {
            if (confirm('Setujui permintaan penarikan ini?')) {
                submitPenarikanAction(url, 'approve', '');
            }
        }
    });
});

// Setor reject form
document.getElementById('rejectFormSetor').addEventListener('submit', function(e) {
    e.preventDefault();
    const note = document.getElementById('rejectNoteSetor').value;
    if (!note.trim()) {
        alert('Alasan penolakan harus diisi');
        return;
    }
    console.log('Reject setor:', pendingSetorId, 'Reason:', note);
    alert('Permintaan setor sampah berhasil ditolak');
    document.getElementById('rejectModalSetor').style.display = 'none';
    location.reload();
});

// Penarikan reject form
document.getElementById('rejectFormPenarikan').addEventListener('submit', function(e) {
    e.preventDefault();
    const note = document.getElementById('rejectNotePenarikan').value;
    if (!note.trim()) {
        alert('Alasan penolakan harus diisi');
        return;
    }
    submitPenarikanAction(pendingPenarikanUrl, 'reject', note);
});

function submitPenarikanAction(url, action, note) {
    if (!url) {
        alert('URL aksi penarikan tidak ditemukan.');
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]');
    const csrf = token ? token.getAttribute('content') : '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action, note })
    })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) {
                throw new Error(data && data.message ? data.message : 'Gagal memproses penarikan.');
            }
            alert(data.message || 'Permintaan penarikan berhasil diproses.');
            document.getElementById('rejectModalPenarikan').style.display = 'none';
            location.reload();
        })
        .catch(error => {
            alert(error.message || 'Terjadi kesalahan saat memproses penarikan.');
        });
}

// Close modal on outside click
window.addEventListener('click', function(e) {
    const modalSetor = document.getElementById('rejectModalSetor');
    const modalPenarikan = document.getElementById('rejectModalPenarikan');
    if (e.target === modalSetor) modalSetor.style.display = 'none';
    if (e.target === modalPenarikan) modalPenarikan.style.display = 'none';
});

function refreshTransaksiTables() {
    const activeElement = document.activeElement;
    if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'SELECT' || activeElement.tagName === 'TEXTAREA')) {
        return;
    }

    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            ['setor-table-wrap', 'penarikan-table-wrap'].forEach(function(id) {
                const current = document.getElementById(id);
                const incoming = doc.getElementById(id);
                if (current && incoming) {
                    current.innerHTML = incoming.innerHTML;
                }
            });
        })
        .catch(() => {
            // Skip errors to avoid interrupting user flow.
        });
}

refreshTransaksiTables();
setInterval(refreshTransaksiTables, 5000);
</script>

@endsection
