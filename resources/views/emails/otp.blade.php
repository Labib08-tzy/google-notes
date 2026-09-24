<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode OTP Verifikasi Login</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #fffbf5; margin: 0; padding: 30px; color: #333333;">
    <div style="max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 30px; border: 1px solid #fde68a; box-shadow: 0 4px 12px rgba(245,158,11,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px;">
                🔐
            </div>
            <h2 style="color: #92400e; margin: 12px 0 4px 0; font-size: 20px;">Verifikasi Kode OTP</h2>
            <p style="color: #78350f; font-size: 13px; margin: 0;">Google Notes Security Verification</p>
        </div>

        <!-- Body -->
        <p style="font-size: 14px; line-height: 1.5; color: #4b5563;">
            Halo <strong>{{ $userName }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.5; color: #4b5563;">
            Gunakan kode OTP di bawah ini untuk menyelesaikan proses login Anda:
        </p>

        <!-- OTP Box -->
        <div style="text-align: center; margin: 24px 0; padding: 20px; background: #fffbf0; border-radius: 12px; border: 2px dashed #f59e0b;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #b45309; font-family: monospace;">
                {{ $otpCode }}
            </span>
            <p style="font-size: 11px; color: #92400e; margin-top: 8px; margin-bottom: 0;">
                Kode ini berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.
            </p>
        </div>

        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin-top: 24px;">
            Jika Anda tidak merasa melakukan login ini, silakan abaikan email ini.
        </p>

        <hr style="border: none; border-top: 1px solid #fef3c7; margin: 20px 0;">

        <div style="text-align: center; font-size: 11px; color: #b45309;">
            &copy; {{ date('Y') }} Google Notes. All rights reserved.
        </div>
    </div>
</body>
</html>
