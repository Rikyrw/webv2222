<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - GreenPoint</title>

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

        body.forgot-password-page {
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

        .forgot-password-shell {
            width: min(100%, 820px);
        }

        .forgot-password-card {
            display: grid;
            grid-template-columns: minmax(250px, 0.82fr) minmax(360px, 1fr);
            min-height: 430px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--gp-border);
            border-radius: var(--gp-radius);
            box-shadow: 0 20px 52px rgba(15, 23, 42, 0.12);
        }

        .forgot-brand-panel {
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
            padding: 32px;
            color: #ffffff;
            overflow: hidden;
            background: #1f3f2a;
        }

        .forgot-brand-panel::before {
            position: absolute;
            inset: -32px 0;
            z-index: -2;
            content: "";
            background: url("{{ asset('images/bg-gunung.png') }}") center center / cover no-repeat;
            filter: saturate(1.14) contrast(1.18) brightness(0.8);
        }

        .forgot-brand-panel::after {
            position: absolute;
            inset: 0;
            z-index: -1;
            content: "";
            background: linear-gradient(180deg, rgba(18, 45, 29, 0.16), rgba(18, 45, 29, 0.4));
        }

        .brand-lockup,
        .mobile-brand {
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

        .brand-logo img,
        .mobile-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .brand-title,
        .brand-subtitle {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.28);
        }

        .brand-title {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
        }

        .brand-subtitle {
            margin: 5px 0 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
        }

        .forgot-brand-copy h2 {
            max-width: 250px;
            margin: 0 0 10px;
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.12;
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.38);
        }

        .forgot-brand-copy p {
            max-width: 250px;
            margin: 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 13px;
            line-height: 1.55;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.28);
        }

        .forgot-brand-note {
            display: flex;
            align-items: center;
            gap: 9px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }

        .forgot-form-panel {
            display: flex;
            align-items: center;
            padding: 40px;
            background: #ffffff;
        }

        .forgot-form-wrap {
            width: 100%;
            max-width: 390px;
            margin: 0 auto;
        }

        .mobile-brand {
            display: none;
            margin-bottom: 22px;
        }

        .mobile-brand .brand-title {
            color: var(--gp-green);
            text-shadow: none;
        }

        .mobile-brand .brand-subtitle {
            color: var(--gp-muted);
            text-shadow: none;
        }

        .forgot-eyebrow {
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

        .forgot-title {
            margin: 0 0 8px;
            color: var(--gp-green);
            font-size: 28px;
            font-weight: 800;
            line-height: 1.12;
        }

        .forgot-description {
            margin: 0 0 22px;
            color: var(--gp-muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .forgot-password-page .gp-toast {
            max-width: 100%;
            margin-bottom: 16px;
        }

        .forgot-form {
            display: grid;
            gap: 14px;
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

        .forgot-password-page .input {
            width: 100%;
            min-height: 42px;
            padding: 9px 12px !important;
            border-radius: 7px !important;
            font-size: 13px !important;
        }

        .form-hint {
            margin: 0;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 43px;
            margin-top: 2px;
            padding: 10px 14px;
            color: #ffffff;
            background: #059669;
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

        .btn-reset:hover,
        .btn-reset:focus {
            background: linear-gradient(135deg, #1acc91 0%, #05a875 100%);
            box-shadow: 0 20px 45px rgba(5, 150, 105, 0.3);
            outline: none;
            transform: translateY(-2px);
        }

        .forgot-footer {
            margin: 18px 0 0;
            color: var(--gp-muted);
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }

        .forgot-footer a {
            color: var(--gp-green);
            text-decoration: none;
            font-weight: 700;
        }

        .forgot-footer a:hover {
            color: var(--gp-green-dark);
            text-decoration: underline;
        }

        @media (max-width: 720px) {
            body.forgot-password-page {
                align-items: start;
                padding: 16px;
            }

            .forgot-password-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .forgot-brand-panel {
                display: none;
            }

            .forgot-form-panel {
                padding: 26px 22px;
            }

            .mobile-brand {
                display: flex;
            }

            .forgot-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body class="forgot-password-page">
    <main class="forgot-password-shell">
        <section class="forgot-password-card" aria-label="Reset password nasabah GreenPoint">
            <aside class="forgot-brand-panel" aria-hidden="true">
                <div class="brand-lockup">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                    </div>
                    <div>
                        <p class="brand-title">GreenPoint</p>
                        <p class="brand-subtitle">Nasabah Dashboard</p>
                    </div>
                </div>

                <div class="forgot-brand-copy">
                    <h2>Pulihkan akses akun Anda.</h2>
                    <p>Gunakan email yang sudah terdaftar agar kami dapat mengirimkan link reset dengan aman.</p>
                </div>

                <div class="forgot-brand-note">
                    <i class="icon-shield-check"></i>
                    Link reset dikirim langsung ke inbox Anda.
                </div>
            </aside>

            <div class="forgot-form-panel">
                <div class="forgot-form-wrap">
                    <div class="mobile-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="GreenPoint Logo">
                        <div>
                            <p class="brand-title">GreenPoint</p>
                            <p class="brand-subtitle">Nasabah Dashboard</p>
                        </div>
                    </div>

                    <div class="forgot-eyebrow"><i class="icon-key-round"></i> Pemulihan Akun</div>
                    <h1 class="forgot-title">Lupa Password</h1>
                    <p class="forgot-description">
                        Masukkan email akun manual Anda. Kami akan mengirimkan link untuk membuat password baru.
                    </p>

                    @if (session('success'))
                        @include('partials.toast', ['type' => 'success', 'message' => session('success')])
                    @endif

                    @if ($errors->any())
                        @include('partials.toast', ['type' => 'danger', 'messages' => $errors->all()])
                    @endif

                    <form class="forgot-form" method="POST" action="{{ route('nasabah.password.email') }}">
                        @csrf

                        <div class="form-group">
                            <label class="field-label" for="email"><i class="icon-mail"></i> Email</label>
                            <input
                                class="input"
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Masukkan email terdaftar"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                autocapitalize="none"
                                spellcheck="false"
                                required
                            >
                            <p class="form-hint">Pastikan email sudah diverifikasi dan dapat Anda akses.</p>
                        </div>

                        <button type="submit" class="btn-reset">
                            <i class="icon-send"></i> Kirim Link Reset
                        </button>
                    </form>

                    <p class="forgot-footer">
                        Ingat password? <a href="{{ route('nasabah.login') }}">Kembali ke login</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
