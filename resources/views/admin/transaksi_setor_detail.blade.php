@extends('layouts.app')

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Detail Permintaan Setor Sampah</h2>
                    <p class="gp-subtitle mb-0">Proses persetujuan per jenis sampah.</p>
                </div>
                <a href="{{ route('admin.transaksi', ['tab' => 'setor']) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>

            @if (!empty($flash))
                @include('partials.toast', ['type' => 'success', 'message' => $flash])
            @endif

            @if (!empty($databaseError))
                @include('partials.toast', ['type' => 'danger', 'message' => $databaseError])
            @endif

            @if ($transaksi)
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="gp-mini-card">
                            <span>ID Transaksi</span>
                            <strong>#{{ $transaksi['id_transaksi_setor'] }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="gp-mini-card">
                            <span>Nama Nasabah</span>
                            <strong>{{ $transaksi['nama_nasabah'] }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="gp-mini-card">
                            <span>Saldo Saat Ini</span>
                            <strong>Rp {{ number_format($transaksi['saldo'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="gp-mini-card">
                            <span>Status</span>
                            <strong>{{ ucfirst($transaksi['status']) }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Daftar Jenis Sampah</h2>
                    <p class="gp-subtitle mb-0">Pilih keputusan dan isi catatan jika diperlukan.</p>
                </div>
            </div>

            @if (count($detailItems) === 0)
                <div class="gp-empty">Tidak ada detail setor untuk transaksi ini.</div>
            @else
                <form method="POST" action="{{ route('admin.transaksi.setor.update', ['id' => $transaksi['id_transaksi_setor'] ?? 0]) }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table gp-table align-middle">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Berat (kg)</th>
                                    <th>Harga / kg</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Keputusan</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailItems as $item)
                                    <tr>
                                        <td>{{ $item['nama_jenis'] }}</td>
                                        <td>{{ number_format($item['berat_kg'], 2, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item['harga_kg'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-3 flex-wrap justify-content-center">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="decisions[{{ $item['id_detail_setor'] }}]" value="approve" {{ $item['status_item'] === 'approved' ? 'checked' : '' }}>
                                                    Setujui
                                                </label>
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="decisions[{{ $item['id_detail_setor'] }}]" value="reject" {{ $item['status_item'] === 'rejected' ? 'checked' : '' }}>
                                                    Tolak
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes[{{ $item['id_detail_setor'] }}]" value="{{ $item['catatan_admin'] }}" placeholder="Catatan admin">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>Simpan Keputusan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Foto Sampah dari Nasabah</h2>
                    <p class="gp-subtitle mb-0">Foto yang diunggah saat nasabah mengajukan setor.</p>
                </div>
            </div>

            @if (count($fotoSetorItems) === 0)
                <div class="gp-empty">Tidak ada foto sampah untuk transaksi ini.</div>
            @else
                <div class="table-responsive">
                    <table class="table gp-table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Sampah</th>
                                <th>Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fotoSetorItems as $foto)
                                @php
                                    $photoElementId = 'waste-photo-' . $loop->iteration;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $foto['urutan'] }}</td>
                                    <td>{{ $foto['nama_jenis'] }}</td>
                                    <td>
                                        <img
                                            id="{{ $photoElementId }}"
                                            src="{{ $foto['foto_url'] }}"
                                            alt="Foto sampah {{ $foto['urutan'] }}"
                                            class="gp-photo-thumb"
                                        >
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary gp-photo-view" data-photo-target="{{ $photoElementId }}" data-bs-toggle="modal" data-bs-target="#wastePhotoModal">
                                            <i class="bi bi-eye"></i>Lihat Foto
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="wastePhotoModal" tabindex="-1" aria-labelledby="wastePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wastePhotoModalLabel">Foto Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <img id="wastePhotoModalImage" src="" alt="Foto sampah ukuran besar" class="gp-photo-modal-img">
            </div>
        </div>
    </div>
</div>

<style>
    .gp-mini-card {
        display: grid;
        gap: 5px;
        height: 100%;
        padding: 14px;
        border: 1px solid #dfe7e1;
        border-radius: 8px;
        background: #fbfcfb;
    }

    .gp-mini-card span {
        color: #6d7a71;
        font-size: 12px;
        font-weight: 700;
    }

    .gp-mini-card strong {
        color: #2f5f3e;
        font-size: 14px;
    }

    .gp-photo-thumb {
        width: 120px;
        height: 90px;
        object-fit: cover;
        border: 1px solid #dfe7e1;
        border-radius: 8px;
        background: #f5f8f5;
    }

    .gp-photo-modal-img {
        display: block;
        width: 100%;
        max-height: 72vh;
        object-fit: contain;
        border-radius: 8px;
        background: #f5f8f5;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalImage = document.getElementById('wastePhotoModalImage');
        const modalElement = document.getElementById('wastePhotoModal');

        document.querySelectorAll('.gp-photo-view').forEach(function (button) {
            button.addEventListener('click', function () {
                const sourceImage = document.getElementById(button.dataset.photoTarget);
                modalImage.src = sourceImage ? sourceImage.src : '';
            });
        });

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function () {
                modalImage.src = '';
            });
        }
    });
</script>
@endsection
