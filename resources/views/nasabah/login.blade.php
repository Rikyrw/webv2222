<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Nasabah - GreenPoint</title>
    
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

        body.nasabah-login {
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

        .nasabah-login-shell {
            width: min(100%, 980px);
        }

        .login-layout {
            display: grid;
            grid-template-columns: minmax(280px, 0.86fr) minmax(360px, 1fr);
            min-height: 558px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--gp-border);
            border-radius: var(--gp-radius);
            box-shadow: 0 20px 52px rgba(15, 23, 42, 0.12);
        }

        .login-brand-panel {
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
            padding: 34px;
            color: #ffffff;
            overflow: hidden;
            background: #1f3f2a;
        }

        .login-brand-panel::before {
            position: absolute;
            inset: 0;
            content: "";
            pointer-events: none;
        }

        .login-brand-panel::before {
            inset: -34px 0;
            z-index: -1;
            background: url("{{ asset('images/bg-gunung.png') }}") center center / cover no-repeat;
            filter: saturate(1.14) contrast(1.18) brightness(0.86);
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
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.16);
        }

        .brand-logo img {
            width: 32px;
            height: 32px;
            object-fit: contain;
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

        .login-brand-panel .brand-title {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.28);
        }

        .login-brand-panel .brand-subtitle {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.28);
        }

        .brand-copy {
            display: grid;
            gap: 14px;
            max-width: 300px;
        }

        .brand-copy h2 {
            margin: 0;
            color: #ffffff;
            font-size: 31px;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: 0;
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.38);
        }

        .brand-features {
            display: grid;
            gap: 9px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 39px;
            padding: 10px 12px;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .brand-feature i {
            font-size: 16px;
        }

        .login-form-panel {
            display: flex;
            align-items: center;
            padding: 40px;
            background: #ffffff;
        }

        .login-form-wrap {
            width: 100%;
            max-width: 430px;
            margin: 0 auto;
        }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
        }

        .mobile-brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .mobile-brand .brand-title {
            color: var(--gp-green);
        }

        .mobile-brand .brand-subtitle {
            color: var(--gp-muted);
        }

        .login-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 29px;
            padding: 6px 10px;
            margin-bottom: 16px;
            color: var(--gp-green);
            background: var(--gp-green-soft);
            border: 1px solid #d7e8dc;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
        }

        .login-title {
            margin: 0 0 8px;
            color: var(--gp-green);
            font-size: 28px;
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: 0;
        }

        .login-description {
            margin: 0 0 26px;
            color: var(--gp-muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .nasabah-login .gp-toast {
            max-width: 100%;
            margin-bottom: 16px;
        }

        .login-form {
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
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

        .nasabah-login .input {
            width: 100%;
            min-height: 42px;
            padding: 9px 12px !important;
            border-radius: 7px !important;
            font-size: 13px !important;
        }

        .password-field {
            position: relative;
        }

        .nasabah-login .password-field .input {
            padding-right: 48px !important;
        }

        .toggle-pass {
            position: absolute;
            top: 50%;
            right: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            color: #516156;
            background: #f4f6f4;
            border: 1px solid #d8e1da;
            border-radius: 7px;
            cursor: pointer;
            transform: translateY(-50%);
            transition: background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease;
        }

        .toggle-pass:hover,
        .toggle-pass:focus {
            color: #ffffff;
            background: var(--gp-green);
            border-color: var(--gp-green);
            outline: none;
        }

        .toggle-pass:focus {
            box-shadow: 0 0 0 3px rgba(47, 95, 62, 0.12);
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 43px;
            margin-top: 4px;
            padding: 10px 14px;
            background: #059669;
            color: white;
            border: none;
            border-radius: 7px;
            box-shadow: 0 10px 22px rgba(47, 95, 62, 0.2);
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.2;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login:hover,
        .btn-login:focus {
            background: linear-gradient(135deg, #1acc91 0%, #05a875 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(5, 150, 105, 0.3) !important;
            outline: none;
            
        }

        .forgot-password {
            margin-top: 12px;
            font-size: 13px;
        }

        .forgot-password a {
            color: var(--gp-green);
            text-decoration: none;
            font-weight: 700;
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: var(--gp-green-dark);
        }

        .login-footer {
            margin-top: 18px;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }

        .login-footer a {
            color: var(--gp-green);
            text-decoration: none;
            font-weight: 700;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .nasabah-login .social-divider {
            color: #9aa09d;
            letter-spacing: 0.05em;
        }

        .nasabah-login .social-divider::before,
        .nasabah-login .social-divider::after {
            background: #e2e8e3;
        }

        .nasabah-login .google-auth-error,
        .nasabah-login .google-auth-disabled {
            border-radius: 8px;
        }

        @media (max-width: 860px) {
            body.nasabah-login {
                padding: 22px;
            }

            .login-layout {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .login-brand-panel {
                display: none;
            }

            .login-form-panel {
                padding: 30px;
            }

            .mobile-brand {
                display: flex;
            }
        }

        @media (max-width: 520px) {
            body.nasabah-login {
                align-items: start;
                padding: 16px;
            }

            .login-form-panel {
                padding: 22px;
            }

            .login-title {
                font-size: 24px;
            }

            .login-description {
                margin-bottom: 22px;
            }
        }
    </style>
</head>
<body class="nasabah-login">
    <main class="nasabah-login-shell">
        <section class="login-layout" aria-label="Login nasabah GreenPoint">
            <aside class="login-brand-panel" aria-hidden="true">
                <div class="brand-lockup">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                    </div>
                    <div>
                        <p class="brand-title">GreenPoint</p>
                        <p class="brand-subtitle">Nasabah Dashboard</p>
                    </div>
                </div>

                <div class="brand-copy">
                    <h2>Kelola saldo dan transaksi Anda.</h2>
                </div>

                <div class="brand-features">
                    <div class="brand-feature"><i class="icon-wallet"></i> Top Up & Tarik Saldo</div>
                    <div class="brand-feature"><i class="icon-send"></i> Transfer & Pembayaran</div>
                    <div class="brand-feature"><i class="icon-history"></i> Riwayat Transaksi</div>
                </div>
            </aside>

            <div class="login-form-panel">
                <div class="login-form-wrap">
                    <div class="mobile-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo">
                        <div>
                            <p class="brand-title">GreenPoint</p>
                            <p class="brand-subtitle">Nasabah Dashboard</p>
                        </div>
                    </div>

                    <div class="login-eyebrow"><i class="icon-user"></i> Akses Nasabah</div>
                    <h1 class="login-title">Login Nasabah</h1>
                    <p class="login-description">Masuk menggunakan akun nasabah yang sudah terdaftar di sistem GreenPoint.</p>

                    @if(session('error'))
                        @include('partials.toast', ['type' => 'danger', 'message' => session('error')])
                    @endif
                    @if($errors->any())
                        @include('partials.toast', ['type' => 'danger', 'messages' => $errors->all()])
                    @endif

                    <form class="login-form" method="POST" action="{{ route('nasabah.authenticate') }}">
                        @csrf

                        <div class="form-group">
                            <label class="field-label" for="username"><i class="icon-user"></i> Username atau Email</label>
                            <input 
                                class="input" 
                                type="text" 
                                id="username" 
                                name="username" 
                                required
                                autocomplete="username"
                                autocapitalize="none"
                                spellcheck="false"
                                placeholder="Masukkan username atau email"
                                value="{{ old('username') }}"
                            >
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="password"><i class="icon-key-round"></i> Password</label>
                            <div class="password-field">
                                <input 
                                    class="input" 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password"
                                >
                                <button type="button" id="togglePassword" class="toggle-pass" aria-label="Tampilkan password" aria-pressed="false">
                                    <i id="togglePasswordIcon" class="icon-eye"></i>
                                </button>
                            </div>
                        </div>

                        <p class="forgot-password">
                            <a href="{{ route('nasabah.password.request') }}">Lupa password?</a>
                        </p>

                        <button type="submit" class="btn-login">
                            <i class="icon-log-in"></i> Masuk ke Dashboard
                        </button>
                    </form>

                    @include('nasabah.partials.google-sso', [
                        'buttonId' => 'google-login-button',
                        'errorId' => 'google-login-error',
                        'buttonText' => 'signin_with',
                    ])

                    <p class="login-footer">
                        Belum punya akun? <a href="{{ route('nasabah.register') }}">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        toggleButton.addEventListener('click', function () {
            const shouldShow = passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute('type', shouldShow ? 'text' : 'password');
            toggleButton.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
            toggleButton.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
            toggleIcon.className = shouldShow ? 'icon-eye-off' : 'icon-eye';
        });
    </script>
</body>
</html>
