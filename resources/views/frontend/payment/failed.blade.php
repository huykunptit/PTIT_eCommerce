@extends('frontend.layouts.master')
@section('title','Thanh Toán Thất Bại - PTIT eCommerce')
@section('main-content')

<div class="payment-failed-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="failed-content">
                    <div class="failed-icon">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <h1 class="failed-title">Thanh toán thất bại</h1>
                    <p class="failed-message">
                        @if(isset($result) && isset($result['message']))
                            {{ $result['message'] }}
                        @else
                            Rất tiếc, thanh toán của bạn không thành công. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.
                        @endif
                    </p>

                    @if(isset($result))
                    <div class="payment-info">
                        <div class="info-box">
                            <h3>Thông tin giao dịch</h3>
                            @if(isset($result['order_id']))
                            <div class="info-row">
                                <span class="info-label">Mã đơn hàng:</span>
                                <span class="info-value">#{{ $result['order_id'] }}</span>
                            </div>
                            @endif
                            @if(isset($result['transaction_no']))
                            <div class="info-row">
                                <span class="info-label">Mã giao dịch:</span>
                                <span class="info-value">{{ $result['transaction_no'] }}</span>
                            </div>
                            @endif
                            @if(isset($result['amount']))
                            <div class="info-row">
                                <span class="info-label">Số tiền:</span>
                                <span class="info-value">{{ number_format($result['amount'], 0, ',', '.') }}₫</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($order)
                    <div class="order-info">
                        <div class="info-box">
                            <h3>Thông tin đơn hàng</h3>
                            <div class="info-row">
                                <span class="info-label">Mã đơn hàng:</span>
                                <span class="info-value">#{{ $order->id }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tổng tiền:</span>
                                <span class="info-value">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Trạng thái:</span>
                                <span class="info-value status-failed">Chưa thanh toán</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="failed-actions">
                        @if($order)
                        <a href="{{ route('payment.vnpay.create', ['order_id' => $order->id, 'amount' => $order->total_amount]) }}" class="btn-retry-payment">
                            <i class="fa fa-redo mr-2"></i>Thử thanh toán lại
                        </a>
                        @endif
                        <a href="{{ route('user.orders') }}" class="btn-view-orders">
                            <i class="fa fa-list mr-2"></i>Xem đơn hàng của tôi
                        </a>
                        <a href="{{ route('home') }}" class="btn-continue-shopping">
                            <i class="fa fa-shopping-bag mr-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.payment-failed-page {
    padding: 60px 0 80px;
    background: #f8f9fa;
    min-height: 70vh;
}

.failed-content {
    background: #fff;
    padding: 50px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.failed-icon {
    margin-bottom: 30px;
}

.failed-icon i {
    font-size: 80px;
    color: #dc3545;
}

.failed-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.failed-message {
    font-size: 16px;
    color: #666;
    margin-bottom: 40px;
    line-height: 1.6;
}

.payment-info, .order-info {
    text-align: left;
    margin-bottom: 30px;
}

.info-box {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1a1a1a;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e0e0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.info-value {
    color: #1a1a1a;
    font-weight: 500;
}

.status-failed {
    color: #dc3545;
}

.failed-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-retry-payment, .btn-view-orders, .btn-continue-shopping {
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}

.btn-retry-payment {
    background: #D4AF37;
    color: #1a1a1a;
}

.btn-retry-payment:hover {
    background: #c9a030;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    color: #1a1a1a;
    text-decoration: none;
}

.btn-view-orders {
    background: #1a1a1a;
    color: #fff;
}

.btn-view-orders:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    color: #fff;
    text-decoration: none;
}

.btn-continue-shopping {
    background: #6c757d;
    color: #fff;
}

.btn-continue-shopping:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    color: #fff;
    text-decoration: none;
}

@media (max-width: 768px) {
    .failed-content {
        padding: 30px 20px;
    }
    
    .failed-actions {
        flex-direction: column;
    }
    
    .btn-retry-payment, .btn-view-orders, .btn-continue-shopping {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@endsection

