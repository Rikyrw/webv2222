@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-6 fw-bold mb-2">Tambah Jenis Sampah</h1>
            <p class="text-muted">Tambahkan jenis sampah baru ke dalam sistem.</p>
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
                    <form method="POST" action="{{ route('admin.sampah.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_jenis" class="form-label">Nama Jenis Sampah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_jenis" name="nama_jenis" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_kg" class="form-label">Harga per kg (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" required min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok" name="stok" required min="0">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="lucide-save"></i> Simpan
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