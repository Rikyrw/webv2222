@extends('layouts.app')

@section('content')
<div class="page-shell">
    <div class="section-header">
        <div>
            <div class="section-title">Pengaturan Admin</div>
            <div class="section-subtitle">Kelola akun administrator. Hanya tersedia untuk super admin.</div>
        </div>

        <button onclick="openTambahAdmin()" class="btn btn-primary">
            <i class="lucide-plus"></i>
            Tambah Admin
        </button>
    </div>

    <div class="section-card">
        <div class="toolbar" style="margin-bottom: 16px;">
            <div>
                <div style="font-size: 18px; font-weight: 800; color: #0f172a;">Daftar Admin</div>
                <div class="section-subtitle">Gunakan tombol edit dan hapus untuk mengelola akses.</div>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin['username'] ?? '-' }}</td>
                            <td>{{ $admin['nama_lengkap'] }}</td>
                            <td>{{ $admin['email'] }}</td>
                            <td>
                                <span class="status-pill status-success" style="background:#eefaf4; color:#047857;">{{ ucfirst(str_replace('superadmin', 'Super Admin', $admin['role'])) }}</span>
                            </td>
                            <td>
                                @php $status = $admin['status'] ?? 'aktif'; @endphp
                                <span class="status-pill {{ $status === 'aktif' ? 'status-success' : 'status-danger' }}">{{ $status }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                                    <button
                                        class="btn btn-info js-edit-admin"
                                        style="padding: 8px 12px;"
                                        data-id="{{ $admin['id_admin'] }}"
                                        data-username="{{ $admin['username'] ?? '' }}"
                                        data-nama="{{ $admin['nama_lengkap'] ?? '' }}"
                                        data-email="{{ $admin['email'] ?? '' }}"
                                        data-role="{{ $admin['role'] ?? 'admin' }}"
                                        data-status="{{ $admin['status'] ?? 'aktif' }}"
                                        data-no-hp="{{ $admin['no_hp'] ?? '' }}"
                                        data-alamat="{{ $admin['alamat'] ?? '' }}"
                                    >Edit</button>
                                    @if(auth()->user()->id !== $admin['id_admin'])
                                        <button class="btn btn-danger js-delete-admin" style="padding: 8px 12px;" data-id="{{ $admin['id_admin'] }}">Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: #64748b;">Tidak ada data admin</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:999;"></div>

<div id="modalTambah" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border-radius:24px; padding:28px; z-index:1000; width:min(560px, 92vw); max-height:90vh; overflow:auto; box-shadow:0 30px 80px rgba(15,23,42,0.18); border:1px solid #e5e7eb;">
    <div class="section-title" style="font-size:22px; margin-bottom: 8px;">Tambah Admin Baru</div>
    <div class="section-subtitle" style="margin-bottom: 18px;">Isi data admin yang akan ditambahkan ke sistem.</div>

    <form id="formTambah" style="display:grid; gap:14px;">
        <input type="hidden" name="action" value="add">

        <div class="form-group">
            <label>Username</label>
            <input class="input" type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input class="input" type="text" name="nama_lengkap" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input class="input" type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input class="input" type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>No. HP</label>
            <input class="input" type="text" name="no_hp">
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea class="input" name="alamat" style="min-height:88px; resize:vertical;"></textarea>
        </div>

        <div class="form-group">
            <label>Role</label>
            <select class="select" name="role" required>
                <option value="operator">Operator</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
            </select>
        </div>

        <div style="display:flex; gap:10px; margin-top: 6px;">
            <button type="button" onclick="closeModal()" class="btn btn-secondary" style="flex:1; justify-content:center;">Batal</button>
            <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan</button>
        </div>
    </form>
</div>

<div id="modalEdit" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; border-radius:24px; padding:28px; z-index:1000; width:min(560px, 92vw); max-height:90vh; overflow:auto; box-shadow:0 30px 80px rgba(15,23,42,0.18); border:1px solid #e5e7eb;">
    <div class="section-title" style="font-size:22px; margin-bottom: 8px;">Edit Admin</div>
    <div class="section-subtitle" style="margin-bottom: 18px;">Perbarui data admin yang dipilih.</div>

    <form id="formEdit" style="display:grid; gap:14px;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id_admin" id="edit_id">

        <div class="form-group">
            <label>Username</label>
            <input class="input" type="text" id="edit_username" name="username" required>
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input class="input" type="text" id="edit_nama" name="nama_lengkap" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input class="input" type="email" id="edit_email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password (kosongkan jika tidak diubah)</label>
            <input class="input" type="password" name="password">
        </div>

        <div class="form-group">
            <label>No. HP</label>
            <input class="input" type="text" id="edit_no_hp" name="no_hp">
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea class="input" id="edit_alamat" name="alamat" style="min-height:88px; resize:vertical;"></textarea>
        </div>

        <div class="form-group">
            <label>Role</label>
            <select class="select" id="edit_role" name="role" required>
                <option value="operator">Operator</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select class="select" id="edit_status" name="status" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>

        <div style="display:flex; gap:10px; margin-top: 6px;">
            <button type="button" onclick="closeModal()" class="btn btn-secondary" style="flex:1; justify-content:center;">Batal</button>
            <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Update</button>
        </div>
    </form>
</div>

<script>
    const overlay = document.getElementById('overlay');
    const modalTambah = document.getElementById('modalTambah');
    const modalEdit = document.getElementById('modalEdit');
    const formTambah = document.getElementById('formTambah');
    const formEdit = document.getElementById('formEdit');

    function openTambahAdmin() {
        overlay.style.display = 'block';
        modalTambah.style.display = 'block';
        formTambah.reset();
    }

    function openEditAdmin(admin) {
        overlay.style.display = 'block';
        modalEdit.style.display = 'block';
        document.getElementById('edit_id').value = admin.dataset.id || '';
        document.getElementById('edit_username').value = admin.dataset.username || '';
        document.getElementById('edit_nama').value = admin.dataset.nama || '';
        document.getElementById('edit_email').value = admin.dataset.email || '';
        document.getElementById('edit_role').value = admin.dataset.role || 'admin';
        document.getElementById('edit_status').value = admin.dataset.status || 'aktif';
        document.getElementById('edit_no_hp').value = admin.dataset.noHp || '';
        document.getElementById('edit_alamat').value = admin.dataset.alamat || '';
    }

    function closeModal() {
        overlay.style.display = 'none';
        modalTambah.style.display = 'none';
        modalEdit.style.display = 'none';
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

    formTambah.addEventListener('submit', function(e) {
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
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => alert('Terjadi kesalahan: ' + error));
    });

    formEdit.addEventListener('submit', function(e) {
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
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => alert('Terjadi kesalahan: ' + error));
    });

    overlay.addEventListener('click', closeModal);

    document.querySelectorAll('.js-edit-admin').forEach(function(button) {
        button.addEventListener('click', function() {
            openEditAdmin(this);
        });
    });

    document.querySelectorAll('.js-delete-admin').forEach(function(button) {
        button.addEventListener('click', function() {
            deleteAdmin(this.dataset.id);
        });
    });
</script>
@endsection
