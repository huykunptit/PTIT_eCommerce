<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ từ website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        .header {
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 120px;
        }
        .message-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #D4AF37;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #D4AF37; margin: 0;">Liên hệ mới từ website</h1>
        </div>

        <div class="info-row">
            <span class="info-label">Họ tên:</span>
            <span>{{ $name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span>{{ $email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Số điện thoại:</span>
            <span>{{ $phone }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Chủ đề:</span>
            <span>{{ $subject }}</span>
        </div>

        <div class="message-box">
            <h3 style="margin-top: 0; color: #1a1a1a;">Nội dung tin nhắn:</h3>
            <p style="white-space: pre-wrap; margin: 0;">{{ $message }}</p>
        </div>

        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Thời gian: {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
</body>
</html>

