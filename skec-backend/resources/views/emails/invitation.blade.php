<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited – {{ $appName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f4f6f9; color: #333; }
        .wrapper { max-width: 640px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1A3C6E 0%, #2E86C1 100%); padding: 40px 48px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; margin-top: 6px; }
        .body { padding: 48px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1A3C6E; margin-bottom: 16px; }
        .message { font-size: 15px; line-height: 1.7; color: #555; margin-bottom: 32px; }
        .cta-wrap { text-align: center; margin: 32px 0; }
        .cta-btn { display: inline-block; background: #1A3C6E; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 16px; font-weight: 600; letter-spacing: 0.3px; }
        .cta-btn:hover { background: #2E86C1; }
        .expiry { background: #FFF8E1; border-left: 4px solid #F39C12; padding: 14px 18px; border-radius: 4px; margin: 24px 0; font-size: 14px; color: #7D6608; }
        .link-fallback { font-size: 13px; color: #888; word-break: break-all; margin: 16px 0; }
        .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 24px 48px; text-align: center; }
        .footer p { font-size: 12px; color: #aaa; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>{{ $appName }}</h1>
        <p>Your Digital Learning Hub</p>
    </div>
    <div class="body">
        <p class="greeting">You've been invited! 🎉</p>
        <p class="message">
            You have been invited to join the <strong>{{ $appName }}</strong> learning platform.
            Click the button below to complete your registration and access all available study materials.
        </p>

        <div class="cta-wrap">
            <a href="{{ $registrationLink }}" class="cta-btn">Accept Invitation &amp; Register</a>
        </div>

        <div class="expiry">
            ⏱ This invitation expires on
            <strong>{{ $invitation->expires_at->format('D, d M Y \a\t h:i A') }}</strong>.
            Please register before it expires.
        </div>

        <p class="link-fallback">
            If the button doesn't work, copy and paste this URL into your browser:<br>
            {{ $registrationLink }}
        </p>
    </div>
    <div class="footer">
        <p>
            If you did not expect this invitation, you can safely ignore this email.<br>
            © {{ date('Y') }} {{ $appName }}. All rights reserved.
        </p>
    </div>
</div>
</body>
</html>
