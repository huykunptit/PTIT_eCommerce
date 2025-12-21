@extends('frontend.layouts.master')
@section('title','Thanh Toán Thành Công - PTIT eCommerce')
@section('main-content')

<div class="payment-success-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-content">
                    <div class="success-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h1 class="success-title">Thanh toán thành công!</h1>
                    <p class="success-message">
                        Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đã được xác nhận và sẽ được xử lý trong thời gian sớm nhất.
                    </p>

                    @if(isset($result))
                    <div class="payment-info">
                        <div class="info-box">
                            <h3>Thông tin thanh toán</h3>
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
                            @if(isset($result['bank_code']))
                            <div class="info-row">
                                <span class="info-label">Ngân hàng:</span>
                                <span class="info-value">{{ $result['bank_code'] }}</span>
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
                                <span class="info-value status-paid">Đã thanh toán</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="success-actions">
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
.payment-success-page {
    padding: 60px 0 80px;
    background: #f8f9fa;
    min-height: 70vh;
}

.success-content {
    background: #fff;
    padding: 50px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.success-icon {
    margin-bottom: 30px;
}

.success-icon i {
    font-size: 80px;
    color: #28a745;
}

.success-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.success-message {
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

.status-paid {
    color: #28a745;
}

.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-view-orders, .btn-continue-shopping {
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}

.btn-view-orders {
    background: #D4AF37;
    color: #1a1a1a;
}

.btn-view-orders:hover {
    background: #c9a030;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    color: #1a1a1a;
    text-decoration: none;
}

.btn-continue-shopping {
    background: #1a1a1a;
    color: #fff;
}

.btn-continue-shopping:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    color: #fff;
    text-decoration: none;
}

@media (max-width: 768px) {
    .success-content {
        padding: 30px 20px;
    }
    
    .success-actions {
        flex-direction: column;
    }
    
    .btn-view-orders, .btn-continue-shopping {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@endsection

