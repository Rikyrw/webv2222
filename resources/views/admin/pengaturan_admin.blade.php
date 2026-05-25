@extends('layouts.app')

@section('content')
<div class="gp-page">
    <div class="card">
        <div class="card-body">
            <div class="gp-card-header">
                <div>
                    <h2 class="gp-title">Daftar Admin</h2>
                    <p class="gp-subtitle mb-0">Gunakan tombol edit dan hapus untuk mengelola akses.</p>
                </div>
                <button class="btn btn-primary" onclick="openTambahAdmin()">
                    <i class="bi bi-plus-circle"></i>Tambah Admin
                </button>
            </div>

            <div class="table-responsive">
                <table class="table gp-table gp-data-table table-hover align-middle" data-page-length="8">
                    <thead>
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
                                    <div class="d-flex justify-content-center flex-wrap gp-actions">
                                        <button class="btn btn-sm btn-secondary" onclick="openEditAdmin(this)"
                                                data-id="{{ $admin['id_admin'] }}"
                                                data-username="{{ $admin['username'] ?? '' }}"
                                                data-nama="{{ $admin['nama_lengkap'] ?? '' }}"
                                                data-email="{{ $admin['email'] ?? '' }}"
                                                data-role="{{ $admin['role'] ?? 'admin' }}"
                                                data-status="{{ $admin['status'] ?? 'aktif' }}"
                                                data-no-hp="{{ $admin['no_hp'] ?? '' }}"
                                                data-alamat="{{ $admin['alamat'] ?? '' }}">
                                            <i class="bi bi-pencil"></i>Edit
                                        </button>
                                        @if(session('admin_id') !== $admin['id_admin'])
                                            <button class="btn btn-sm btn-danger" onclick="deleteAdmin({{ $admin['id_admin'] }})">
                                                <i class="bi bi-trash"></i>Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="gp-empty">Tidak ada data admin</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Admin Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tambah_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="tambah_username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tambah_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="tambah_nama" name="nama_lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tambah_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="tambah_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tambah_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="tambah_password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tambah_no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="tambah_no_hp" name="no_hp">
                        </div>
                        <div class="col-md-6">
                            <label for="tambah_role" class="form-label">Role</label>
                            <select class="form-select" id="tambah_role" name="role" required>
                                <option value="operator">Operator</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="tambah_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="tambah_alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil me-2"></i>Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_admin" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama_lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="operator">Operator</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
        const editRole = document.getElementById('edit_role');
        const editStatus = document.getElementById('edit_status');
        editRole.value = data.role || 'admin';
        editStatus.value = data.status || 'aktif';
        editRole.dispatchEvent(new Event('change', { bubbles: true }));
        editStatus.dispatchEvent(new Event('change', { bubbles: true }));
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
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
        .catch(error => alert('Terjadi kesalahan: ' + error));
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
@endpush
@endsection
