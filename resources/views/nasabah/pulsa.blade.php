<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenPoint - Pulsa</title>

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
            background: transparent;
            color: #059669;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.24);
            transform: none;
            position: static;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1acc91 0%, #05a875 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(5, 150, 105, 0.3) !important;
        }
        
        .btn-primary:active {
            transform: translate(0, -5%) scale(0.98);
            transition: all 0.1s ease;
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
    @include('partials.greenpoint-theme')
    <style>
        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            width: 100%;
        }

        .app {
            display: block;
            width: 100%;
            max-width: 100%;
            min-height: auto;
            overflow-x: hidden;
        }

        .app .main {
            width: min(100%, 760px);
            max-width: 760px;
            min-width: 0;
            margin: 0 auto !important;
        }

        .card,
        .pulsa-form,
        .form-row,
        .form-group,
        .select-wrapper {
            min-width: 0;
            max-width: 100%;
        }

        .input-with-value {
            min-width: 0;
        }

        .input-with-value input {
            min-width: 0;
        }

        .edit-btn {
            width: 44px;
            height: 44px;
            min-width: 44px;
            flex: 0 0 44px;
            padding: 0 !important;
            border-radius: 12px !important;
        }

        .back-btn {
            position: static !important;
            transform: none !important;
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .app .main {
                padding: 0 !important;
            }

            .card {
                width: 100%;
                padding: 24px 18px !important;
                border-radius: 14px !important;
            }

            .nasabah-page-header {
                gap: 12px !important;
            }

            .nasabah-header-side {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- Sidebar dihilangkan di mode form desain -->

        <main class="main">
            
            <div class="card">
                @include('partials.nasabah-header', [
                    'title' => 'Pulsa',
                    'subtitle' => 'Isi pulsa dengan mudah',
                    'showMeta' => false,
                    'actionUrl' => Route::has('nasabah.dashboard') ? route('nasabah.dashboard') : url('/'),
                    'actionLabel' => 'Kembali',
                ])
                <h3>Form Isi Pulsa</h3>
                <form method="POST" action="{{ Route::has('nasabah.pulsa.store') ? route('nasabah.pulsa.store') : '#' }}" class="pulsa-form">
                    @csrf
                    <input type="hidden" name="service_type" value="pulsa">

                    <div class="form-group">
                        <label for="phoneNumber">Nomor Tujuan</label>
                        <div class="input-with-value">
                            <input type="text" id="phoneNumber" name="target" value="{{ old('target') }}" placeholder="Masukkan nomor HP" required>
                            <button type="button" class="edit-btn" title="Edit nomor"><i data-lucide="edit-2"></i></button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Operator</label>
                            <div class="select-wrapper">
                                <select id="category" name="category" required>
                                    <option value="TELKOMSEL" {{ old('category') == 'TELKOMSEL' ? 'selected' : '' }}>Telkomsel</option>
                                    <option value="INDOSAT" {{ old('category') == 'INDOSAT' ? 'selected' : '' }}>Indosat</option>
                                    <option value="XL" {{ old('category') == 'XL' ? 'selected' : '' }}>XL</option>
                                </select>
                                <i data-lucide="chevron-down"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nominal">Nominal (Rp)</label>
                            <div class="select-wrapper">
                                <select id="nominal" name="nominal" required>
                                    @for ($v = 5000; $v <= 50000; $v += 5000)
                                        <option value="{{ $v }}" {{ old('nominal') == $v ? 'selected' : ($v == 50000 ? 'selected' : '') }}>
                                            {{ number_format($v,0,',','.') }}
                                        </option>
                                    @endfor
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

                <div class="saldo-info">Saldo Anda saat ini: <strong>Rp {{ number_format((float)($user['saldo'] ?? 0),0,',','.') }}</strong></div>

                @if(session('error'))
                    @include('partials.toast', ['type' => 'danger', 'message' => session('error')])
                @endif
                @if(session('success'))
                    @include('partials.toast', ['type' => 'success', 'message' => session('success')])
                @endif
                @if($errors->any())
                    @include('partials.toast', ['type' => 'danger', 'messages' => $errors->all()])
                @endif
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();

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

        document.querySelector('.pulsa-form')?.addEventListener('submit', function(e) {
            const target = document.getElementById('phoneNumber')?.value.trim() || '';
            const nominal = parseInt(document.getElementById('nominal')?.value || '0', 10);
            if (!target) { e.preventDefault(); alert('Harap masukkan nomor tujuan!'); return false; }
            if (isNaN(nominal) || nominal < 5000 || nominal > 50000 || (nominal % 5000) !== 0) { e.preventDefault(); alert('Nominal tidak valid. Pilih kelipatan 5.000 (min 5.000, max 50.000).'); return false; }
            if (target.length < 5) { e.preventDefault(); alert('Nomor tujuan tidak valid. Minimal 5 digit.'); return false; }
            return true;
        });

        const pulsaForm = document.querySelector('.pulsa-form');
        if (pulsaForm && pulsaForm.getAttribute('action') === '#') {
            pulsaForm.addEventListener('submit', function(e) { e.preventDefault(); alert('Mode desain: backend tidak tersedia. Form tidak dikirim.'); return false; });
        }
    </script>

    <!-- Chat Bot -->
    @include('partials.chatbot')
</body>
</html>
