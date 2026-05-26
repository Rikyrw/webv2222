<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nasabah - GreenPoint</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@0.469.0/font/lucide.css" rel="stylesheet">
    @include('partials.greenpoint-theme')

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body.register-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            margin: 0;
            padding: 32px;
            color: var(--gp-text);
            background:
                linear-gradient(135deg, rgba(47, 95, 62, 0.08) 0 34%, rgba(47, 95, 62, 0) 34%),
                linear-gradient(180deg, #f8faf8 0%, var(--gp-bg) 100%) !important;
        }

        .register-shell {
            width: min(100%, 760px);
        }

        .register-card {
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--gp-border);
            border-radius: 14px;
            box-shadow: 0 20px 52px rgba(15, 23, 42, 0.12);
        }

        .register-hero {
            position: relative;
            isolation: isolate;
            display: grid;
            gap: 32px;
            min-height: 220px;
            padding: 32px;
            color: #ffffff;
            background: #1f3f2a;
            overflow: hidden;
        }

        .register-hero::before {
            position: absolute;
            inset: -28px 0;
            z-index: -2;
            content: "";
            background: url("{{ asset('images/bg-gunung.png') }}") center center / cover no-repeat;
            filter: saturate(1.14) contrast(1.18) brightness(0.78);
        }

        .register-hero::after {
            position: absolute;
            inset: 0;
            z-index: -1;
            content: "";
            background:
                linear-gradient(90deg, rgba(18, 45, 29, 0.42) 0%, rgba(18, 45, 29, 0.14) 64%, rgba(18, 45, 29, 0.04) 100%),
                linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.18));
        }

        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            display: grid;
            place-items: center;
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.16);
        }

        .brand-logo img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .brand-title,
        .brand-subtitle,
        .register-hero h1,
        .register-hero p {
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.34);
        }

        .brand-title {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: 0;
        }

        .brand-subtitle {
            margin: 5px 0 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
        }

        .register-hero-copy {
            max-width: 430px;
        }

        .register-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 29px;
            padding: 6px 10px;
            margin-bottom: 14px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
        }

        .register-hero h1 {
            margin: 0 0 8px;
            color: #ffffff;
            font-size: 31px;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: 0;
        }

        .register-hero p {
            max-width: 360px;
            margin: 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 13px;
            line-height: 1.55;
        }

        .register-body {
            padding: 28px 32px 30px;
        }

        .register-page .gp-toast {
            max-width: 100%;
            margin-bottom: 18px;
        }

        .register-form {
            display: grid;
            gap: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
            min-width: 0;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #35473c;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .field-label i {
            color: var(--gp-green);
            font-size: 15px;
        }

        .register-page .input {
            width: 100%;
            min-height: 42px;
            padding: 9px 12px !important;
            border-radius: 10px !important;
            font-size: 13px !important;
        }

        .register-page textarea.input {
            min-height: 92px;
            resize: vertical;
        }

        .password-hint,
        .password-feedback {
            color: var(--gp-muted);
            font-size: 12px;
            min-height: 18px;
            line-height: 1.45;
        }

        .password-match,
        .password-mismatch,
        .password-default {
            display: none;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 700;
        }

        .password-default {
            display: inline-flex;
            color: var(--gp-muted);
            font-weight: 400;
        }

        .password-feedback.has-result .password-default {
            display: none;
        }

        .password-match {
            color: #1c6b37;
        }

        .password-mismatch {
            color: #a61b1b;
        }

        .password-match.show,
        .password-mismatch.show {
            display: inline-flex;
        }

        .btn-register {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 43px;
            margin-top: 4px;
            padding: 10px 14px;
            color: #ffffff;
            background: var(--gp-green) !important;
            border: 1px solid var(--gp-green);
            border-radius: 10px;
            box-shadow: 0 10px 22px rgba(47, 95, 62, 0.2) !important;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color 0.16s ease, border-color 0.16s ease, transform 0.16s ease, box-shadow 0.16s ease;
        }

        .btn-register:hover,
        .btn-register:focus {
            background: var(--gp-green-dark) !important;
            border-color: var(--gp-green-dark) !important;
            box-shadow: 0 12px 26px rgba(47, 95, 62, 0.24) !important;
            outline: none;
            transform: translateY(-1px);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .register-page .social-auth {
            margin-top: 20px;
        }

        .register-page .social-divider {
            color: #9aa09d;
            letter-spacing: 0.05em;
        }

        .register-page .social-divider::before,
        .register-page .social-divider::after {
            background: #e2e8e3;
        }

        .register-page .google-auth-error,
        .register-page .google-auth-disabled {
            border-radius: 10px;
        }

        .footer-text {
            margin: 18px 0 0;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }

        .footer-text a {
            color: var(--gp-green);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-text a:hover {
            color: var(--gp-green-dark);
            text-decoration: underline;
        }

        @media (max-width: 720px) {
            body.register-page {
                align-items: start;
                padding: 16px;
            }

            .register-hero {
                min-height: 210px;
                padding: 26px 22px;
            }

            .register-body {
                padding: 24px 22px 26px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .register-hero h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body class="register-page">
    <main class="register-shell">
        <section class="register-card" aria-label="Daftar nasabah GreenPoint">
            <div class="register-hero">
                <div class="brand-lockup">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                    </div>
                    <div>
                        <p class="brand-title">GreenPoint</p>
                        <p class="brand-subtitle">Nasabah Dashboard</p>
                    </div>
                </div>

                <div class="register-hero-copy">
                    <div class="register-eyebrow"><i class="icon-user-plus"></i> Akun Nasabah</div>
                    <h1>Daftar Nasabah</h1>
                    <p>Lengkapi data diri untuk mulai mengelola saldo dan transaksi GreenPoint.</p>
                </div>
            </div>

            <div class="register-body">
                @if ($errors->any() || session('error'))
                    @include('partials.toast', [
                        'type' => 'danger',
                        'messages' => session('error') ? [session('error')] : $errors->all(),
                    ])
                @endif

                @if (session('success'))
                    @include('partials.toast', ['type' => 'success', 'message' => session('success')])
                @endif

                <form class="register-form" method="POST" action="{{ route('nasabah.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="field-label" for="username"><i class="icon-user"></i> Username</label>
                            <input
                                class="input"
                                type="text"
                                id="username"
                                name="username"
                                required
                                autocomplete="username"
                                autocapitalize="none"
                                spellcheck="false"
                                placeholder="Masukkan username"
                                value="{{ old('username') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="nama"><i class="icon-id-card"></i> Nama Lengkap</label>
                            <input
                                class="input"
                                type="text"
                                id="nama"
                                name="nama"
                                required
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('nama') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="email"><i class="icon-mail"></i> Email</label>
                            <input
                                class="input"
                                type="email"
                                id="email"
                                name="email"
                                required
                                autocomplete="email"
                                placeholder="nama@email.com"
                                value="{{ old('email') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="no_hp"><i class="icon-phone"></i> Nomor HP</label>
                            <input
                                class="input"
                                type="tel"
                                id="no_hp"
                                name="no_hp"
                                required
                                autocomplete="tel"
                                placeholder="+62 812 3456 7890"
                                value="{{ old('no_hp') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="password"><i class="icon-key-round"></i> Password</label>
                            <input
                                class="input"
                                type="password"
                                id="password"
                                name="password"
                                maxlength="8"
                                required
                                autocomplete="new-password"
                                placeholder="Maksimal 8 karakter"
                            >
                            <div class="password-hint">Gunakan maksimal 8 karakter.</div>
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="konfirmasi_password"><i class="icon-shield-check"></i> Konfirmasi Password</label>
                            <input
                                class="input"
                                type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                maxlength="8"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                            >
                            <div class="password-feedback" aria-live="polite">
                                <span class="password-default" id="password-default">Ulangi password yang sama.</span>
                                <span class="password-match" id="password-match">Password cocok</span>
                                <span class="password-mismatch" id="password-mismatch">Password tidak cocok</span>
                            </div>
                        </div>

                        <div class="form-group full">
                            <label class="field-label" for="alamat"><i class="icon-map-pin"></i> Alamat</label>
                            <textarea
                                class="input"
                                id="alamat"
                                name="alamat"
                                autocomplete="street-address"
                                placeholder="Masukkan alamat lengkap"
                            >{{ old('alamat') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="icon-user-plus"></i> Daftar
                    </button>
                </form>

                @include('nasabah.partials.google-sso', [
                    'buttonId' => 'google-register-button',
                    'errorId' => 'google-register-error',
                    'buttonText' => 'signup_with',
                ])

                <p class="footer-text">
                    Sudah punya akun? <a href="{{ route('nasabah.login') }}">Login di sini</a>
                </p>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const konfirmasi = document.getElementById('konfirmasi_password');
            const feedback = document.querySelector('.password-feedback');
            const match = document.getElementById('password-match');
            const mismatch = document.getElementById('password-mismatch');

            function validatePassword() {
                if (!password || !konfirmasi || !match || !mismatch) {
                    return;
                }

                if (password.value === '' || konfirmasi.value === '') {
                    feedback?.classList.remove('has-result');
                    match.classList.remove('show');
                    mismatch.classList.remove('show');
                    return;
                }

                feedback?.classList.add('has-result');

                if (password.value === konfirmasi.value) {
                    match.classList.add('show');
                    mismatch.classList.remove('show');
                } else {
                    match.classList.remove('show');
                    mismatch.classList.add('show');
                }
            }

            password?.addEventListener('input', validatePassword);
            konfirmasi?.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
