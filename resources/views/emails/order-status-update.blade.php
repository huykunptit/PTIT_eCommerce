<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật trạng thái đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-shipped { background-color: #007bff; color: #fff; }
        .status-delivered { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #D4AF37;
            color: #1a1a1a;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cập nhật trạng thái đơn hàng</h1>
        </div>

        <div style="text-align: center;">
            <p>Xin chào {{ $order->shipping_name }},</p>
            <p>Đơn hàng #{{ $order->id }} của bạn đã được cập nhật trạng thái:</p>
            
            <div class="status-badge status-{{ $status }}">
                {{ \App\Helpers\StatusLabel::orderStatus($status) }}
            </div>
        </div>

        <div class="order-info">
            <h2>Thông tin đơn hàng</h2>
            <div class="info-row">
                <span>Mã đơn hàng:</span>
                <strong>#{{ $order->id }}</strong>
            </div>
            <div class="info-row">
                <span>Tổng tiền:</span>
                <strong style="color: #D4AF37;">{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
            </div>
            <div class="info-row">
                <span>Ngày cập nhật:</span>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        @if($message)
        <div class="order-info">
            <h3>Thông báo:</h3>
            <p>{{ $message }}</p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('orders.show', $order->id) }}" class="button">Xem chi tiết đơn hàng</a>
        </div>

        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
            <p>Email: support@ptit-ecommerce.com | Hotline: 0123-456-789</p>
            <p>&copy; {{ date('Y') }} PTIT eCommerce. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

