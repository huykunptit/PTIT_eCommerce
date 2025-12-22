@extends('frontend.layouts.master')
@section('title','Thanh Toán - PTIT eCommerce')
@section('main-content')

<div class="checkout-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">Thanh Toán</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-form-wrapper">
                        <h2 class="form-title">Thông tin giao hàng</h2>
                        
                        <div class="form-group">
                            <label for="shipping_name">Họ và tên <span class="required">*</span></label>
                            <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                   value="{{ old('shipping_name', $user->name ?? '') }}" required>
                            @error('shipping_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_phone">Số điện thoại <span class="required">*</span></label>
                            <input type="text" class="form-control" id="shipping_phone" name="shipping_phone" 
                                   value="{{ old('shipping_phone', $user->phone ?? '') }}" required>
                            @error('shipping_phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_email">Email</label>
                            <input type="email" class="form-control" id="shipping_email" name="shipping_email" 
                                   value="{{ old('shipping_email', $user->email ?? '') }}">
                            @error('shipping_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Địa chỉ giao hàng <span class="required">*</span></label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', $user->address ?? '') }}</textarea>
                            @error('shipping_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú về đơn hàng của bạn...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="summary-title">Đơn hàng của bạn</h3>
                        <div class="order-items">
                            @foreach($cartItems as $item)
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['product']->name }}" referrerpolicy="no-referrer">
                                </div>
                                <div class="item-details">
                                    <h4 class="item-name">{{ $item['product']->name }}</h4>
                                    @if($item['variant'])
                                    <p class="item-variant">
                                        Size: {{ $item['variant']->attributes['size'] ?? 'N/A' }}
                                        @if(isset($item['variant']->attributes['option']))
                                        | Option: {{ $item['variant']->attributes['option'] }}
                                        @endif
                                    </p>
                                    @endif
                                    <p class="item-quantity">Số lượng: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="item-price">
                                    {{ number_format($item['subtotal'], 0, ',', '.') }}₫
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="summary-totals">
                            <div class="total-row">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="total-row">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <div class="total-row final-total">
                                <span>Tổng cộng:</span>
                                <span class="total-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        <div class="payment-methods">
                            <h4 class="payment-title">Phương thức thanh toán</h4>

                            <div class="payment-option">
                                <input type="radio" id="payment_vnpay" name="payment_method" value="vnpay" 
                                       {{ old('payment_method', 'vnpay') == 'vnpay' ? 'checked' : '' }} required>
                                <label for="payment_vnpay">
                                    <i class="fa fa-credit-card"></i>
                                    <span>Thanh toán qua VNPay</span>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="payment_sepay" name="payment_method" value="sepay"
                                       {{ old('payment_method') == 'sepay' ? 'checked' : '' }}>
                                <label for="payment_sepay">
                                    <i class="fa fa-qrcode"></i>
                                    <span>Chuyển khoản QR Sepay (Vietcombank)</span>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="payment_cod" name="payment_method" value="cod" 
                                       {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                                <label for="payment_cod">
                                    <i class="fa fa-money-bill-wave"></i>
                                    <span>Thanh toán khi nhận hàng (COD)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-checkout-submit">
                            <i class="fa fa-lock mr-2"></i>Đặt hàng
                        </button>

                        <p class="secure-notice">
                            <i class="fa fa-shield-alt mr-2"></i>
                            Thông tin của bạn được bảo mật
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.checkout-page {
    padding: 40px 0 80px;
    background: #f8f9fa;
    min-height: 60vh;
}

.page-header {
    margin-bottom: 40px;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item a {
    color: #666;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #D4AF37;
}

.checkout-form-wrapper {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.form-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #1a1a1a;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #D4AF37;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.order-summary {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 20px;
}

.summary-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1a1a1a;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.order-items {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
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

.item-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #1a1a1a;
}

.item-variant {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.item-quantity {
    font-size: 12px;
    color: #666;
    margin: 0;
}

.item-price {
    font-size: 16px;
    font-weight: 600;
    color: #D4AF37;
}

.summary-totals {
    padding: 20px 0;
    border-top: 2px solid #f0f0f0;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
}

.total-row:last-child {
    margin-bottom: 0;
}

.final-total {
    font-size: 18px;
    font-weight: 700;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.total-amount {
    color: #D4AF37;
    font-size: 20px;
}

.payment-methods {
    margin-bottom: 25px;
}

.payment-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #1a1a1a;
}

.payment-option {
    margin-bottom: 12px;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option label {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}

.payment-option label i {
    margin-right: 10px;
    font-size: 18px;
    color: #D4AF37;
}

.payment-option input[type="radio"]:checked + label {
    border-color: #D4AF37;
    background: rgba(212, 175, 55, 0.05);
}

.btn-checkout-submit {
    width: 100%;
    padding: 15px;
    background: #D4AF37;
    color: #1a1a1a;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 15px;
}

.btn-checkout-submit:hover {
    background: #c9a030;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

.secure-notice {
    text-align: center;
    font-size: 12px;
    color: #666;
    margin: 0;
}

.secure-notice i {
    color: #28a745;
}

@media (max-width: 991px) {
    .order-summary {
        position: relative;
        top: 0;
        margin-top: 30px;
    }
}
</style>
@endpush

@endsection

