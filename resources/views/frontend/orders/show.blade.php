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
<div class="modal fade" id="returnOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hoàn trả sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('orders.return', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bạn muốn hoàn trả đơn hàng #{{ $order->id }}?</p>
                    <div class="form-group">
                        <label>Lý do hoàn trả <span class="text-danger">*</span></label>
                        <select name="reason" class="form-control" required>
                            <option value="">-- Chọn lý do --</option>
                            <option value="defective">Sản phẩm bị lỗi</option>
                            <option value="wrong_item">Nhận nhầm sản phẩm</option>
                            <option value="not_as_described">Không đúng như mô tả</option>
                            <option value="damaged_during_shipping">Bị hỏng trong quá trình vận chuyển</option>
                            <option value="size_issue">Vấn đề về kích thước</option>
                            <option value="color_issue">Vấn đề về màu sắc</option>
                            <option value="other">Lý do khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chi tiết lý do (tùy chọn)</label>
                        <textarea name="reason_detail" class="form-control" rows="3" placeholder="Mô tả chi tiết lý do hoàn trả..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Xác nhận hoàn trả</button>
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
</style>
@endpush

@endsection

