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
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.24);
            white-space: nowrap;
            transform: none;
            position: static;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1acc91 0%, #05a875 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(5, 150, 105, 0.3) !important;
        }

        .btn-primary:active {
            transform: scale(0.98);
            transition: all 0.1s ease;
        }

        .btn-primary:disabled,
        .btn-secondary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #6b7280;
            color: white;
            border: 1px solid #d1d5db;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #c1cec9 0%, #75817d 100%) !important;
            color: white !important;
            transform: scale(1.02);
        }

        .btn-secondary:active {
            transform: scale(0.98);
            transition: all 0.1s ease;
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
    @include('partials.greenpoint-theme')
</head>
<body>
    <div class="app">
        <!-- SIDEBAR -->
        @include('partials.sidebarNasabah')

        <!-- MAIN CONTENT -->
        <main class="main">
            @include('partials.nasabah-header', [
                'title' => 'Setor Sampah',
                'subtitle' => 'Ajukan setor sampah, tunggu persetujuan admin',
            ])

            <section class="grid">
                <!-- PROFILE SECTION -->
                <div class="card col-6">
                    <h3>Profil</h3>
                    <div style="margin-top: 8px;">
                        <div class="form-group">
                            <label>Nama</label>
                            <div>{{ htmlspecialchars($user['nama_nasabah'] ?? '') }}</div>
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <div>{{ htmlspecialchars($user['alamat'] ?? '') }}</div>
                        </div>
                    </div>
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
                            <input type="number" id="beratInput" min="1" step="0.01" placeholder="Minimal 1 kg">
                        </div>

                        <div class="form-actions">
                            <button type="button" id="addItemBtn" class="btn-primary">+ Tambah Item</button>
                            <button type="button" id="uploadPhotoBtn" class="btn-secondary">Upload Foto</button>
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
                        @include('partials.toast', ['type' => 'success', 'message' => $success])
                    @elseif($error)
                        @include('partials.toast', ['type' => 'danger', 'message' => $error])
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
        const formMsg = document.getElementById('formMsg');
        const csrfToken = setorForm.querySelector('input[name="_token"]').value;
        const detectPhotoUrl = "{{ route('nasabah.setor.detect-photo') }}";

        let items = [];
        let activePhotoChecks = 0;
        const formatRupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID', {
            maximumFractionDigits: 0
        });

        uploadPhotoBtn.addEventListener('click', function() {
            if (items.length === 0) {
                alert('Tambahkan item terlebih dahulu sebelum upload foto');
                return;
            }
            photoInput.click();
        });

        photoInput.addEventListener('change', async function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) {
                return;
            }

            if (files.length > 1) {
                alert('Upload satu foto untuk satu item. Foto pertama akan diperiksa.');
            }

            await processPhotoForItem(files[0], items.length - 1);
            photoInput.value = '';
        });

        function setPhotoChecking(isChecking) {
            activePhotoChecks += isChecking ? 1 : -1;
            activePhotoChecks = Math.max(0, activePhotoChecks);
            const disabled = activePhotoChecks > 0;

            uploadPhotoBtn.disabled = disabled;
            addItemBtn.disabled = disabled;
            setorForm.querySelector('button[type="submit"]').disabled = disabled;
            if (disabled) {
                formMsg.textContent = 'Memeriksa foto sampah...';
            }
        }

        function readFileAsDataUrl(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = event => resolve(event.target.result);
                reader.onerror = () => reject(new Error('Gagal membaca file gambar.'));
                reader.readAsDataURL(file);
            });
        }

        function validateImageFile(file) {
            if (!file.type.match('image.*')) {
                return 'File ' + file.name + ' bukan gambar.';
            }

            if (file.size > 2 * 1024 * 1024) {
                return 'Ukuran file ' + file.name + ' terlalu besar (maks 2MB).';
            }

            return null;
        }

        async function processPhotoForItem(file, itemIndex) {
            const item = items[itemIndex];
            if (!item) {
                alert('Item sampah tidak ditemukan.');
                return;
            }

            const fileError = validateImageFile(file);
            if (fileError) {
                alert(fileError);
                return;
            }

            setPhotoChecking(true);

            try {
                const dataUrl = await readFileAsDataUrl(file);
                const detection = await detectWastePhoto(dataUrl, item);

                items[itemIndex].photo = dataUrl;
                items[itemIndex].photoFile = file;
                items[itemIndex].photoDetection = detection;
                renderItems();
                formMsg.textContent = detection.message || 'Foto sesuai.';
            } catch (error) {
                formMsg.textContent = 'Foto ditolak. Silakan upload foto yang sesuai.';
                alert(error.message || 'Foto tidak lolos deteksi otomatis.');
            } finally {
                setPhotoChecking(false);
            }
        }

        async function detectWastePhoto(dataUrl, item) {
            const response = await fetch(detectPhotoUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    id_jenis: item.id,
                    photo: dataUrl
                })
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.valid) {
                throw new Error(data.message || 'Foto tidak sesuai dengan jenis sampah yang dipilih.');
            }

            return data;
        }

        function renderItems() {
            itemsTableBody.innerHTML = '';
            let totalBerat = 0;
            let totalNilai = 0;

            items.forEach((it, idx) => {
                const tr = document.createElement('tr');
                const photoHtml = it.photo 
                    ? `<div class="photo-badge">Foto sesuai</div>`
                    : `<button type="button" class="btn-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="addPhotoToItem(${idx})">Tambah Foto</button>`;
                
                tr.innerHTML = `
                    <td>${it.nama}</td>
                    <td>${it.berat}</td>
                    <td>${formatRupiah(it.harga)}</td>
                    <td>${formatRupiah(it.subtotal)}</td>
                    <td style="text-align: center;">${photoHtml}</td>
                    <td><button type="button" data-idx="${idx}" class="remove-btn">Hapus</button></td>
                `;
                itemsTableBody.appendChild(tr);
                totalBerat += parseFloat(it.berat);
                totalNilai += parseFloat(it.subtotal);
            });

            totalBeratEl.textContent = totalBerat.toFixed(2);
            totalNilaiEl.textContent = formatRupiah(totalNilai);

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
            totalNilaiInput.value = totalNilai.toFixed(0);
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

        window.addPhotoToItem = function(itemIndex) {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/jpeg,image/png,image/jpg';
            fileInput.onchange = async function(e) {
                const file = e.target.files[0];
                if (file) {
                    await processPhotoForItem(file, itemIndex);
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
            const harga = Math.round(parseFloat(sel.dataset.harga || 0));
            const berat = parseFloat(beratInput.value || 0);

            if (berat < 1) {
                alert('Berat minimal 1 kg');
                return;
            }

            const existingIndex = items.findIndex(item => String(item.id) === String(id));
            if (existingIndex >= 0) {
                const existing = items[existingIndex];
                const newBerat = parseFloat(existing.berat) + berat;
                existing.berat = newBerat.toFixed(2);
                existing.subtotal = String(Math.round(harga * newBerat));
                items[existingIndex] = existing;
            } else {
                const subtotal = String(Math.round(harga * berat));
                items.push({
                    id,
                    nama,
                    berat: berat.toFixed(2),
                    harga: String(harga),
                    subtotal,
                    photo: null,
                    photoFile: null,
                    photoDetection: null
                });
            }

            renderItems();
            beratInput.value = '';
            jenisSelect.selectedIndex = 0;
            jenisSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });

        setorForm.addEventListener('submit', function(e) {
            if (activePhotoChecks > 0) {
                e.preventDefault();
                alert('Tunggu sampai pemeriksaan foto selesai.');
                return;
            }

            if (items.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item sebelum mengajukan setor');
                return;
            }
            
            const itemsWithoutPhoto = items.filter(item => !item.photo);
            if (itemsWithoutPhoto.length > 0) {
                e.preventDefault();
                alert('Setiap item wajib memiliki foto');
            }
        });
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
