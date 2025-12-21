@extends('frontend.layouts.master')
@section('title','Chi Tiết Đơn Hàng - PTIT eCommerce')
@section('main-content')

<div class="order-detail-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1 class="page-title">Chi Tiết Đơn Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.orders') }}">Đơn hàng của tôi</a></li>
                            <li class="breadcrumb-item active">Đơn #{{ $order->id }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Info -->
                <div class="order-info-card">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Mã đơn hàng:</span>
                            <span class="value">#{{ $order->id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Ngày đặt:</span>
                            <span class="value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Trạng thái thanh toán:</span>
                            <span class="value">
                                @if($order->status == 'paid')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                @elseif($order->status == 'pending_payment')
                                    <span class="badge badge-warning">Chờ thanh toán</span>
                                @else
                                    <span class="badge badge-secondary">Chưa thanh toán</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Trạng thái vận chuyển:</span>
                            <span class="value">
                                @php
                                    $shippingStatusMap = [
                                        'pending_pickup' => ['text' => 'Chờ lấy hàng', 'class' => 'warning'],
                                        'in_transit' => ['text' => 'Đang vận chuyển', 'class' => 'info'],
                                        'delivered' => ['text' => 'Đã nhận hàng', 'class' => 'success'],
                                        'cancelled' => ['text' => 'Đã hủy', 'class' => 'danger'],
                                        'returned' => ['text' => 'Đã hoàn trả', 'class' => 'secondary'],
                                    ];
                                    $shippingStatus = $shippingStatusMap[$order->shipping_status] ?? ['text' => $order->shipping_status, 'class' => 'secondary'];
                                @endphp
                                <span class="badge badge-{{ $shippingStatus['class'] }}">{{ $shippingStatus['text'] }}</span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phương thức thanh toán:</span>
                            <span class="value">
                                @if($order->payment_method == 'vnpay')
                                    VNPay
                                @elseif($order->payment_method == 'cod')
                                    Thanh toán khi nhận hàng
                                @else
                                    {{ $order->payment_method }}
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Tổng tiền:</span>
                            <span class="value" style="color:#D4AF37;font-weight:600;font-size:18px;">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-items-card">
                    <h3>Sản phẩm</h3>
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                        <div class="order-item-row">
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
                                <h4><a href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a></h4>
                                @if($item->variant)
                                <p class="variant-info">
                                    @php
                                        $attrs = is_array($item->variant->attributes) ? $item->variant->attributes : (is_string($item->variant->attributes) ? json_decode($item->variant->attributes, true) : []);
                                    @endphp
                                    Size: {{ $attrs['size'] ?? 'N/A' }}
                                    @if(isset($attrs['option']))
                                    | Option: {{ $attrs['option'] }}
                                    @endif
                                </p>
                                @endif
                                <p class="item-quantity">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <div class="item-price">
                                {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}₫
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="shipping-info-card">
                    <h3>Thông tin giao hàng</h3>
                    <div class="shipping-details">
                        <p><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                        <p><strong>Email:</strong> {{ $order->shipping_email ?? 'N/A' }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        @if($order->notes)
                        <p><strong>Ghi chú:</strong> {{ $order->notes }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Actions -->
                <div class="order-actions-card">
                    <h3>Thao tác</h3>
                    
                    <!-- Cancel Order (only if pending_pickup) -->
                    @if($order->shipping_status == 'pending_pickup' && !$order->cancellation)
                    <div class="action-section">
                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelOrderModal">
                            <i class="ti-close"></i> Hủy đơn hàng
                        </button>
                    </div>
                    @endif

                    <!-- Return Order (only if delivered) -->
                    @if($order->shipping_status == 'delivered' && !$order->return)
                    <div class="action-section">
                        <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#returnOrderModal">
                            <i class="ti-reload"></i> Hoàn trả sản phẩm
                        </button>
                    </div>
                    @endif

                    <!-- Cancellation Status -->
                    @if($order->cancellation)
                    <div class="action-section">
                        <div class="alert alert-info">
                            <strong>Yêu cầu hủy đơn hàng:</strong><br>
                            Lý do: {{ $order->cancellation->reason_text }}<br>
                            Trạng thái: 
                            @if($order->cancellation->status == 'pending')
                                <span class="badge badge-warning">Đang chờ xử lý</span>
                            @elseif($order->cancellation->status == 'approved')
                                <span class="badge badge-success">Đã duyệt</span>
                            @else
                                <span class="badge badge-danger">Đã từ chối</span>
                            @endif
                            @if($order->cancellation->admin_note)
                            <br><small>Ghi chú: {{ $order->cancellation->admin_note }}</small>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Return Status -->
                    @if($order->return)
                    <div class="action-section">
                        <div class="alert alert-info">
                            <strong>Yêu cầu hoàn trả:</strong><br>
                            Lý do: {{ $order->return->reason_text }}<br>
                            Trạng thái: 
                            @if($order->return->status == 'pending')
                                <span class="badge badge-warning">Đang chờ xử lý</span>
                            @elseif($order->return->status == 'approved')
                                <span class="badge badge-success">Đã duyệt</span>
                            @elseif($order->return->status == 'processing')
                                <span class="badge badge-info">Đang xử lý</span>
                            @elseif($order->return->status == 'completed')
                                <span class="badge badge-success">Hoàn thành</span>
                            @else
                                <span class="badge badge-danger">Đã từ chối</span>
                            @endif
                            @if($order->return->admin_note)
                            <br><small>Ghi chú: {{ $order->return->admin_note }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }}?</p>
                    <div class="form-group">
                        <label>Lý do hủy đơn hàng <span class="text-danger">*</span></label>
                        <select name="reason" class="form-control" required>
                            <option value="">-- Chọn lý do --</option>
                            <option value="changed_mind">Thay đổi ý định</option>
                            <option value="found_cheaper">Tìm thấy sản phẩm rẻ hơn</option>
                            <option value="wrong_item">Đặt nhầm sản phẩm</option>
                            <option value="delivery_too_long">Thời gian giao hàng quá lâu</option>
                            <option value="payment_issue">Vấn đề thanh toán</option>
                            <option value="other">Lý do khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chi tiết lý do (tùy chọn)</label>
                        <textarea name="reason_detail" class="form-control" rows="3" placeholder="Mô tả chi tiết lý do hủy đơn hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Order Modal -->
<div class="modal fade" id="returnOrderModal" tabindex="-1" role="dialog" aria-labelledby="returnOrderModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content return-modal-content">
            <div class="return-modal-header">
                <div class="return-modal-header-left">
                    <div class="return-modal-icon">
                        <i class="fa fa-undo-alt"></i>
                    </div>
                    <div>
                        <h4 class="return-modal-title">Hoàn trả sản phẩm</h4>
                        <p class="return-modal-subtitle">Đơn hàng #{{ $order->id }}</p>
                    </div>
                </div>
                <button type="button" class="return-modal-close" data-dismiss="modal" aria-label="Đóng">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('orders.return', $order->id) }}" method="POST" id="returnOrderForm">
                @csrf
                <div class="return-modal-body">
                    <!-- Order Items Preview -->
                    <div class="return-order-items">
                        <h5 class="return-section-title">
                            <i class="fa fa-shopping-bag"></i> Sản phẩm trong đơn hàng
                        </h5>
                        <div class="return-items-list">
                            @foreach($order->items as $item)
                            <div class="return-item-card">
                                @php
                                    $product = $item->product;
                                    $photos = explode(',', (string)($product->image_url ?? ''));
                                    $img = trim($photos[0] ?? '');
                                    $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                                        ? $img 
                                        : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                                @endphp
                                <div class="return-item-image">
                                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" referrerpolicy="no-referrer">
                                </div>
                                <div class="return-item-info">
                                    <h6 class="return-item-name">{{ $product->name }}</h6>
                                    @if($item->variant)
                                    <p class="return-item-variant">
                                        @php
                                            $attrs = is_array($item->variant->attributes) ? $item->variant->attributes : (is_string($item->variant->attributes) ? json_decode($item->variant->attributes, true) : []);
                                        @endphp
                                        <span class="variant-badge">Size: {{ $attrs['size'] ?? 'N/A' }}</span>
                                        @if(isset($attrs['option']))
                                        <span class="variant-badge">Option: {{ $attrs['option'] }}</span>
                                        @endif
                                    </p>
                                    @endif
                                    <div class="return-item-meta">
                                        <span class="return-item-qty">
                                            <i class="fa fa-cube"></i> Số lượng: {{ $item->quantity }}
                                        </span>
                                        <span class="return-item-price">
                                            {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Return Reason Section -->
                    <div class="return-reason-section">
                        <h5 class="return-section-title">
                            <i class="fa fa-exclamation-circle"></i> Lý do hoàn trả <span class="text-danger">*</span>
                        </h5>
                        <div class="return-reason-options">
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_defective" value="defective" required>
                                <label for="reason_defective">
                                    <i class="fa fa-times-circle"></i>
                                    <span>Sản phẩm bị lỗi</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_wrong_item" value="wrong_item" required>
                                <label for="reason_wrong_item">
                                    <i class="fa fa-exchange-alt"></i>
                                    <span>Nhận nhầm sản phẩm</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_not_as_described" value="not_as_described" required>
                                <label for="reason_not_as_described">
                                    <i class="fa fa-file-alt"></i>
                                    <span>Không đúng như mô tả</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_damaged" value="damaged_during_shipping" required>
                                <label for="reason_damaged">
                                    <i class="fa fa-box-open"></i>
                                    <span>Bị hỏng trong quá trình vận chuyển</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_size" value="size_issue" required>
                                <label for="reason_size">
                                    <i class="fa fa-ruler"></i>
                                    <span>Vấn đề về kích thước</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_color" value="color_issue" required>
                                <label for="reason_color">
                                    <i class="fa fa-palette"></i>
                                    <span>Vấn đề về màu sắc</span>
                                </label>
                            </div>
                            <div class="reason-option">
                                <input type="radio" name="reason" id="reason_other" value="other" required>
                                <label for="reason_other">
                                    <i class="fa fa-ellipsis-h"></i>
                                    <span>Lý do khác</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="return-details-section">
                        <label class="return-details-label">
                            <i class="fa fa-comment-alt"></i> Chi tiết lý do (tùy chọn)
                        </label>
                        <textarea 
                            name="reason_detail" 
                            class="return-details-textarea" 
                            rows="4" 
                            placeholder="Vui lòng mô tả chi tiết lý do hoàn trả để chúng tôi có thể hỗ trợ bạn tốt hơn..."
                        ></textarea>
                    </div>
                </div>
                
                <div class="return-modal-footer">
                    <button type="button" class="btn-return-cancel" data-dismiss="modal">
                        <i class="fa fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn-return-submit">
                        <i class="fa fa-check"></i> Xác nhận hoàn trả
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.order-detail-page {
    padding: 40px 0 80px;
    background: #f8f9fa;
    min-height: 60vh;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 15px;
}

.order-info-card, .order-items-card, .shipping-info-card, .order-actions-card {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.order-info-card h3, .order-items-card h3, .shipping-info-card h3, .order-actions-card h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #1a1a1a;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item .label {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.info-item .value {
    font-size: 14px;
    color: #1a1a1a;
    font-weight: 500;
}

.order-item-row {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item-row:last-child {
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
}

.item-details h4 a {
    color: #1a1a1a;
    text-decoration: none;
}

.item-details h4 a:hover {
    color: #D4AF37;
}

.variant-info, .item-quantity {
    font-size: 12px;
    color: #666;
    margin: 0;
}

.item-price {
    font-size: 16px;
    font-weight: 600;
    color: #D4AF37;
}

.shipping-details p {
    margin-bottom: 10px;
    font-size: 14px;
}

.action-section {
    margin-bottom: 15px;
}

.action-section:last-child {
    margin-bottom: 0;
}

@media (max-width: 991px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}

/* ========== Return Modal Styles ========== */
.return-modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.return-modal-header {
    background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
    color: white;
    padding: 24px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.return-modal-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.return-modal-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    backdrop-filter: blur(10px);
}

.return-modal-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: white;
}

.return-modal-subtitle {
    margin: 4px 0 0 0;
    font-size: 14px;
    opacity: 0.9;
}

.return-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    backdrop-filter: blur(10px);
}

