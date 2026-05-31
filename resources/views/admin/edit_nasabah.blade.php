@extends('layouts.app')

@push('styles')
<style>
    .locked-field {
        background: #f6f8f6 !important;
        color: #53625a !important;
        cursor: not-allowed;
    }

    .locked-note {
        color: #6d7a71;
        font-size: 12px;
        margin-top: 6px;
    }
</style>
@endpush

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Form Edit Nasabah</h2>
                    <p class="gp-subtitle mb-0">Perbarui data nasabah terpilih.</p>
                </div>
                <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.nasabah.update', $nasabah['id_nasabah']) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ $nasabah['nama_lengkap'] ?? '' }}" required maxlength="150">
                </div>
                <div class="col-md-6">
                    <label for="user_name" class="form-label">Username</label>
                    <input type="text" class="form-control locked-field" id="user_name" value="{{ $nasabah['user_name'] ?? '' }}" readonly aria-readonly="true">
                    <div class="locked-note">Username tidak dapat diubah dari halaman admin.</div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control locked-field" id="email" value="{{ $nasabah['email'] ?? '' }}" readonly aria-readonly="true">
                    <div class="locked-note">Email hanya dapat diperbarui oleh nasabah melalui profil dan verifikasi email.</div>
                </div>
                <div class="col-md-6">
                    <label for="no_hp" class="form-label">No. HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ $nasabah['no_hp'] ?? '' }}" maxlength="30">
                </div>
                <div class="col-md-6">
                    <label for="saldo" class="form-label">Saldo (Rp)</label>
                    <input type="text" class="form-control locked-field" id="saldo" value="Rp {{ number_format((float) ($nasabah['saldo'] ?? 0), 0, ',', '.') }}" readonly aria-readonly="true">
                    <div class="locked-note">Saldo berubah otomatis dari transaksi, top up, dan penarikan.</div>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" {{ ($nasabah['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="menunggu" {{ ($nasabah['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="nonaktif" {{ ($nasabah['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" maxlength="255">{{ $nasabah['alamat'] ?? '' }}</textarea>
                </div>
                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>Simpan
                    </button>
                    <a href="{{ route('admin.nasabah.daftar') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
