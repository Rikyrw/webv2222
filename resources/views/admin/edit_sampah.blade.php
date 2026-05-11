@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-6 fw-bold mb-2">Edit Jenis Sampah</h1>
            <p class="text-muted">Perbarui informasi jenis sampah.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">
                <i class="lucide-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sampah.update', $sampah->id_jenis_sampah) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_jenis" class="form-label">Nama Jenis Sampah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_jenis" name="nama_jenis" value="{{ $sampah->nama_jenis }}" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_kg" class="form-label">Harga per kg (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" value="{{ $sampah->harga_per_kg }}" required min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok" name="stok" value="{{ $sampah->stok }}" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="aktif" {{ $sampah->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ $sampah->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="lucide-save"></i> Perbarui
                            </button>
                            <a href="{{ route('admin.sampah.daftar') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection