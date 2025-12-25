@extends('frontend.layouts.master')
@section('title','Đặt Hàng Thành Công - PTIT eCommerce')
@section('main-content')

<div class="checkout-success-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="success-content">
                    <div class="success-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h1 class="success-title">Đặt hàng thành công!</h1>
                    <p class="success-message">
                        Cảm ơn bạn đã đặt hàng. Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.
                    </p>

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
                                <span class="info-label">Phương thức thanh toán:</span>
                                <span class="info-value">
                                    @if($order->payment_method == 'cod')
                                        Thanh toán khi nhận hàng (COD)
                                    @else
                                        VNPay
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Trạng thái:</span>
                                <span class="info-value status-{{ $order->status }}">
                                    {{ \App\Helpers\StatusLabel::orderStatus($order->status) }}
                                </span>
                            </div>
                        </div>

                        @if($order->items && count($order->items) > 0)
                        <div class="order-items-box">
                            <h3>Sản phẩm đã đặt</h3>
                            <div class="items-list">
                                @foreach($order->items as $item)
                                <div class="order-item">
                                    <div class="item-image">
                                        @php
                                            $product = $item->product;
                                            $photos = explode(',', (string)($product->image_url ?? ''));
                                            $img = trim($photos[0] ?? '');
                                            $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                                                ? $img 
                                                : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                                        @endphp
                                        <img src="{{ $imgSrc }}" alt="{{ $product->name }}" referrerpolicy="no-referrer">
                                    </div>
                                    <div class="item-details">
                                        <h4>{{ $product->name }}</h4>
                                        @if($item->variant)
                                        <p class="variant-info">
                                            Size: {{ $item->variant->attributes['size'] ?? 'N/A' }}
                                            @if(isset($item->variant->attributes['option']))
                                            | Option: {{ $item->variant->attributes['option'] }}
                                            @endif
                                        </p>
                                        @endif
                                        <p class="item-qty">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="item-price">
                                        {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}₫
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
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
.checkout-success-page {
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

.order-info {
    text-align: left;
    margin-bottom: 30px;
}

.info-box, .order-items-box {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box h3, .order-items-box h3 {
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

.status-pending {
    color: #ffc107;
}

.status-paid {
    color: #28a745;
}

.items-list {
    max-height: 400px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #e0e0e0;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    border-radius: 4px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #1a1a1a;
}

.variant-info, .item-qty {
    font-size: 12px;
    color: #666;
    margin: 0;
}

.item-price {
    font-size: 16px;
    font-weight: 600;
    color: #D4AF37;
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

