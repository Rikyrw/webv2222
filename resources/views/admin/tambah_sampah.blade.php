@extends('layouts.app')

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Form Tambah Jenis Sampah</h2>
                    <p class="gp-subtitle mb-0">Tambahkan jenis sampah baru ke dalam sistem.</p>
                </div>
                <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.sampah.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="nama_jenis" class="form-label">Nama Jenis Sampah <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_jenis" name="nama_jenis" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label for="harga_per_kg" class="form-label">Harga per kg (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" required min="0" step="1">
                </div>
                <div class="col-md-6">
                    <label for="stok" class="form-label">Stok (kg) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stok" name="stok" required min="0" step="0.01">
                </div>
                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>Simpan
                    </button>
                    <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
