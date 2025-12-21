<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
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
        .header h1 {
            color: #D4AF37;
            margin: 0;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .order-info h2 {
            color: #1a1a1a;
            margin-top: 0;
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
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #1a1a1a;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-name {
            flex: 1;
            font-weight: bold;
        }
        .item-quantity {
            margin: 0 20px;
        }
        .item-price {
            color: #D4AF37;
            font-weight: bold;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #D4AF37;
        }
        .total-amount {
            font-size: 24px;
            color: #D4AF37;
            font-weight: bold;
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
            <h1>🎉 Đặt hàng thành công!</h1>
            <p>Cảm ơn bạn đã mua sắm tại PTIT eCommerce</p>
        </div>

        <div class="order-info">
            <h2>Thông tin đơn hàng</h2>
            <div class="info-row">
                <span class="info-label">Mã đơn hàng:</span>
                <span class="info-value">#{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value">
                    @if($order->status == 'pending')
                        Chờ xử lý
                    @elseif($order->status == 'processing')
                        Đang xử lý
                    @elseif($order->status == 'shipped')
                        Đã gửi hàng
                    @elseif($order->status == 'delivered')
                        Đã giao hàng
                    @else
                        {{ ucfirst($order->status) }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Phương thức thanh toán:</span>
                <span class="info-value">
                    @if($order->payment_method == 'vnpay')
                        VNPay
                    @elseif($order->payment_method == 'cod')
                        Thanh toán khi nhận hàng (COD)
                    @else
                        {{ ucfirst($order->payment_method) }}
                    @endif
                </span>
            </div>
        </div>

        <div class="order-info">
            <h2>Thông tin giao hàng</h2>
            <div class="info-row">
                <span class="info-label">Người nhận:</span>
                <span class="info-value">{{ $order->shipping_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Số điện thoại:</span>
                <span class="info-value">{{ $order->shipping_phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Địa chỉ:</span>
                <span class="info-value">{{ $order->shipping_address }}</span>
            </div>
            @if($order->shipping_email)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->shipping_email }}</span>
            </div>
            @endif
        </div>

        <div class="order-items">
            <h2>Sản phẩm đã đặt</h2>
            @foreach($order->items as $item)
            <div class="order-item">
                <div class="item-name">{{ $item->product->name ?? 'N/A' }}</div>
                <div class="item-quantity">x{{ $item->quantity }}</div>
                <div class="item-price">{{ number_format($item->subtotal, 0, ',', '.') }}₫</div>
            </div>
            @endforeach
        </div>

        <div class="total">
            <div class="info-row">
                <span class="info-label">Tổng cộng:</span>
                <span class="total-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
            </div>
        </div>

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

