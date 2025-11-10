<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
        }
        .email-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .email-header h1 {
            color: #667eea;
            margin: 0;
        }
        .email-body {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-reset {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .email-footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>PTIT eCommerce</h1>
        </div>
        
        <div class="email-body">
            <h2>Yêu cầu đặt lại mật khẩu</h2>
            <p>Xin chào,</p>
            <p>Bạn nhận được email này vì chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            <p>Nhấn vào nút bên dưới để đặt lại mật khẩu:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset">Đặt lại mật khẩu</a>
            </div>
            
            <p>Hoặc copy và paste link sau vào trình duyệt:</p>
            <p style="word-break: break-all; color: #667eea;">{{ $resetUrl }}</p>
            
            <p><strong>Lưu ý:</strong> Link này sẽ hết hạn sau 24 giờ.</p>
            
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        
        <div class="email-footer">
            <p>Trân trọng,<br>PTIT eCommerce Team</p>
        </div>
    </div>
</body>
</html>

