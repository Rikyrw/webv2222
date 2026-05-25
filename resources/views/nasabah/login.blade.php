<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Nasabah - GreenPoint</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #0f172a;
            background: radial-gradient(circle at top, rgba(16, 185, 129, 0.16), transparent 28%),
                        linear-gradient(180deg, #f8fdf9 0%, #eef7f1 100%);
        }

        /* Container utama */
        .login-container {
            width: 100%;
            max-width: 448px;
            animation: fadeUp 0.3s ease-out;
        }

        /* Gaya untuk card */
        .login-container {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header card */
        h2 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-align: center;
            color: #0f172a;
            margin-bottom: 8px;
        }

        /* Tambahkan subtitle */
        h2::after {
            content: "Masuk ke akun nasabah Anda";
            display: block;
            font-size: 14px;
            font-weight: 400;
            color: #64748b;
            margin-top: 8px;
        }

        /* Area atas card (seperti brand mark di admin) */
        .login-container::before {
            content: "🌱";
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin: 28px auto 18px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            font-size: 32px;
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.28);
        }

        /* Head section gradient */
        .login-container {
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.08) 0%, rgba(255,255,255,0) 100%);
        }

        /* Card body padding */
        .login-container {
            padding: 0 28px 28px;
        }

        /* Style untuk form di dalam */
        form {
            margin-top: 0;
        }

        /* Error message styling */
        .error-message {
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            text-align: left;
        }

        /* Form group */
        .form-group {
            margin-bottom: 20px;
        }

        /* Input fields */
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            background: white;
            font-family: inherit;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder,
        input[type="email"]::placeholder {
            color: #9ca3af;
        }

        /* Button styling */
        button[name="login"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            margin-top: 8px;
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
            transform: none;
            position: static;
        }

        button[name="login"]:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }

        button[name="login"]:active {
            transform: translateY(0);
        }

        /* Footer text */
        .footer-text {
            margin-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            padding-bottom: 16px;
        }

        .footer-text a {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
            color: #059669;
        }

        .forgot-password {
            margin-top: -8px;
            margin-bottom: 18px;
            text-align: right;
            font-size: 13px;
        }

        .forgot-password a {
            color: #10b981;
            text-decoration: none;
            font-weight: 700;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            
            .login-container {
                padding: 0 20px 24px;
            }
        }
    </style>
    @include('partials.greenpoint-theme')
</head>
<body>
    <div class="login-container">
        <h2>Login Nasabah</h2>

        @if (session('error'))
            @include('partials.toast', ['type' => 'danger', 'message' => session('error')])
        @endif

        @if ($errors->any())
            @include('partials.toast', ['type' => 'danger', 'messages' => $errors->all()])
        @endif

        <form method="POST" action="{{ route('nasabah.authenticate') }}">
            @csrf

            <div class="form-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username atau Email" 
                    required
                    autocomplete="username"
                    autocapitalize="none"
                    spellcheck="false"
                    value="{{ old('username') }}"
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    required
                >
            </div>

            <p class="forgot-password">
                <a href="{{ route('nasabah.password.request') }}">Lupa password?</a>
            </p>

            <button type="submit" name="login">Login ke Dashboard</button>
        </form>

        @include('nasabah.partials.google-sso', [
            'buttonId' => 'google-login-button',
            'errorId' => 'google-login-error',
            'buttonText' => 'signin_with',
        ])

        <p class="footer-text">
            Belum punya akun? <a href="{{ route('nasabah.register') }}">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
