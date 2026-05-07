<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ubah Profil | GreenPoint</title>
    
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
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-content h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .header-content h2 svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .subtle {
            color: #6b7280;
            font-size: 14px;
        }

        .form-content {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 24px 24px 24px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }

        .form-group label svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            background: #f9fafb;
            font-family: inherit;
            color: #1f2937;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #059669;
            background: white;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            justify-content: flex-start;
        }

        .btn-primary,
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            border: 1px solid;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);
        }

        .btn-primary:active {
            transform: scale(0.98);
            transition: all 0.15s ease;
            box-shadow: 0 4px 8px rgba(5, 150, 105, 0.2);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: transparent;
            color: #6b7280;
            border-color: #d1d5db;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            color: #374151;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .btn-secondary:active {
            transform: scale(0.98);
            transition: all 0.15s ease;
            background: #e5e7eb;
        }

        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary svg,
        .btn-secondary svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .character-count {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            text-align: right;
        }

        .character-count.warning {
            color: #f59e0b;
        }

        .character-count.danger {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
            }

            .page-header {
                padding: 16px;
            }

            .header-content h2 {
                font-size: 20px;
            }

            .form-content {
                padding: 0 16px 16px 16px;
            }

            .form-container {
                padding: 20px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
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
                    <h2>
                        <svg viewBox="0 0 24 24">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                        </svg>
                        Ubah Profil
                    </h2>
                    <p class="subtle">Perbarui informasi profil Anda</p>
                </div>
            </div>

            <section class="form-content">
                <div class="form-container">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <svg viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 8 12 12"></polyline>
                                <circle cx="12" cy="16" r="1"></circle>
                            </svg>
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('nasabah.profil.update') }}" aria-label="Form ubah profil pengguna">
                        @csrf
                        
                        <!-- Username (Now Editable) -->
                        <div class="form-group">
                            <label for="username">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Username
                                <span style="font-size: 12px; color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="username" name="username" 
                                   value="{{ old('username', $user['username'] ?? '') }}"
                                   minlength="3"
                                   maxlength="50"
                                   pattern="[a-zA-Z0-9_]+"
                                   title="Username hanya boleh berisi huruf, angka, dan underscore"
                                   required />
                            <div class="character-count">
                                <span id="usernameCount">0</span>/50 karakter
                            </div>
                            <small style="display: block; margin-top: 4px; color: #6b7280; font-size: 12px;">
                                * Username hanya boleh berisi huruf, angka, dan underscore (_)
                            </small>
                            @error('username')
                                <span style="color: #dc2626; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="form-group">
                            <label for="nama_nasabah">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Nama Lengkap
                                <span style="font-size: 12px; color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="nama_nasabah" name="nama_nasabah" 
                                   value="{{ old('nama_nasabah', $user['nama_nasabah'] ?? '') }}"
                                   minlength="3"
                                   maxlength="100"
                                   required />
                            <div class="character-count">
                                <span id="namaCount">0</span>/100 karakter
                            </div>
                            @error('nama_nasabah')
                                <span style="color: #dc2626; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">
                                <svg viewBox="0 0 24 24">
                                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                    <path d="m22 6-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 6"></path>
                                </svg>
                                Email
                                <span style="font-size: 12px; color: #dc2626;">*</span>
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="{{ old('email', $user['email'] ?? '') }}"
                                   required />
                            @error('email')
                                <span style="color: #dc2626; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- No. Handphone -->
                        <div class="form-group">
                            <label for="no_hp">
                                <svg viewBox="0 0 24 24">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                    <path d="M12 18h.01"></path>
                                </svg>
                                No. Handphone
                            </label>
                            <input type="tel" id="no_hp" name="no_hp" 
                                   value="{{ old('no_hp', $user['no_hp'] ?? '') }}"
                                   placeholder="Contoh: 082123456789"
                                   pattern="[0-9]{10,13}"
                                   title="Nomor handphone harus terdiri dari 10-13 digit angka" />
                            <small style="display: block; margin-top: 4px; color: #6b7280; font-size: 12px;">
                                Contoh: 081234567890 (10-13 digit angka)
                            </small>
                            @error('no_hp')
                                <span style="color: #dc2626; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="form-group">
                            <label for="alamat">
                                <svg viewBox="0 0 24 24">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Alamat
                            </label>
                            <textarea id="alamat" name="alamat" 
                                      maxlength="500"
                                      placeholder="Masukkan alamat lengkap Anda">{{ old('alamat', $user['alamat'] ?? '') }}</textarea>
                            <div class="character-count">
                                <span id="alamatCount">0</span>/500 karakter
                            </div>
                            @error('alamat')
                                <span style="color: #dc2626; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Daftar (Read-only) -->
                        <div class="form-group" style="background: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <label for="tanggal_daftar" style="margin-bottom: 0;">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <polyline points="16 2 16 6 8 6 8 2"></polyline>
                                    <polyline points="3 10 21 10"></polyline>
                                </svg>
                                Tanggal Daftar
                            </label>
                            <div style="margin-top: 8px; font-weight: 500;">
                                @if(isset($user['tanggal_daftar']))
                                    {{ date('d F Y', strtotime($user['tanggal_daftar'])) }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <small style="display: block; margin-top: 8px; color: #6b7280; font-size: 12px;">
                                Tanggal pendaftaran tidak dapat diubah
                            </small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('nasabah.profil') }}" class="btn-secondary">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="19 12 5 12"></polyline>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Real-time character counter for username
        const usernameInput = document.getElementById('username');
        const usernameCount = document.getElementById('usernameCount');
        
        if (usernameInput) {
            function updateUsernameCount() {
                const length = usernameInput.value.length;
                usernameCount.textContent = length;
                
                const countElement = usernameCount.parentElement;
                if (length > 45) {
                    countElement.classList.add('warning');
                } else {
                    countElement.classList.remove('warning');
                }
                if (length >= 50) {
                    countElement.classList.add('danger');
                } else {
                    countElement.classList.remove('danger');
                }
            }
            
            usernameInput.addEventListener('input', updateUsernameCount);
            updateUsernameCount();
        }
        
        // Real-time character counter for nama lengkap
        const namaInput = document.getElementById('nama_nasabah');
        const namaCount = document.getElementById('namaCount');
        
        if (namaInput) {
            function updateNamaCount() {
                const length = namaInput.value.length;
                namaCount.textContent = length;
                
                const countElement = namaCount.parentElement;
                if (length > 90) {
                    countElement.classList.add('warning');
                } else {
                    countElement.classList.remove('warning');
                }
                if (length >= 100) {
                    countElement.classList.add('danger');
                } else {
                    countElement.classList.remove('danger');
                }
            }
            
            namaInput.addEventListener('input', updateNamaCount);
            updateNamaCount();
        }
        
        // Real-time character counter for alamat
        const alamatTextarea = document.getElementById('alamat');
        const alamatCount = document.getElementById('alamatCount');
        
        if (alamatTextarea) {
            function updateAlamatCount() {
                const length = alamatTextarea.value.length;
                alamatCount.textContent = length;
                
                const countElement = alamatCount.parentElement;
                if (length > 450) {
                    countElement.classList.add('warning');
                } else {
                    countElement.classList.remove('warning');
                }
                if (length >= 500) {
                    countElement.classList.add('danger');
                } else {
                    countElement.classList.remove('danger');
                }
            }
            
            alamatTextarea.addEventListener('input', updateAlamatCount);
            updateAlamatCount();
        }

        // Validate username format
        if (usernameInput) {
            usernameInput.addEventListener('input', function(e) {
                const regex = /^[a-zA-Z0-9_]*$/;
                if (!regex.test(this.value)) {
                    this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
                }
            });
        }

        // Validate phone number format
        const phoneInput = document.getElementById('no_hp');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 13) {
                    this.value = this.value.slice(0, 13);
                }
            });
        }
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>