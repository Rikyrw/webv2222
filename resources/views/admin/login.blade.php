<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin • GreenPoint</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@0.469.0/font/lucide.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            background:
                radial-gradient(circle at top, rgba(16, 185, 129, 0.16), transparent 28%),
                linear-gradient(180deg, #f8fdf9 0%, #eef7f1 100%);
        }

        .shell {
            width: 100%;
            max-width: 520px;
        }

        .brand-mark {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.28);
        }

        .card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 24px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .card-head {
            padding: 28px 28px 22px;
            text-align: center;
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.12) 0%, rgba(255,255,255,0) 100%);
        }

        .card-head h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }

        .card-head p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .card-body {
            padding: 0 28px 28px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 14px;
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

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .input, .select {
            width: 100%;
            min-height: 46px;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            background: white;
            font: inherit;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input:focus, .select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .password-group { position: relative; }

        .toggle-pass {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #f8fafc;
            color: #334155;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            min-height: 48px;
            margin-top: 6px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.24);
        }

        .btn-login:hover { filter: brightness(0.98); }

        .helper {
            margin-top: 16px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .select-wrapper { position: relative; }
        .select-wrapper::after {
            content: '⌄';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="brand-mark"><i class="lucide-shield"></i></div>
        <div class="card">
            <div class="card-head">
                <h1>Admin Login</h1>
                <p>Masuk untuk mengelola sistem bank sampah GreenPoint.</p>
            </div>

            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email"><i class="lucide-mail"></i> Email</label>
                        <input class="input" type="email" id="email" name="email" required value="{{ old('email') }}" autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="lucide-key"></i> Password</label>
                        <div class="password-group">
                            <input class="input" type="password" id="password" name="password" required>
                            <button type="button" id="togglePassword" class="toggle-pass">👁️</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role"><i class="lucide-shield"></i> Login Sebagai</label>
                        <div class="select-wrapper">
                            <select class="select" id="role" name="role" required>
                                <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin Sistem</option>
                                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="lucide-log-in"></i> Masuk ke Dashboard
                    </button>
                </form>

                <div class="helper">Gunakan akun admin yang sudah terdaftar di sistem.</div>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');

        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });

        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.toLowerCase();
            const roleSelect = document.getElementById('role');
            if (email.includes('superadmin')) roleSelect.value = 'superadmin';
            else if (email.includes('admin')) roleSelect.value = 'admin';
        });
    </script>
</body>
</html>
