<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil Saya | GreenPoint</title>
    
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

        .profile-content {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 24px 24px;
            align-items: flex-start;
        }

        .profile-left {
            flex: 0 0 300px;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .profile-form {
            flex: 1;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .avatar-initials {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #059669, #10b981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 48px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
            border: 4px solid white;
            flex-shrink: 0;
        }

        .profile-left h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
            word-break: break-word;
        }

        .saldo-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #bbf7d0;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .saldo-box span:first-child {
            font-size: 16px;
            color: #059669;
            font-weight: 500;
        }

        .saldo-box span:last-child {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
        }

        .btn-transaksi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transform: translate(0, -50%);
            background: transparent;
            color: #059669;
            border: 1px solid #059669;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 28px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-transaksi:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-transaksi:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .btn-transaksi svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .topup-box {
            margin-top: 16px;
            padding: 16px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .topup-title {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .topup-form {
            display: grid;
            gap: 10px;
        }

        .topup-label {
            font-size: 12px;
            color: #475569;
            font-weight: 500;
        }

        .topup-input-wrap {
            display: grid;
            grid-template-columns: 48px 1fr;
            align-items: center;
            border: 1px solid #cbd5f5;
            border-radius: 10px;
            background: white;
            overflow: hidden;
        }

        .topup-prefix {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            font-weight: 600;
            color: #059669;
            background: #ecfdf3;
            border-right: 1px solid #d1fae5;
        }

        .topup-input {
            border: none;
            height: 44px;
            padding: 0 12px;
            font-size: 16px;
            color: #0f172a;
            outline: none;
        }

        .btn-topup {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-topup:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(5, 150, 105, 0.25);
        }

        .topup-hint {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }

        .topup-status {
            font-size: 12px;
            font-weight: 500;
            min-height: 18px;
        }

        .topup-status.is-success {
            color: #16a34a;
        }

        .topup-status.is-error {
            color: #dc2626;
        }

        .form-group {
            margin-bottom: 20px;
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

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            background: #f9fafb;
            font-family: inherit;
            color: #1f2937;
        }

        .form-group input:read-only {
            background: #f3f4f6;
            cursor: not-allowed;
            color: #6b7280;
        }

        .form-actions {
            margin-top: 30px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transform: translate(0, -50%);
            background: transparent;
            color: #059669;
            border: 1px solid #059669;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 28px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-primary:active{
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .btn-primary svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
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

            .profile-content {
                flex-direction: column;
                gap: 20px;
                padding: 0 16px 16px 16px;
            }

            .profile-left {
                flex: none;
                width: 100%;
            }

            .profile-form {
                flex: none;
                width: 100%;
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
                'title' => 'Profil Saya',
                'subtitle' => 'Kelola informasi profil Anda',
            ])

            <section class="profile-content">
                <div class="profile-left">
                    <div class="avatar-initials">
                        {{ htmlspecialchars($initials) }}
                    </div>
                    <h2>{{ htmlspecialchars($user['nama_nasabah'] ?? 'User') }}</h2>
                    <div class="subtitle">{{ htmlspecialchars($user['email'] ?? '') }}</div>
                    <div class="saldo-box" role="region" aria-label="Saldo akun">
                        <span>Saldo</span>
                        <span id="saldo-amount">Rp {{ number_format((float)($user['saldo'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('nasabah.setor') }}" class="btn-transaksi">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 8 12 16"></polyline>
                            <polyline points="8 12 16 12"></polyline>
                        </svg>
                        Transaksi Setor
                    </a>
                    <div class="topup-box" role="region" aria-label="Top up saldo">
                        <div class="topup-title">Top Up Saldo</div>
                        <form id="topup-form" class="topup-form" method="post" action="{{ route('nasabah.topup.create') }}">
                            @csrf
                            <label class="topup-label" for="topup-amount">Nominal</label>
                            <div class="topup-input-wrap">
                                <span class="topup-prefix">Rp</span>
                                <input class="topup-input" type="number" id="topup-amount" name="nominal" min="10000" step="1000" placeholder="10000" required />
                            </div>
                            <button type="submit" class="btn-topup">Top Up Saldo</button>
                            <div id="topup-status" class="topup-status" aria-live="polite"></div>
                        </form>
                        <div class="topup-hint">Minimal Rp 10.000</div>
                    </div>
                </div>

                <form class="profile-form" aria-label="Form profil pengguna">
                    
                    <div class="form-group">
                        <label for="username">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="1"></circle>
                                <path d="M4 12h7M13 12h7"></path>
                            </svg>
                            Username
                        </label>
                        <input type="text" id="username" name="username" value="{{ htmlspecialchars($user['username'] ?? '') }}" readonly />
                    </div>

                    <div class="form-group">
                        <label for="name">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Nama Lengkap
                        </label>
                        <input type="text" id="name" name="name" value="{{ htmlspecialchars($user['nama_nasabah'] ?? '') }}" readonly />
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <svg viewBox="0 0 24 24">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m22 6-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 6"></path>
                            </svg>
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ htmlspecialchars($user['email'] ?? '') }}" readonly />
                    </div>

                    <div class="form-group">
                        <label for="no_hp">
                            <svg viewBox="0 0 24 24">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                <path d="M12 18h.01"></path>
                            </svg>
                            No. Handphone
                        </label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ htmlspecialchars($user['no_hp'] ?? 'Belum diisi') }}" readonly />
                    </div>

                    <div class="form-group">
                        <label for="alamat">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Alamat
                        </label>
                        <input type="text" id="alamat" name="alamat" value="{{ htmlspecialchars($user['alamat'] ?? 'Belum diisi') }}" readonly />
                    </div>

                    <div class="form-group">
                        <label for="tanggal_daftar">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <polyline points="16 2 16 6 8 6 8 2"></polyline>
                                <polyline points="3 10 21 10"></polyline>
                            </svg>
                            Tanggal Daftar
                        </label>
                        <input type="text" id="tanggal_daftar" name="tanggal_daftar" 
                               value="@if(isset($user['tanggal_daftar'])){{ date('d F Y', strtotime($user['tanggal_daftar'])) }}@else N/A @endif" 
                               readonly />
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('nasabah.profil.edit') }}" class="btn-primary">
                            <svg viewBox="0 0 24 24">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                            Ubah Profil
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <!-- Chat Bot -->
    @include('partials.chatbot')

    <script src="{{ env('MIDTRANS_IS_PROD') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        (function () {
            var form = document.getElementById('topup-form');
            if (!form) {
                return;
            }

            var statusEl = document.getElementById('topup-status');
            var amountInput = document.getElementById('topup-amount');
            var saldoEl = document.getElementById('saldo-amount');
            var csrfInput = form.querySelector('input[name="_token"]');
            var pollTimer = null;
            var lastOrderId = null;
            var maxPolls = 20;
            var pollCount = 0;
            var pollDelayMs = 3000;

            var setStatus = function (message, state) {
                if (!statusEl) {
                    return;
                }
                statusEl.textContent = message || '';
                statusEl.classList.remove('is-success', 'is-error');
                if (state === 'success') {
                    statusEl.classList.add('is-success');
                }
                if (state === 'error') {
                    statusEl.classList.add('is-error');
                }
            };

            var formatRupiah = function (value) {
                var num = Number(value || 0);
                if (Number.isNaN(num)) {
                    num = 0;
                }
                return 'Rp ' + num.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            };

            var stopPolling = function () {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            };

            var startPolling = function () {
                if (!lastOrderId) {
                    return;
                }

                stopPolling();
                pollCount = 0;

                pollTimer = setInterval(function () {
                    pollCount += 1;
                    if (pollCount > maxPolls) {
                        stopPolling();
                        return;
                    }

                    fetch("{{ route('nasabah.topup.status') }}?order_id=" + encodeURIComponent(lastOrderId), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (!result.ok || !result.data) {
                            return;
                        }

                        var status = result.data.transaction_status || result.data.status;
                        if (status === 'settlement' || status === 'capture') {
                            stopPolling();
                            if (saldoEl && typeof result.data.saldo !== 'undefined') {
                                saldoEl.textContent = formatRupiah(result.data.saldo);
                            }
                            setStatus('Pembayaran sukses. Saldo sudah diperbarui.', 'success');
                        }
                    })
                    .catch(function () {
                        // Ignore polling errors and keep trying until maxPolls.
                    });
                }, pollDelayMs);
            };

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!window.snap) {
                    setStatus('Pembayaran belum siap. Coba lagi nanti.', 'error');
                    return;
                }

                var nominal = parseInt(amountInput.value, 10);
                if (!nominal || nominal < 10000) {
                    setStatus('Nominal minimal Rp 10.000.', 'error');
                    return;
                }

                setStatus('Memproses top up...', null);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfInput ? csrfInput.value : ''
                    },
                    body: JSON.stringify({ nominal: nominal })
                })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (!result.ok) {
                        throw new Error(result.data && result.data.message ? result.data.message : 'Gagal membuat transaksi.');
                    }

                    if (!result.data || !result.data.token) {
                        throw new Error('Token pembayaran tidak tersedia.');
                    }

                    lastOrderId = result.data.order_id || null;

                    window.snap.pay(result.data.token, {
                        onSuccess: function () {
                            setStatus('Pembayaran sukses. Menunggu saldo diperbarui...', 'success');
                            startPolling();
                        },
                        onPending: function () {
                            setStatus('Pembayaran menunggu konfirmasi. Saldo akan diperbarui otomatis.', null);
                            startPolling();
                        },
                        onError: function () {
                            stopPolling();
                            setStatus('Pembayaran gagal. Silakan coba lagi.', 'error');
                        },
                        onClose: function () {
                            stopPolling();
                            setStatus('Popup pembayaran ditutup.', null);
                        }
                    });
                })
                .catch(function (error) {
                    setStatus(error.message || 'Terjadi kesalahan saat memproses top up.', 'error');
                });
            });
        })();
    </script>
</body>
</html>
