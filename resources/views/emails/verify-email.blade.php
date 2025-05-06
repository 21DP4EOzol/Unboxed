<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
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
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #964B00, #7D3C00);
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
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
            <div>Verify Your Email Address</div>
        </div>
        
        <p>Hello {{ $user->name }},</p>
        
        <p>Thanks for signing up with Unboxed! Before you can start discovering and swiping on products, please verify your email address by clicking the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $url }}" class="button">Verify Email Address</a>
        </div>
        
        <p>This verification link will expire in 60 minutes.</p>
        
        <p>If you did not create an account, no further action is required.</p>
        
        <div class="footer">
            <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
            <p style="word-break: break-all;">{{ $url }}</p>
            <p>© {{ date('Y') }} Unboxed. All rights reserved.</p>
        </div>
    </div>
</body>
</html>