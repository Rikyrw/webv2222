<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Belum Diverifikasi</title>
    @include('partials.favicon')
    <style>
        * {
            box-sizing: border-box;
            letter-spacing: 0;
        }

        body {
            align-items: center;
            background: #f3f7f1;
            color: #173022;
            display: flex;
            font-family: Arial, Helvetica, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
        }

        .notice-panel {
            background: #fff;
            border: 1px solid #d4e1d7;
            border-radius: 8px;
            box-shadow: 0 18px 52px rgba(16, 46, 29, .12);
            max-width: 480px;
            padding: 34px;
            width: 100%;
        }

        .eyebrow {
            color: #446955;
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 10px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 28px;
            line-height: 1.25;
            margin: 0 0 12px;
        }

        p {
            color: #496556;
            line-height: 1.55;
            margin: 0 0 18px;
        }

        .email {
            color: #173022;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .message {
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 16px;
            padding: 12px 14px;
        }

        .success {
            background: #e8f5eb;
            border: 1px solid #b8d9c1;
            color: #19512e;
        }

        .error {
            background: #fff0ef;
            border: 1px solid #efc0bb;
            color: #8a241b;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        form {
            margin: 0;
        }

        button,
        a {
            align-items: center;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            font-size: 15px;
            font-weight: 700;
            justify-content: center;
            min-height: 46px;
            padding: 0 16px;
            text-decoration: none;
        }

        button {
            background: #246c41;
            border: 1px solid #246c41;
            color: #fff;
        }

        button:hover {
            background: #1d5935;
        }

        a {
            border: 1px solid #b7cbbb;
            color: #246c41;
        }

        @media (max-width: 520px) {
            .notice-panel {
                padding: 26px 20px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <main class="notice-panel">
        <p class="eyebrow">Nasabah</p>
        <h1>Email belum diverifikasi</h1>
        @if ($email)
            <p>
                Kami menunggu verifikasi untuk <span class="email">{{ $email }}</span>.
                Klik link pada email terbaru sebelum masuk dashboard.
            </p>
        @else
            <p>Login dengan akun manual Anda agar link verifikasi dapat dikirim ulang.</p>
        @endif

        @if (session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="message error">{{ $errors->first() }}</div>
        @endif

        <div class="actions">
            @if ($email)
                <form method="POST" action="{{ route('nasabah.verification.resend') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit">Kirim ulang email verifikasi</button>
                </form>
            @endif
            <a href="{{ route('nasabah.login') }}">Kembali ke login</a>
        </div>
    </main>
</body>
</html>
