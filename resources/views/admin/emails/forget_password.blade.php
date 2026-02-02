<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef1f5;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
        }

        .wrapper {
            max-width: 620px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, #1d3557, #457b9d);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .content {
            padding: 35px 30px;
            font-size: 16px;
            color: #333333;
            line-height: 1.7;
        }

        .button {
            display: block;
            width: fit-content;
            padding: 14px 32px;
            margin: 30px auto;
            background: #1d3557;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
        }

        .note {
            margin-top: 18px;
            padding: 12px 16px;
            background: #fff1f2;
            border-left: 4px solid #e63946;
            font-size: 14px;
            color: #d62828;
            border-radius: 6px;
        }

        .footer {
            text-align: center;
            padding: 25px 20px;
            font-size: 13px;
            color: #8a8a8a;
            background: #f9fafc;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <h1>Password Reset</h1>
        </div>

        <!-- Content -->
        <div class="content">
            Hello {{ $user->name ?? 'User' }},
            <br><br>
            We received a request to reset your password for your account at
            <strong>{{ config('app.name') }}</strong>.
            <br><br>
            To proceed, please click the button below:
        </div>

        <!-- Button -->
        <a href="{{ $resetUrl }}" class="button">
            Reset Password
        </a>

        <div class="content">
            If you did not request this password reset, please ignore this email. Your account remains secure.
            <div class="note">
                ⏳ This reset link is valid for <strong>10 minutes</strong>.
                After that, you will need to request a new one.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>

    </div>

</body>

</html>
