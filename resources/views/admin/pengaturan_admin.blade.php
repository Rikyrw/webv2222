@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-success:hover { background-color: #218838; border-color: #1e7e34; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; border-color: #bd2130; }
        .btn-info { background-color: #17a2b8; border-color: #17a2b8; }
        .btn-info:hover { background-color: #138496; border-color: #117a8b; }
        .table th { background-color: #28a745; color: white; border: none; }
        .table td { vertical-align: middle; }
        .badge { font-size: 0.8em; }
        .modal-content { border-radius: 10px; }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Pengaturan Admin</h1>
                    <p class="text-muted">Kelola akun administrator. Semua admin dapat mengakses halaman ini.</p>
                </div>
                <button class="btn btn-success" onclick="openTambahAdmin()">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Admin
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Daftar Admin</h5>
                    <p class="card-text text-muted mb-4">Gunakan tombol edit dan hapus untuk mengelola akses.</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td>{{ $admin['username'] ?? '-' }}</td>
                                        <td>{{ $admin['nama_lengkap'] }}</td>
                                        <td>{{ $admin['email'] }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ ucfirst(str_replace('superadmin', 'Super Admin', $admin['role'])) }}</span>
                                        </td>
                                        <td>
                                            @php $status = $admin['status'] ?? 'aktif'; @endphp
                                            <span class="badge {{ $status === 'aktif' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="openEditAdmin(this)" 
                                                        data-id="{{ $admin['id_admin'] }}"
                                                        data-username="{{ $admin['username'] ?? '' }}"
                                                        data-nama="{{ $admin['nama_lengkap'] ?? '' }}"
                                                        data-email="{{ $admin['email'] ?? '' }}"
                                                        data-role="{{ $admin['role'] ?? 'admin' }}"
                                                        data-status="{{ $admin['status'] ?? 'aktif' }}"
                                                        data-no-hp="{{ $admin['no_hp'] ?? '' }}"
                                                        data-alamat="{{ $admin['alamat'] ?? '' }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                @if(session('admin_id') !== $admin['id_admin'])
                                                    <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteAdmin({{ $admin['id_admin'] }})">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-info-circle fs-1 mb-2"></i>
                                                <p>Tidak ada data admin</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTambahLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Admin Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambah_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="tambah_username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tambah_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="tambah_nama" name="nama_lengkap" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambah_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="tambah_email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tambah_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="tambah_password" name="password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambah_no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="tambah_no_hp" name="no_hp">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tambah_role" class="form-label">Role</label>
                            <select class="form-select" id="tambah_role" name="role" required>
                                <option value="operator">Operator</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tambah_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="tambah_alamat" name="alamat" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Admin -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditLabel">
                    <i class="bi bi-pencil me-2"></i>Edit Admin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_admin" id="edit_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama_lengkap" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="operator">Operator</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));

    function openTambahAdmin() {
        document.getElementById('formTambah').reset();
        modalTambah.show();
    }

    function openEditAdmin(button) {
        const data = button.dataset;
        document.getElementById('edit_id').value = data.id || '';
        document.getElementById('edit_username').value = data.username || '';
        document.getElementById('edit_nama').value = data.nama || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_role').value = data.role || 'admin';
        document.getElementById('edit_status').value = data.status || 'aktif';
        document.getElementById('edit_no_hp').value = data.noHp || '';
        document.getElementById('edit_alamat').value = data.alamat || '';
        modalEdit.show();
    }

    function deleteAdmin(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus admin ini?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id_admin', id);

        fetch('{{ url("/admin/pengaturan/action") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    document.getElementById('formTambah').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('{{ url("/admin/pengaturan/action") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                modalTambah.hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => alert('Terjadi kesalahan: ' + error));
    });

    document.getElementById('formEdit').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('{{ url("/admin/pengaturan/action") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                modalEdit.hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => alert('Terjadi kesalahan: ' + error));
    });
</script>
</body>
</html>
@endsection
