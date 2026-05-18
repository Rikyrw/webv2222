@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="display-6 fw-bold mb-2">Edit Nasabah</h1>
            <p class="text-muted">Perbarui data nasabah terpilih.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">
                <i class="lucide-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.nasabah.update', $nasabah['id_nasabah']) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ $nasabah['nama_lengkap'] ?? '' }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="user_name" class="form-label">Username</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" value="{{ $nasabah['user_name'] ?? '' }}" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $nasabah['email'] ?? '' }}" maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" maxlength="255">{{ $nasabah['alamat'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ $nasabah['no_hp'] ?? '' }}" maxlength="30">
                        </div>
                        <div class="mb-3">
                            <label for="saldo" class="form-label">Saldo (Rp)</label>
                            <input type="number" class="form-control" id="saldo" name="saldo" value="{{ $nasabah['saldo'] ?? 0 }}" min="0" step="1">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="aktif" {{ ($nasabah['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="menunggu" {{ ($nasabah['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="nonaktif" {{ ($nasabah['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="lucide-save"></i> Simpan
                            </button>
                            <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
