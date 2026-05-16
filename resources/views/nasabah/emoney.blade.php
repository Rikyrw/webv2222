<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenPoint • E-money</title>

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
            background: radial-gradient(circle at top, rgba(16, 185, 129, 0.16), transparent 28%),
                        linear-gradient(180deg, #f8fdf9 0%, #eef7f1 100%);
            min-height: 100vh;
            padding: 24px;
            color: #0f172a;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            margin: 0 auto;
            padding: 24px;
            max-width: 980px;
        }

        .page-header {
            margin-bottom: 24px;
            position: relative;
        }

        .header-content h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .header-content p {
            color: #6b7280;
            font-size: 14px;
        }

        .card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
            background-image: linear-gradient(180deg, rgba(16, 185, 129, 0.08) 0%, rgba(255,255,255,0) 100%);
        }

        .card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-with-value {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .input-with-value input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            font-size: 14px;
            font-family: inherit;
            background-color: white;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-with-value input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .edit-btn {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .edit-btn:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .edit-btn i {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            color: #4b5563;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            font-size: 14px;
            font-family: inherit;
            background-color: white;
            appearance: none;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
        }

        .select-wrapper select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .select-wrapper i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 18px;
            height: 18px;
            stroke: currentColor;
            color: #6b7280;
        }

        .form-actions {
            margin-top: 24px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.24);
        }

        .btn-primary:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary i {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .back-btn {
            position: absolute;
            right: 0;
            top: 0;
            transform: translate(0, -50%);
            background: transparent;
            color: #059669;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-btn:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .back-btn:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .saldo-info {
            margin-top: 16px;
            padding: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            font-size: 14px;
            color: #1f2937;
        }

        .saldo-info strong {
            color: #059669;
            font-weight: 700;
        }

        .error-message {
            color: #b91c1c;
            background: #fee2e2;
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .success-message {
            color: #065f46;
            background: #d1fae5;
            padding: 12px;
            border-radius: 6px;
            margin-top: 16px;
            font-size: 14px;
            border-left: 4px solid #10b981;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 16px;
            }

            .header-content h2 {
                font-size: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar dihilangkan di mode form desain -->

        <!-- MAIN CONTENT -->
        <main class="main">

            <div class="card">
                <div class="page-header">
                <div class="header-content">
                    <h2>E-money</h2>
                    <p>Isi saldo e-money Anda dengan mudah</p>
                </div>
                <a href="{{ Route::has('nasabah.dashboard') ? route('nasabah.dashboard') : url('/') }}" class="back-btn">Kembali</a>
            </div>

                <h3>Form Isi Saldo E-money</h3>
                
                <form method="POST" action="{{ route('nasabah.emoney.store') }}" class="emoney-form">
                    @csrf
                    <input type="hidden" name="service_type" value="emoney">

                    <div class="form-group">
                        <label for="phoneNumber">Nomor Tujuan</label>
                        <div class="input-with-value">
                            <input type="text" 
                                   id="phoneNumber" 
                                   name="target" 
                                   value="{{ old('target') }}" 
                                   placeholder="Masukkan nomor HP / ID pengguna"
                                   required>
                            <button type="button" class="edit-btn" title="Edit nomor">
                                <i data-lucide="edit-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Kategori E-money</label>
                            <div class="select-wrapper">
                                <select id="category" name="category" required>
                                    <option value="DANA" {{ old('category') == 'DANA' ? 'selected' : '' }}>DANA</option>
                                    <option value="GOPAY" {{ old('category') == 'GOPAY' ? 'selected' : '' }}>GoPay</option>
                                </select>
                                <i data-lucide="chevron-down"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nominal">Nominal (Rp)</label>
                            <div class="select-wrapper">
                                <select id="nominal" name="nominal" required>
                                    @foreach ([20000, 50000, 100000] as $v)
                                        <option value="{{ $v }}" {{ old('nominal') == $v ? 'selected' : ($v == 50000 ? 'selected' : '') }}>
                                            {{ number_format($v, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            Proses Pembayaran
                        </button>
                    </div>
                </form>

                <!-- Informasi Saldo -->
                <div class="saldo-info">
                    Saldo Anda saat ini: <strong>Rp {{ number_format((float)($saldo_val ?? 0), 0, ',', '.') }}</strong>
                </div>

                <!-- Pesan Error/Success -->
                @if(session('error'))
                    <div class="error-message">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        // Inisialisasi icon Lucide
        lucide.createIcons();

        // Fungsi edit nomor
        document.querySelectorAll('.edit-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetInput = document.getElementById('phoneNumber');
                if (targetInput) {
                    targetInput.removeAttribute('readonly');
                    targetInput.focus();
                    targetInput.select();
                }
            });
        });

        // Validasi form sebelum submit
        document.querySelector('.emoney-form')?.addEventListener('submit', function(e) {
            const target = document.getElementById('phoneNumber')?.value.trim() || '';
            const nominal = parseInt(document.getElementById('nominal')?.value || '0', 10);
            
            if (!target) {
                e.preventDefault();
                alert('Harap masukkan nomor tujuan!');
                return false;
            }
            
            if (isNaN(nominal) || ![20000, 50000, 100000].includes(nominal)) {
                e.preventDefault();
                alert('Nominal tidak valid. Pilih 20.000, 50.000, atau 100.000.');
                return false;
            }
            
            // Nomor tujuan minimal 5 digit (validasi sederhana)
            if (target.length < 5) {
                e.preventDefault();
                alert('Nomor tujuan tidak valid. Minimal 5 digit.');
                return false;
            }
            
            return true;
        });
        
        // Set initial readonly state untuk nomor tujuan
        const targetInput = document.getElementById('phoneNumber');
        if (targetInput && !targetInput.value) {
            targetInput.removeAttribute('readonly');
        } else if (targetInput) {
            targetInput.setAttribute('readonly', 'readonly');
        }

        const emoneyForm = document.querySelector('.emoney-form');
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>