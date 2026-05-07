<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Nasabah • GreenPoint</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #000000, #1f2937);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 16px;
        }

        .register-container {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 448px;
            animation: fadeUp 0.3s ease-out;
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

        h2 {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #10b981;
            margin-bottom: 24px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            border: 1px solid;
        }

        .alert-error {
            background-color: #fee2e2;
            border-color: #f87171;
            color: #991b1b;
        }

        .alert-success {
            background-color: #dcfce7;
            border-color: #86efac;
            color: #15803d;
        }

        .form-group {
            margin-bottom: 16px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        textarea:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .password-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .password-feedback {
            font-size: 12px;
            margin-top: 4px;
        }

        .password-match {
            color: #16a34a;
            display: none;
        }

        .password-match.show {
            display: block;
        }

        .password-mismatch {
            color: #dc2626;
            display: none;
        }

        .password-mismatch.show {
            display: block;
        }

        button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 12px 24px;
            transform: translate(0, -50%);
            background: transparent;
            color: #059669;
            border: 1px solid #059669;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 28px;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        button:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-color: #059669;
            transform: translate(0, -50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        button:active {
            transform: translate(0, -50%) scale(0.98);
            transition: all 0.1s ease;
        }

        .footer-text {
            margin-top: 8px;
            text-align: center;
            color: #4b5563;
            font-size: 14px;
        }

        .footer-text a {
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 24px;
            }

            h2 {
                font-size: 20px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Daftar Nasabah</h2>

        @if ($errors->any() || session('error'))
            <div class="alert alert-error">
                @if (session('error'))
                    {{ session('error') }}
                @elseif ($errors->any())
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                @endif
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
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
