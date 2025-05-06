<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two Factor Authentication Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f3e8;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #964B00, #7D3C00);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px;
        }
        .logo {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            text-align: center;
            margin: 30px 0;
            padding: 10px;
            background-color: #f9f3e8;
            border-radius: 4px;
            border: 1px dashed #964B00;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Unboxed</div>
            <div>Two Factor Authentication</div>
        </div>
        
        <p>Hello {{ $user->name }},</p>
        
        <p>You recently requested to enable two-factor authentication for your Unboxed account. To complete this setup, please use the following verification code:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>This code will expire in 10 minutes. If you did not request this code, please ignore this message and consider reviewing your account security.</p>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
        </div>
    </div>
</body>
</html>