@extends('frontend.layouts.master')
@section('title','Yêu Thích - PTIT eCommerce')
@section('main-content')

<div class="wishlist-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">Danh Sách Yêu Thích</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Yêu thích</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if(count($wishlistItems) > 0)
        <div class="wishlist-grid">
            <div class="row">
                @foreach($wishlistItems as $item)
                <div class="col-lg-3 col-md-4 col-6 mb-4" data-product-id="{{ $item['product']->id }}">
                    <div class="wishlist-card">
                        <div class="product-image">
                            <a href="{{ route('product.show', $item['product']->id) }}">
                                <img src="{{ $item['image'] }}" referrerpolicy="no-referrer" alt="{{ $item['product']->name }}">
                            </a>
                            <div class="product-actions">
                                <button type="button" class="action-btn btn-add-cart" data-product-id="{{ $item['product']->id }}" title="Thêm vào giỏ">
                                    <i class="fas fa-shopping-bag"></i>
                                </button>
                                <button type="button" class="action-btn btn-remove-wishlist" data-product-id="{{ $item['product']->id }}" title="Xóa khỏi yêu thích">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h4 class="product-name">
                                <a href="{{ route('product.show', $item['product']->id) }}">{{ $item['product']->name }}</a>
                            </h4>
                            <div class="product-price">
                                <span class="current-price">Từ {{ number_format($item['min_price'], 0, ',', '.') }}₫</span>
                            </div>
                            @if($item['variants']->count() > 0)
                            <div class="product-variants">
                                <span class="variants-count">{{ $item['variants']->count() }} biến thể</span>
                            </div>
                            @endif
                            <div class="product-actions-bottom">
                                <a href="{{ route('product.show', $item['product']->id) }}" class="btn-view-detail">
                                    <i class="fas fa-eye mr-2"></i>Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="wishlist-actions">
            <a href="{{ route('home') }}" class="btn-continue">
                <i class="fas fa-arrow-left mr-2"></i>Tiếp tục mua sắm
            </a>
            <button type="button" class="btn-clear-wishlist">
                <i class="fas fa-trash mr-2"></i>Xóa toàn bộ
            </button>
        </div>
        @else
        <div class="empty-wishlist">
            <div class="empty-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h3>Danh sách yêu thích của bạn đang trống</h3>
            <p>Hãy thêm sản phẩm yêu thích để xem lại sau</p>
            <a href="{{ route('home') }}" class="btn-shop-now">
                <i class="fas fa-shopping-bag mr-2"></i>Mua sắm ngay
            </a>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.wishlist-page {
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

.wishlist-grid {
    margin-bottom: 40px;
}

.wishlist-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.wishlist-card .product-image {
    position: relative;
    overflow: hidden;
    padding-top: 100%;
    background: #f5f5f5;
}

.wishlist-card .product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.wishlist-card:hover .product-image img {
    transform: scale(1.1);
}

.product-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.wishlist-card:hover .product-actions {
    opacity: 1;
}

.action-btn {
    width: 40px;
    height: 40px;
    background: #fff;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    color: #1a1a1a;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.action-btn:hover {
    background: #D4AF37;
    color: #fff;
    transform: scale(1.1);
}

.action-btn.btn-remove-wishlist:hover {
    background: #dc3545;
}

.product-info {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 15px;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.product-name a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.3s;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-name a:hover {
    color: #D4AF37;
}

.product-price {
    margin-bottom: 10px;
}

.current-price {
    font-size: 18px;
    font-weight: 700;
    color: #D4AF37;
}

.product-variants {
    margin-bottom: 15px;
}

.variants-count {
    font-size: 13px;
    color: #666;
    background: #f8f9fa;
    padding: 4px 10px;
    border-radius: 12px;
}

.product-actions-bottom {
    margin-top: auto;
    padding-top: 15px;
}

.btn-view-detail {
    display: block;
    width: 100%;
    padding: 10px;
    background: #D4AF37;
    color: #1a1a1a;
    text-align: center;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-view-detail:hover {
    background: #C4A037;
    color: #1a1a1a;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212,175,55,0.3);
}

.wishlist-actions {
    display: flex;
    justify-content: space-between;
    padding: 30px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.btn-continue, .btn-clear-wishlist {
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

.btn-clear-wishlist {
    background: #dc3545;
    color: #fff;
}

.btn-clear-wishlist:hover {
    background: #c82333;
}

.empty-wishlist {
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

.empty-wishlist h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.empty-wishlist p {
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart from wishlist
    $('.btn-add-cart').on('click', function() {
        const productId = $(this).data('product-id');
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { product_id: productId, quantity: 1 },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    updateCartHeader();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.error || 'Có lỗi xảy ra');
            }
        });
    });
    
    // Remove from wishlist
    $('.btn-remove-wishlist').on('click', function() {
        const productId = $(this).data('product-id');
        if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi yêu thích?')) {
            $.ajax({
                url: `{{ url('wishlist/remove') }}/${productId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $(`.wishlist-card[data-product-id="${productId}"]`).closest('.col-lg-3').fadeOut(300, function() {
                            $(this).remove();
                            if ($('.wishlist-card').length === 0) {
                                location.reload();
                            }
                            updateWishlistHeader();
                        });
                    }
                }
            });
        }
    });
    
    // Clear wishlist
    $('.btn-clear-wishlist').on('click', function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ danh sách yêu thích?')) {
            $.ajax({
                url: '{{ route("wishlist.clear") }}',
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
    
    function updateCartHeader() {
        $.get('{{ route("cart.data") }}', function(data) {
            $('.shopping .total-count').text(data.count);
        });
    }
    
    function updateWishlistHeader() {
        $.get('{{ route("wishlist.data") }}', function(data) {
            $('.fa-heart-o').closest('.shopping').find('.total-count').text(data.count);
        });
    }
});
</script>
@endpush

@endsection

