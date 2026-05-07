<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Setor Sampah | GreenPoint</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #d2d7dd;
            color: #1f2937;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            margin-left: 280px;
            padding: 0;
        }

        .page-header {
            background: white;
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 0px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-content h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .subtle {
            color: #6b7280;
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .col-6 {
            grid-column: span 1;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 16px;
        }

        .card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .card h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            color: #1f2937;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #059669;
            border: 1px solid #059669;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-primary:active {
            transform: scale(0.98);
            transition: all 0.1s ease;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            color: #374151;
            transform: scale(1.02);
        }

        .btn-danger {
            background: #dc2626;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .remove-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: #b91c1c;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .table thead {
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            color: #1f2937;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        /* Photo Upload Styles */
        .photo-upload {
            display: inline-block;
            position: relative;
        }

        .photo-preview {
            margin-top: 8px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .preview-item {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item .remove-photo {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-item .remove-photo:hover {
            background: #dc2626;
        }

        .photo-badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        .file-input-hidden {
            display: none;
        }

        #formMsg {
            color: #374151;
            font-size: 14px;
        }

        .success-message {
            color: #065f46;
            background: #d1fae5;
            padding: 10px 12px;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 14px;
            border-left: 4px solid #10b981;
        }

        .error-message {
            color: #b91c1c;
            background: #fee2e2;
            padding: 10px 12px;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .saldo-info {
            margin-top: 8px;
            font-size: 14px;
            color: #1f2937;
        }

        .saldo-info strong {
            color: #059669;
            font-weight: 600;
        }

        .totals-info {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 14px;
        }

        .totals-info strong {
            font-weight: 600;
            color: #059669;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
            }

            .grid {
                grid-template-columns: 1fr;
                padding: 16px;
                gap: 16px;
            }

            .col-6 {
                grid-column: span 1;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }

            .totals-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- SIDEBAR -->
        @include('partials.sidebarNasabah')

        <!-- MAIN CONTENT -->
        <main class="main">
            <div class="page-header">
                <div class="header-content">
                    <h2>Setor Sampah</h2>
                    <p class="subtle">Ajukan setor sampah, tunggu persetujuan admin</p>
                </div>
            </div>

            <section class="grid">
                <!-- PROFILE SECTION -->
                <div class="card col-6">
                    <h3>Profil</h3>
                    <form method="POST" style="margin-top: 8px;" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="form-group">
                            <label for="nama_nasabah">Nama</label>
                            <input type="text" id="nama_nasabah" name="nama_nasabah" value="{{ htmlspecialchars($user['nama_nasabah'] ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" id="alamat" name="alamat" value="{{ htmlspecialchars($user['alamat'] ?? '') }}">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Update Profil</button>
                        </div>

                        @if($profile_success)
                            <div class="success-message">{{ $profile_success }}</div>
                        @elseif($profile_error)
                            <div class="error-message">{{ $profile_error }}</div>
                        @endif
                    </form>
                </div>

                <!-- SETOR FORM SECTION -->
                <div class="card col-6">
                    <h3>Form Setor</h3>
                    <div class="saldo-info">Saldo: <strong>Rp {{ number_format((float)($user['saldo'] ?? 0), 0, ',', '.') }}</strong></div>

                    <form id="setorForm" method="POST" style="margin-top: 12px;" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="submit_transaction" value="1">

                        <div class="form-group">
                            <label for="jenisSelect">Pilih Jenis Sampah</label>
                            <select id="jenisSelect">
                                <option value="">-- Pilih jenis --</option>
                                @foreach($waste_types as $wt)
                                    <option value="{{ $wt['id_jenis'] }}" data-harga="{{ $wt['harga_per_kg'] }}">
                                        {{ htmlspecialchars($wt['nama_jenis']) }} - Rp {{ number_format((float)($wt['harga_per_kg'] ?? 0), 0, ',', '.') }}/kg
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="beratInput">Berat (kg)</label>
                            <input type="number" id="beratInput" min="0.01" step="0.01" value="0">
                        </div>

                        <div class="form-actions">
                            <button type="button" id="addItemBtn" class="btn-primary">+ Tambah Item</button>
                            <button type="button" id="uploadPhotoBtn" class="btn-secondary">📷 Upload Foto</button>
                            <input type="file" id="photoInput" accept="image/jpeg,image/png,image/jpg" multiple style="display: none;">
                            <span id="formMsg"></span>
                        </div>

                        <!-- Photo Preview -->
                        <div id="photoPreview" class="photo-preview"></div>

                        <h4 style="margin-top: 12px;">Daftar Item</h4>
                        <table id="itemsTable" class="table">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Berat (kg)</th>
                                    <th>Harga/kg</th>
                                    <th>Subtotal</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <div class="totals-info">
                            <div>Total Berat: <strong id="totalBerat">0</strong> kg</div>
                            <div>Total Nilai: <strong id="totalNilai">Rp 0</strong></div>
                        </div>

                        <div style="margin-top: 12px;">
                            <button type="submit" class="btn-primary" style="width: 100%;">Ajukan Setor</button>
                        </div>
                    </form>

                    @if($success)
                        <div class="success-message">{{ $success }}</div>
                    @elseif($error)
                        <div class="error-message">{{ $error }}</div>
                    @endif
                </div>
            </section>
        </main>
    </div>

    <script>
        const addItemBtn = document.getElementById('addItemBtn');
        const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        const jenisSelect = document.getElementById('jenisSelect');
        const beratInput = document.getElementById('beratInput');
        const itemsTableBody = document.querySelector('#itemsTable tbody');
        const totalBeratEl = document.getElementById('totalBerat');
        const totalNilaiEl = document.getElementById('totalNilai');
        const setorForm = document.getElementById('setorForm');

        let items = [];
        let currentPhotos = []; // Store base64 or file objects for photos

        // Handle photo upload button click
        uploadPhotoBtn.addEventListener('click', function() {
            if (items.length === 0) {
                alert('Tambahkan item terlebih dahulu sebelum upload foto');
                return;
            }
            photoInput.click();
        });

        // Handle file selection
        photoInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                if (!file.type.match('image.*')) {
                    alert('File ' + file.name + ' bukan gambar');
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file ' + file.name + ' terlalu besar (maks 2MB)');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(evt) {
                    // Assign photo to the last item added
                    if (items.length > 0) {
                        const lastIndex = items.length - 1;
                        items[lastIndex].photo = evt.target.result;
                        items[lastIndex].photoFile = file;
                        renderItems();
                    }
                };
                reader.readAsDataURL(file);
            });
            
            // Clear input
            photoInput.value = '';
        });

        function renderItems() {
            itemsTableBody.innerHTML = '';
            let totalBerat = 0;
            let totalNilai = 0;

            items.forEach((it, idx) => {
                const tr = document.createElement('tr');
                const photoHtml = it.photo 
                    ? `<div class="photo-badge">✓ Ada Foto</div>`
                    : `<button type="button" class="btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="addPhotoToItem(${idx})">Tambah Foto</button>`;
                
                tr.innerHTML = `
                    <td>${it.nama}</td>
                    <td>${it.berat}</td>
                    <td>Rp ${Number(it.harga).toLocaleString('id-ID')}</td>
                    <td>Rp ${Number(it.subtotal).toLocaleString('id-ID')}</td>
                    <td style="text-align: center;">${photoHtml}</td>
                    <td><button type="button" data-idx="${idx}" class="remove-btn">Hapus</button></td>
                `;
                itemsTableBody.appendChild(tr);
                totalBerat += parseFloat(it.berat);
                totalNilai += parseFloat(it.subtotal);
            });

            totalBeratEl.textContent = totalBerat.toFixed(2);
            totalNilaiEl.textContent = 'Rp ' + totalNilai.toLocaleString('id-ID');

            // Remove existing dynamic inputs
            document.querySelectorAll('input[name^="waste_items"]').forEach(n => n.remove());
            document.querySelectorAll('input[name="total_berat"], input[name="total_nilai"]').forEach(n => n.remove());
            document.querySelectorAll('input[name^="waste_photos"]').forEach(n => n.remove());

            // Add hidden inputs for form submission
            items.forEach((it, i) => {
                const idJenis = document.createElement('input');
                idJenis.type = 'hidden';
                idJenis.name = `waste_items[${i}][id_jenis]`;
                idJenis.value = it.id;
                setorForm.appendChild(idJenis);

                const berat = document.createElement('input');
                berat.type = 'hidden';
                berat.name = `waste_items[${i}][berat]`;
                berat.value = it.berat;
                setorForm.appendChild(berat);

                const harga = document.createElement('input');
                harga.type = 'hidden';
                harga.name = `waste_items[${i}][harga]`;
                harga.value = it.harga;
                setorForm.appendChild(harga);

                const subtotal = document.createElement('input');
                subtotal.type = 'hidden';
                subtotal.name = `waste_items[${i}][subtotal]`;
                subtotal.value = it.subtotal;
                setorForm.appendChild(subtotal);

                // Add photo as base64 or file
                if (it.photo) {
                    const photoInput = document.createElement('input');
                    photoInput.type = 'hidden';
                    photoInput.name = `waste_photos[${i}]`;
                    photoInput.value = it.photo;
                    setorForm.appendChild(photoInput);
                }
            });

            // Add total hidden inputs
            const totalBeratInput = document.createElement('input');
            totalBeratInput.type = 'hidden';
            totalBeratInput.name = 'total_berat';
            totalBeratInput.value = totalBerat.toFixed(2);
            setorForm.appendChild(totalBeratInput);

            const totalNilaiInput = document.createElement('input');
            totalNilaiInput.type = 'hidden';
            totalNilaiInput.name = 'total_nilai';
            totalNilaiInput.value = totalNilai.toFixed(2);
            setorForm.appendChild(totalNilaiInput);

            // Attach remove handlers
            document.querySelectorAll('.remove-btn').forEach(b => {
                b.addEventListener('click', function(e) {
                    e.preventDefault();
                    const i = parseInt(this.dataset.idx, 10);
                    items.splice(i, 1);
                    renderItems();
                });
            });
        }

        // Function to add photo to existing item
        window.addPhotoToItem = function(itemIndex) {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/jpeg,image/png,image/jpg';
            fileInput.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (!file.type.match('image.*')) {
                        alert('File harus gambar');
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        items[itemIndex].photo = evt.target.result;
                        items[itemIndex].photoFile = file;
                        renderItems();
                    };
                    reader.readAsDataURL(file);
                }
            };
            fileInput.click();
        };

        addItemBtn.addEventListener('click', function() {
            const sel = jenisSelect.options[jenisSelect.selectedIndex];
            if (!sel || !sel.value) {
                alert('Pilih jenis sampah terlebih dahulu');
                return;
            }
            const id = sel.value;
            const nama = sel.textContent.split(' - ')[0].trim();
            const harga = parseFloat(sel.dataset.harga || 0);
            const berat = parseFloat(beratInput.value || 0);

            if (berat <= 0) {
                alert('Masukkan berat yang valid');
                return;
            }

            const subtotal = (harga * berat).toFixed(2);
            items.push({
                id,
                nama,
                berat: berat.toFixed(2),
                harga: harga.toFixed(2),
                subtotal,
                photo: null,
                photoFile: null
            });

            renderItems();
            beratInput.value = '0';
            jenisSelect.selectedIndex = 0;
        });

        setorForm.addEventListener('submit', function(e) {
            if (items.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item sebelum mengajukan setor');
                return;
            }
            
            // Optional: Check if all items have photos
            const itemsWithoutPhoto = items.filter(item => !item.photo);
            if (itemsWithoutPhoto.length > 0) {
                const confirmSubmit = confirm(`${itemsWithoutPhoto.length} item belum memiliki foto. Lanjutkan tanpa foto?`);
                if (!confirmSubmit) {
                    e.preventDefault();
                }
            }
        });
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>