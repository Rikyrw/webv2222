<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Reset Password Akun Nasabah</title>
</head>
<body style="margin:0;background:#f4f7f3;color:#173022;font-family:Arial,Helvetica,sans-serif;">
    <div style="margin:0 auto;max-width:560px;padding:36px 20px;">
        <div style="background:#ffffff;border:1px solid #d7e2da;border-radius:8px;padding:32px;">
            <p style="margin:0 0 12px;color:#52705c;font-size:14px;">GreenPoint</p>
            <h1 style="margin:0 0 18px;font-size:24px;line-height:1.3;">Reset password akun nasabah</h1>
            <p style="margin:0 0 14px;font-size:16px;line-height:1.6;">Halo {{ $recipientName }},</p>
            <p style="margin:0 0 20px;font-size:16px;line-height:1.6;">
                Klik tombol berikut untuk membuat password baru. Gunakan link terbaru dari email ini.
            </p>
            <p style="margin:0 0 22px;">
                <a href="{{ $resetUrl }}" style="background:#246c41;border-radius:8px;color:#ffffff;display:inline-block;font-size:15px;font-weight:700;padding:14px 20px;text-decoration:none;">
                    Reset password
                </a>
            </p>
            <p style="margin:0 0 8px;color:#52705c;font-size:14px;line-height:1.6;">
                Jika tombol tidak muncul, buka link reset berikut:
            </p>
            <p style="margin:0 0 18px;font-size:13px;line-height:1.6;word-break:break-all;">
                <a href="{{ $resetUrl }}" style="color:#246c41;">{{ $resetUrl }}</a>
            </p>
            <p style="margin:0 0 8px;color:#52705c;font-size:13px;line-height:1.6;">
                Link terbaru dibuat pada {{ now()->format('d-m-Y H:i:s') }}.
            </p>
            <p style="margin:0;color:#52705c;font-size:14px;line-height:1.6;">
                Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
        </div>
    </div>
</body>
</html>