.return-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.return-modal-body {
    padding: 30px;
    max-height: 70vh;
    overflow-y: auto;
}

.return-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.return-section-title i {
    color: #D4AF37;
}

/* Order Items Preview */
.return-order-items {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f0f0f0;
}

.return-items-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.return-item-card {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: all 0.2s;
}

.return-item-card:hover {
    background: #f0f0f0;
    border-color: #D4AF37;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.1);
}

.return-item-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.return-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.return-item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.return-item-name {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.4;
}

.return-item-variant {
    margin: 0;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.variant-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #e9ecef;
    color: #666;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.return-item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.return-item-qty {
    font-size: 13px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 6px;
}

.return-item-qty i {
    color: #D4AF37;
}

.return-item-price {
    font-size: 16px;
    font-weight: 700;
    color: #D4AF37;
}

/* Return Reason Options */
.return-reason-section {
    margin-bottom: 30px;
}

.return-reason-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
}

.reason-option {
    position: relative;
}

.reason-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.reason-option label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.reason-option label i {
    font-size: 18px;
    color: #666;
    width: 24px;
    text-align: center;
}

.reason-option input[type="radio"]:checked + label {
    background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
    border-color: #D4AF37;
    color: white;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

.reason-option input[type="radio"]:checked + label i {
    color: white;
}

.reason-option label:hover {
    border-color: #D4AF37;
    background: #fff9e6;
}

/* Details Section */
.return-details-section {
    margin-top: 24px;
}

.return-details-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.return-details-label i {
    color: #D4AF37;
}

.return-details-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: all 0.2s;
    background: #f8f9fa;
}

