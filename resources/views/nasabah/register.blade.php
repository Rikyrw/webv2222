<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nasabah - GreenPoint</title>
    
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

        .register-container {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 24px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            animation: fadeUp 0.3s ease-out;
            padding: 0;
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

        .register-container::before {
            content: "📝";
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

        /* Header styling */
        h2 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-align: center;
            color: #0f172a;
            margin-bottom: 8px;
            position: relative;
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.08) 0%, rgba(255,255,255,0) 100%);
            padding-top: 0;
        }

        /* Subtitle */
        h2::after {
            content: "Isi data diri Anda dengan lengkap";
            display: block;
            font-size: 14px;
            font-weight: 400;
            color: #64748b;
            margin-top: 8px;
            margin-bottom: 16px;
        }

        form {
            padding: 0 28px 28px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 14px;
            margin: 0 28px 20px 28px;
            font-size: 14px;
            font-weight: 600;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* Form group */
        .form-group {
            margin-bottom: 16px;
        }

        /* Input fields styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            background: white;
            font-family: 'Inter', inherit;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        textarea:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af;
        }

        /* Password hint */
        .password-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
            margin-left: 4px;
        }

        /* Password feedback styling */
        .password-feedback {
            margin-top: 6px;
        }

        .password-match {
            color: #16a34a;
            font-size: 12px;
            display: none;
            align-items: center;
            gap: 4px;
        }

        .password-match.show {
            display: flex;
        }

        .password-mismatch {
            color: #dc2626;
            font-size: 12px;
            display: none;
            align-items: center;
            gap: 4px;
        }

        .password-mismatch.show {
            display: flex;
        }

        /* Button styling */
        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            margin-top: 24px;
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

        button:hover {
            filter: brightness(0.98);
            transform: translateY(-1px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        /* Footer text */
        .footer-text {
            margin-top: 16px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            padding: 0 28px 28px 28px;
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

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            
            form {
                padding: 0 20px 24px;
            }
            
            .alert {
                margin: 0 20px 16px 20px;
            }
            
            .footer-text {
                padding: 0 20px 24px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
        }
    </style>
    @include('partials.greenpoint-theme')
</head>
<body>
    <div class="register-container">
        <h2>Daftar Nasabah</h2>

        @if ($errors->any() || session('error'))
            @include('partials.toast', [
                'type' => 'danger',
                'messages' => session('error') ? [session('error')] : $errors->all(),
            ])
        @endif

        @if (session('success'))
            @include('partials.toast', ['type' => 'success', 'message' => session('success')])
        @endif

        <form method="POST" action="{{ route('nasabah.store') }}">
            @csrf

            <div class="form-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username" 
                    required
                    value="{{ old('username') }}"
                >
            </div>

            <div class="form-group">
                <input 
                    type="text" 
                    name="nama" 
                    placeholder="Nama Lengkap" 
                    required
                    value="{{ old('nama') }}"
                >
            </div>

            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email" 
                    required
                    value="{{ old('email') }}"
                >
            </div>

            <div class="form-group">
                <input 
                    type="text" 
                    name="no_hp" 
                    placeholder="Nomor HP" 
                    required
                    value="{{ old('no_hp') }}"
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password (max 8 karakter)" 
                    maxlength="8" 
                    required
                    id="password"
                >
                <div class="password-hint">
                    Maksimal 8 karakter
                </div>
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="konfirmasi_password" 
                    placeholder="Konfirmasi Password" 
                    maxlength="8" 
                    required
                    id="konfirmasi_password"
                >
                <div class="password-feedback">
                    <span class="password-match" id="password-match">✓ Password cocok</span>
                    <span class="password-mismatch" id="password-mismatch">✗ Password tidak cocok</span>
                </div>
            </div>

            <div class="form-group">
                <textarea 
                    name="alamat" 
                    placeholder="Alamat"
                ></textarea>
            </div>

            <button type="submit" name="register">Daftar</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const konfirmasi = document.getElementById('konfirmasi_password');
            const match = document.getElementById('password-match');
            const mismatch = document.getElementById('password-mismatch');
            
            function validatePassword() {
                if (password.value === '' || konfirmasi.value === '') {
                    match.classList.remove('show');
                    mismatch.classList.remove('show');
                    return;
                }
                
                if (password.value === konfirmasi.value) {
                    match.classList.add('show');
                    mismatch.classList.remove('show');
                } else {
                    match.classList.remove('show');
                    mismatch.classList.add('show');
                }
            }
            
            password.addEventListener('input', validatePassword);
            konfirmasi.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
