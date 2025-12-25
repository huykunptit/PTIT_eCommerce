@extends('frontend.layouts.master')
@section('title','Giỏ Hàng - PTIT eCommerce')
@section('main-content')

<div class="cart-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">Giỏ Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if(count($cartItems) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-table-wrapper">
                    <div class="table-responsive">
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr data-key="{{ $item['key'] }}">
                                    <td>
                                        <div class="product-info">
                                            <div class="product-image">
                                                <img src="{{ $item['image'] }}" referrerpolicy="no-referrer" alt="{{ $item['product']->name }}">
                                            </div>
                                            <div class="product-details">
                                                <h4 class="product-name">
                                                    <a href="{{ route('product.show', $item['product']->id) }}">{{ $item['product']->name }}</a>
                                                </h4>
                                                @if($item['variant'])
                                                <div class="product-variant">
                                                    <span class="variant-label">Size:</span> {{ $item['variant']->attributes['size'] ?? 'N/A' }}
                                                    @if(isset($item['variant']->attributes['option']))
                                                    <span class="variant-separator">|</span>
                                                    <span class="variant-label">Option:</span> {{ $item['variant']->attributes['option'] }}
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-price">
                                            <span class="price">{{ number_format($item['price'], 0, ',', '.') }}₫</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="quantity-control">
                                            <button type="button" class="qty-btn qty-minus" data-key="{{ $item['key'] }}">-</button>
                                            <input type="number" class="qty-input" value="{{ $item['quantity'] }}" min="1" data-key="{{ $item['key'] }}">
                                            <button type="button" class="qty-btn qty-plus" data-key="{{ $item['key'] }}">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-subtotal">
                                            <span class="subtotal">{{ number_format($item['subtotal'], 0, ',', '.') }}₫</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-remove" data-key="{{ $item['key'] }}" title="Xóa">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="cart-actions">
                        <a href="{{ route('home') }}#products" class="btn-continue">
                            <i class="fa fa-arrow-left mr-2"></i>Tiếp tục mua sắm
                        </a>
                        <button type="button" class="btn-clear-cart">
                            <i class="fa fa-trash mr-2"></i>Xóa toàn bộ
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h3 class="summary-title">Tóm tắt đơn hàng</h3>
                    <div class="summary-content">
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span class="subtotal-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span class="shipping-fee">Miễn phí</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row total-row">
                            <span>Tổng cộng:</span>
                            <span class="total-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                    <div class="summary-actions">
                        <a href="{{ route('checkout.index') }}" class="btn-checkout">
                            <i class="fa fa-shopping-bag mr-2"></i>Thanh toán
                        </a>
                        <p class="secure-text">
                            <i class="fa fa-lock mr-2"></i>Thanh toán an toàn và bảo mật
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="empty-cart">
            <div class="empty-icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <h3>Giỏ hàng của bạn đang trống</h3>
            <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
            <a href="{{ route('home') }}#products" class="btn-shop-now">
                <i class="fa fa-shopping-bag mr-2"></i>Mua sắm ngay
            </a>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.cart-page {
    padding: 40px 0 80px;
    background: #f8f9fa;
    min-height: 60vh;
}

.page-header {
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #eee;
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

.cart-table-wrapper {
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.cart-table {
    margin: 0;
}

.cart-table thead th {
    border: none;
    padding: 15px;
    font-weight: 600;
    color: #1a1a1a;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #eee;
}

.cart-table tbody td {
    padding: 25px 15px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    flex: 1;
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.product-name a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.3s;
}

.product-name a:hover {
    color: #D4AF37;
}

.product-variant {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
}

.variant-label {
    font-weight: 600;
}

.variant-separator {
    margin: 0 8px;
}

.product-price .price {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0;
}

.qty-btn {
    width: 35px;
    height: 35px;
    border: 1px solid #ddd;
    background: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    font-size: 16px;
    color: #666;
}

.qty-btn:hover {
    background: #D4AF37;
    color: #fff;
    border-color: #D4AF37;
}

.qty-input {
    width: 60px;
    height: 35px;
    border: 1px solid #ddd;
    border-left: none;
    border-right: none;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
}

.product-subtotal .subtotal {
    font-size: 18px;
    font-weight: 700;
    color: #D4AF37;
}

.btn-remove {
    background: transparent;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 8px;
    transition: all 0.3s;
    font-size: 18px;
}

.btn-remove:hover {
    color: #c82333;
    transform: scale(1.1);
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #eee;
}

.btn-continue, .btn-clear-cart {
    padding: 12px 30px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-continue {
    background: #f8f9fa;
    color: #1a1a1a;
}

.btn-continue:hover {
    background: #e9ecef;
    color: #1a1a1a;
    text-decoration: none;
}

.btn-clear-cart {
    background: #dc3545;
    color: #fff;
}

.btn-clear-cart:hover {
    background: #c82333;
}

.cart-summary {
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px;
}

.summary-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 15px;
}

.summary-row span:first-child {
    color: #666;
}

.summary-row span:last-child {
    font-weight: 600;
    color: #1a1a1a;
}

.summary-divider {
    height: 1px;
    background: #eee;
    margin: 20px 0;
}

.total-row {
    font-size: 18px;
    margin-top: 10px;
}

.total-row span:first-child {
    font-weight: 700;
    color: #1a1a1a;
}

.total-amount {
    font-size: 24px;
    font-weight: 700;
    color: #D4AF37;
}

.shipping-fee {
    color: #28a745;
}

.summary-actions {
    margin-top: 30px;
}

.btn-checkout {
    display: block;
    width: 100%;
    padding: 15px;
    background: #D4AF37;
    color: #1a1a1a;
    text-align: center;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    margin-bottom: 15px;
}

.btn-checkout:hover {
    background: #C4A037;
    color: #1a1a1a;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212,175,55,0.3);
}

.secure-text {
    text-align: center;
    font-size: 13px;
    color: #666;
    margin: 0;
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.empty-icon {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 30px;
}

.empty-cart h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.empty-cart p {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
}

.btn-shop-now {
    display: inline-block;
    padding: 15px 40px;
    background: #D4AF37;
    color: #1a1a1a;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-shop-now:hover {
    background: #C4A037;
    color: #1a1a1a;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212,175,55,0.3);
}

@media (max-width: 991px) {
    .cart-summary {
        position: static;
        margin-top: 30px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Update quantity
    $('.qty-btn').on('click', function() {
        const key = $(this).data('key');
        const input = $(`.qty-input[data-key="${key}"]`);
        let qty = parseInt(input.val());
        
        if ($(this).hasClass('qty-plus')) {
            qty++;
        } else if ($(this).hasClass('qty-minus') && qty > 1) {
            qty--;
        }
        
        input.val(qty);
        updateCartItem(key, qty);
    });
    
    $('.qty-input').on('change', function() {
        const key = $(this).data('key');
        const qty = parseInt($(this).val()) || 1;
        updateCartItem(key, qty);
    });
    
    // Remove item
    $('.btn-remove').on('click', function() {
        const key = $(this).data('key');
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            removeCartItem(key);
        }
    });
    
    // Clear cart
    $('.btn-clear-cart').on('click', function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
            $.ajax({
                url: '{{ route("cart.clear") }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                }
            });
        }
    });
    
    function updateCartItem(key, quantity) {
        $.ajax({
            url: `{{ url('cart/update') }}/${key}`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { quantity: quantity },
            success: function(response) {
                if (response.success) {
                    const row = $(`tr[data-key="${key}"]`);
                    row.find('.subtotal').text(response.subtotal);
                    $('.total-amount').text(response.total);
                    $('.subtotal-amount').text(response.total);
                    updateCartHeader();
                }
            }
        });
    }
    
    function removeCartItem(key) {
        $.ajax({
            url: `{{ url('cart/remove') }}/${key}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $(`tr[data-key="${key}"]`).fadeOut(300, function() {
                        $(this).remove();
                        if ($('.cart-table tbody tr').length === 0) {
                            location.reload();
                        } else {
                            $('.total-amount').text(response.total);
                            $('.subtotal-amount').text(response.total);
                            updateCartHeader();
                        }
                    });
                }
            }
        });
    }
    
    function updateCartHeader() {
        $.get('{{ route("cart.data") }}', function(data) {
            $('.shopping .total-count').text(data.count);
        });
    }
});
</script>
@endpush

@endsection

