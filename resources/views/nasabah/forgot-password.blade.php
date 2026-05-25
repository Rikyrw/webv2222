<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - GreenPoint</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #0f172a;
            background: radial-gradient(circle at top, rgba(16, 185, 129, 0.16), transparent 28%),
                        linear-gradient(180deg, #f8fdf9 0%, #eef7f1 100%);
        }
        .card {
            width: 100%;
            max-width: 448px;
            padding: 28px;
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 22px;
        }
        .alert {
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 600;
        }
        .alert-success {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .alert-error {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            border: 1px solid #dbe3ea;
            border-radius: 14px;
            font: inherit;
        }
        input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }
        button {
            width: 100%;
            min-height: 48px;
            margin-top: 16px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }
        .footer {
            margin-top: 18px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }
        a {
            color: #10b981;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
    @include('partials.greenpoint-theme')
</head>
<body>
    <main class="card">
        <h1>Lupa Password</h1>
        <p class="subtitle">
            Masukkan email akun manual Anda. Link reset hanya dikirim ke inbox email akun tersebut.
        </p>

        @if (session('success'))
            @include('partials.toast', ['type' => 'success', 'message' => session('success')])
        @endif

        @if ($errors->any())
            @include('partials.toast', ['type' => 'danger', 'messages' => $errors->all()])
        @endif

        <form method="POST" action="{{ route('nasabah.password.email') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email" autocapitalize="none" spellcheck="false" required>
            <button type="submit">Kirim Link Reset</button>
        </form>

        <p class="footer">
            Ingat password? <a href="{{ route('nasabah.login') }}">Kembali ke login</a>
        </p>
    </main>
</body>
</html>