.return-details-textarea:focus {
    outline: none;
    border-color: #D4AF37;
    background: white;
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
}

.return-details-textarea::placeholder {
    color: #999;
}

/* Modal Footer */
.return-modal-footer {
    padding: 20px 30px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-return-cancel,
.btn-return-submit {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-return-cancel {
    background: #6c757d;
    color: white;
}

.btn-return-cancel:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-return-submit {
    background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

.btn-return-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4);
}

.btn-return-submit:active {
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
    .return-modal-body {
        padding: 20px;
        max-height: 60vh;
    }
    
    .return-reason-options {
        grid-template-columns: 1fr;
    }
    
    .return-item-card {
        flex-direction: column;
    }
    
    .return-item-image {
        width: 100%;
        height: 200px;
    }
    
    .return-modal-footer {
        flex-direction: column-reverse;
    }
    
    .btn-return-cancel,
    .btn-return-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const returnForm = document.getElementById('returnOrderForm');
    const reasonInputs = document.querySelectorAll('input[name="reason"]');
    const submitBtn = document.querySelector('.btn-return-submit');
    
    if (returnForm) {
        // Form validation
        returnForm.addEventListener('submit', function(e) {
            const selectedReason = document.querySelector('input[name="reason"]:checked');
            
            if (!selectedReason) {
                e.preventDefault();
                alert('Vui lòng chọn lý do hoàn trả');
                
                // Highlight first reason option
                const firstOption = document.querySelector('.reason-option:first-child label');
                if (firstOption) {
                    firstOption.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        firstOption.style.animation = '';
                    }, 500);
                }
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
        });
        
        // Auto-focus on first reason when modal opens
        $('#returnOrderModal').on('shown.bs.modal', function() {
            const firstReason = document.querySelector('input[name="reason"]');
            if (firstReason) {
                // Scroll to reason section
                const reasonSection = document.querySelector('.return-reason-section');
                if (reasonSection) {
                    reasonSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        });
        
        // Reset form when modal closes
        $('#returnOrderModal').on('hidden.bs.modal', function() {
            returnForm.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-check"></i> Xác nhận hoàn trả';
        });
    }
    
    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
})();
</script>
@endpush

@endsection

