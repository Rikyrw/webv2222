<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - GreenPoint</title>
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

        body.admin-login {
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

        .admin-login-shell {
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

        .brand-links {
            display: grid;
            gap: 9px;
        }

        .brand-link {
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

        .brand-link i {
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

        .admin-login .gp-toast {
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

        .admin-login .input,
        .admin-login .select {
            width: 100%;
            min-height: 42px;
            padding: 9px 12px !important;
            border-radius: 7px !important;
            font-size: 13px !important;
        }

        .password-field {
            position: relative;
        }

        .admin-login .password-field .input {
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

        .role-helper {
            margin: -4px 0 0;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.45;
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
            color: #ffffff;
            background: var(--gp-green) !important;
            border: 1px solid var(--gp-green);
            border-radius: 7px;
            box-shadow: 0 10px 22px rgba(47, 95, 62, 0.2) !important;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color 0.16s ease, border-color 0.16s ease, transform 0.16s ease, box-shadow 0.16s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: var(--gp-green-dark) !important;
            border-color: var(--gp-green-dark) !important;
            box-shadow: 0 12px 26px rgba(47, 95, 62, 0.24) !important;
            outline: none;
            transform: translateY(-1px);
        }

        .login-footer {
            margin-top: 18px;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }

        @media (max-width: 860px) {
            body.admin-login {
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
            body.admin-login {
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
<body class="admin-login">
    <main class="admin-login-shell">
        <section class="login-layout" aria-label="Login admin GreenPoint">
            <aside class="login-brand-panel" aria-hidden="true">
                <div class="brand-lockup">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                    </div>
                    <div>
                        <p class="brand-title">GreenPoint</p>
                        <p class="brand-subtitle">Admin Dashboard</p>
                    </div>
                </div>

                <div class="brand-copy">
                    <h2>Ruang kerja admin yang lebih tertata.</h2>
                </div>

                <div class="brand-links">
                    <div class="brand-link"><i class="icon-layout-dashboard"></i> Dashboard</div>
                    <div class="brand-link"><i class="icon-recycle"></i> Data Sampah</div>
                    <div class="brand-link"><i class="icon-chart-no-axes-combined"></i> Laporan</div>
                </div>
            </aside>

            <div class="login-form-panel">
                <div class="login-form-wrap">
                    <div class="mobile-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo">
                        <div>
                            <p class="brand-title">GreenPoint</p>
                            <p class="brand-subtitle">Admin Dashboard</p>
                        </div>
                    </div>

                    <div class="login-eyebrow"><i class="icon-shield-check"></i> Akses Admin</div>
                    <h1 class="login-title">Login Admin</h1>
                    <p class="login-description">Masuk menggunakan akun admin yang sudah terdaftar di sistem GreenPoint.</p>

                    @if(session('error'))
                        @include('partials.toast', ['type' => 'danger', 'message' => session('error')])
                    @endif
                    @if(session('success'))
                        @include('partials.toast', ['type' => 'success', 'message' => session('success')])
                    @endif
                    @if($errors->any())
                        @include('partials.toast', ['type' => 'danger', 'messages' => $errors])
                    @endif

                    <form class="login-form" method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-group">
                            <label class="field-label" for="email"><i class="icon-mail"></i> Email</label>
                            <input class="input" type="email" id="email" name="email" required value="{{ old('email') }}" autocomplete="username" autofocus>
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="password"><i class="icon-key-round"></i> Password</label>
                            <div class="password-field">
                                <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
                                <button type="button" id="togglePassword" class="toggle-pass" aria-label="Tampilkan password" aria-pressed="false">
                                    <i id="togglePasswordIcon" class="icon-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="field-label" for="role"><i class="icon-shield"></i> Login Sebagai</label>
                            <select class="select" id="role" name="role" required>
                                <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin Sistem</option>
                                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            <p class="role-helper">Pilih peran sesuai akun yang digunakan.</p>
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="icon-log-in"></i> Masuk ke Dashboard
                        </button>
                    </form>

                    <p class="login-footer">Akses ini hanya untuk pengelola GreenPoint.</p>
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

        document.getElementById('email').addEventListener('blur', function () {
            const email = this.value.toLowerCase();
            const roleSelect = document.getElementById('role');

            if (email.includes('superadmin')) {
                roleSelect.value = 'superadmin';
            } else if (email.includes('admin')) {
                roleSelect.value = 'admin';
            }

            roleSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
    </script>
</body>
</html>
