@extends('layouts.app')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 600;">Detail Permintaan Setor Sampah</h3>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">Proses persetujuan per jenis sampah.</p>
            </div>
            <a href="{{ route('admin.transaksi', ['tab' => 'setor']) }}" style="padding: 8px 14px; background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;">Kembali</a>
        </div>

        @if (!empty($flash))
            <div style="margin-top: 16px; padding: 12px 16px; background: #ecfdf3; border: 1px solid #bbf7d0; color: #065f46; border-radius: 8px; font-size: 14px;">
                {{ $flash }}
            </div>
        @endif

        @if (!empty($databaseError))
            <div style="margin-top: 16px; padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 8px; font-size: 14px;">
                {{ $databaseError }}
            </div>
        @endif

        @if ($transaksi)
            <div style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="background: #f9fafb; padding: 12px 14px; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280;">ID Transaksi</div>
                    <div style="font-weight: 600;">#{{ $transaksi['id_transaksi_setor'] }}</div>
                </div>
                <div style="background: #f9fafb; padding: 12px 14px; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280;">Nama Nasabah</div>
                    <div style="font-weight: 600;">{{ $transaksi['nama_nasabah'] }}</div>
                </div>
                <div style="background: #f9fafb; padding: 12px 14px; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280;">Saldo Saat Ini</div>
                    <div style="font-weight: 600;">Rp {{ number_format($transaksi['saldo'], 0, ',', '.') }}</div>
                </div>
                <div style="background: #f9fafb; padding: 12px 14px; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280;">Status</div>
                    <div style="font-weight: 600; text-transform: capitalize;">{{ $transaksi['status'] }}</div>
                </div>
            </div>
        @endif
    </div>

    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin: 0 0 12px; font-size: 16px; font-weight: 600;">Daftar Jenis Sampah</h4>

        @if (count($detailItems) === 0)
            <div style="text-align: center; padding: 32px 16px; color: #6b7280;">
                Tidak ada detail setor untuk transaksi ini.
            </div>
        @else
            <form method="POST" action="{{ route('admin.transaksi.setor.update', ['id' => $transaksi['id_transaksi_setor'] ?? 0]) }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Jenis</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Berat (kg)</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Harga / kg</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Subtotal</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; color: #374151;">Keputusan</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; color: #374151;">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detailItems as $item)
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px 16px;">{{ $item['nama_jenis'] }}</td>
                                    <td style="padding: 12px 16px;">{{ number_format($item['berat_kg'], 2, ',', '.') }}</td>
                                    <td style="padding: 12px 16px;">Rp {{ number_format($item['harga_kg'], 0, ',', '.') }}</td>
                                    <td style="padding: 12px 16px;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td style="padding: 12px 16px; text-align: center;">
                                        <label style="margin-right: 10px; font-size: 12px;">
                                            <input type="radio" name="decisions[{{ $item['id_detail_setor'] }}]" value="approve" {{ $item['status_item'] === 'approved' ? 'checked' : '' }}> Setujui
                                        </label>
                                        <label style="font-size: 12px;">
                                            <input type="radio" name="decisions[{{ $item['id_detail_setor'] }}]" value="reject" {{ $item['status_item'] === 'rejected' ? 'checked' : '' }}> Tolak
                                        </label>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <input type="text" name="notes[{{ $item['id_detail_setor'] }}]" value="{{ $item['catatan_admin'] }}" placeholder="Catatan admin" style="width: 100%; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="submit" style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600;">Simpan Keputusan</button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
