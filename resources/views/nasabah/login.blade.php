<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Nasabah • GreenPoint</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #000000, #002d6d);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 16px;
        }

        .login-container {
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

        .error-message {
            color: #ef4444;
            text-align: center;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #9ca3af;
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
            .login-container {
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
    <div class="login-container">
        <h2>Login Nasabah</h2>

        @if (session('error'))
            <p class="error-message">{{ session('error') }}</p>
        @endif

        @if ($errors->any())
            <p class="error-message">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </p>
        @endif

        <form method="POST" action="{{ route('nasabah.authenticate') }}">
            @csrf

            <div class="form-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username atau Email" 
                    required
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

            <button type="submit" name="login">Login ke Dashboard</button>
        </form>

        <p class="footer-text">
            Belum punya akun? <a href="{{ route('nasabah.register') }}">Daftar di sini</a>
        </p>
    </div>
</body>
</html>
