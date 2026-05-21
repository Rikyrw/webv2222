@extends('layouts.app')

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Form Edit Jenis Sampah</h2>
                    <p class="gp-subtitle mb-0">Perbarui informasi jenis sampah.</p>
                </div>
                <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.sampah.update', $sampah->id_jenis_sampah) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label for="nama_jenis" class="form-label">Nama Jenis Sampah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_jenis" name="nama_jenis" value="{{ $sampah->nama_jenis }}" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label for="harga_per_kg" class="form-label">Harga per kg (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" value="{{ $sampah->harga_per_kg }}" required min="0" step="0.01">
                </div>
                <div class="col-md-6">
                    <label for="stok" class="form-label">Stok (kg) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stok" name="stok" value="{{ $sampah->stok }}" required min="0">
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" {{ $sampah->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $sampah->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>Perbarui
                    </button>
                    <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
