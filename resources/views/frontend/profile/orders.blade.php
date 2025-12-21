@extends('frontend.layouts.master')
@section('title','Đơn hàng của tôi - PTIT eCommerce')
@section('main-content')

@php
    $breadcrumbs = [
        ['title' => 'Trang chủ', 'url' => route('home')],
        ['title' => 'Tài khoản', 'url' => route('user.profile')],
        ['title' => 'Đơn hàng của tôi']
    ];
@endphp
@include('frontend.components.breadcrumbs')

<section class="orders-section" style="padding: 40px 0; background: #f8f9fa; min-height: 60vh;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4" style="font-size: 32px; font-weight: 700; color: #1a1a1a;">
                    <i class="fa fa-shopping-bag mr-2" style="color: #D4AF37;"></i>
                    Đơn hàng của tôi
                </h1>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Tìm kiếm theo mã đơn hàng:</label>
                            <input type="text" id="orderSearch" class="form-control" placeholder="Nhập mã đơn hàng...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Lọc theo trạng thái:</label>
                            <select id="orderStatusFilter" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="pending_payment">Chờ thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipped">Đã gửi hàng</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Sắp xếp theo:</label>
                            <select id="orderSort" class="form-control">
                                <option value="created_at_desc">Ngày đặt hàng (mới nhất)</option>
                                <option value="created_at_asc">Ngày đặt hàng (cũ nhất)</option>
                                <option value="total_desc">Tổng tiền cao → thấp</option>
                                <option value="total_asc">Tổng tiền thấp → cao</option>
                                <option value="status">Trạng thái</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" id="orderReset" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border: none; color: #1a1a1a; font-weight: 600;">
                                <i class="fa fa-times"></i> Đặt lại
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <span id="orderFilterResult" style="font-size:14px;color:#666;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($orders) > 0)
        <div class="row">
            @foreach($orders as $order)
            <div class="col-12 mb-4 order-card-item"
                 data-order-id="{{ $order->id }}"
                 data-status="{{ $order->status }}"
                 data-total="{{ $order->total_amount }}"
                 data-created="{{ $order->created_at->timestamp }}">
                <div class="order-card" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
                    <!-- Order Header -->
                    <div class="order-header" style="background: #fff; padding: 16px 20px; color: #1a1a1a; border-bottom: 1px solid #f0f0f0;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 style="margin: 0; font-size: 20px; font-weight: 600;">
                                    Đơn hàng #{{ $order->id }}
                                </h3>
                                <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">
                                    <i class="fa fa-calendar mr-1"></i>
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                @php
                                    $statusLabel = [
                                        'pending' => 'Chờ xử lý',
                                        'pending_payment' => 'Chờ thanh toán',
                                        'paid' => 'Đã thanh toán',
                                        'processing' => 'Đang xử lý',
                                        'shipped' => 'Đã gửi hàng',
                                        'delivered' => 'Đã giao hàng',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                @endphp
                                <span class="badge badge-status-{{ $order->status }}" 
                                      style="padding: 8px 16px; font-size: 14px; font-weight: 600;">
                                    {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Order Progress Timeline -->
                        @if($order->status != 'cancelled')
                        <div class="order-timeline" style="padding: 15px 20px; background: rgba(0,0,0,0.05);">
                            <div class="timeline-steps">
                                @php
                                    $statuses = [
                                        'pending' => ['label' => 'Chờ xử lý', 'icon' => 'fa-clock'],
                                        'processing' => ['label' => 'Đang xử lý', 'icon' => 'fa-cog'],
                                        'paid' => ['label' => 'Đã thanh toán', 'icon' => 'fa-check-circle'],
                                        'shipped' => ['label' => 'Đã gửi hàng', 'icon' => 'fa-shipping-fast'],
                                        'delivered' => ['label' => 'Đã giao hàng', 'icon' => 'fa-check-circle'],
                                    ];
                                    $statusKeys = array_keys($statuses);
                                    $currentStatusIndex = array_search($order->status, $statusKeys);
                                    if ($currentStatusIndex === false) $currentStatusIndex = -1;
                                @endphp
                                @foreach($statuses as $key => $status)
                                    @php
                                        $stepIndex = array_search($key, $statusKeys);
                                        $isActive = $stepIndex <= $currentStatusIndex;
                                        $isCurrent = $stepIndex == $currentStatusIndex;
                                    @endphp
                                    <div class="timeline-step {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}" 
                                         style="flex: 1; text-align: center; position: relative;">
                                        <div class="step-icon" style="width: 40px; height: 40px; border-radius: 50%; background: {{ $isActive ? '#D4AF37' : '#ddd' }}; color: {{ $isActive ? '#1a1a1a' : '#999' }}; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 16px;">
                                            <i class="fa {{ $status['icon'] }}"></i>
                                        </div>
                                        <div class="step-label" style="font-size: 12px; color: {{ $isActive ? '#1a1a1a' : '#999' }}; font-weight: {{ $isActive ? '600' : '400' }};">
                                            {{ $status['label'] }}
                                        </div>
                                        @if($stepIndex < count($statuses) - 1)
                                            <div class="step-line" style="position: absolute; top: 20px; left: 60%; width: 80%; height: 2px; background: {{ $isActive && $stepIndex < $currentStatusIndex ? '#D4AF37' : '#ddd' }}; z-index: -1;"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Order Body -->
                    <div class="order-body" style="padding: 25px;">
                        <!-- Order Items -->
                        <div class="order-items mb-3">
                            <h5 style="font-weight: 600; margin-bottom: 15px; color: #1a1a1a;">
                                <i class="fa fa-box mr-2" style="color: #D4AF37;"></i>
                                Sản phẩm ({{ $order->items->count() }})
                            </h5>
                            @foreach($order->items as $item)
                            <div class="order-item-row" style="display: flex; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                <div style="width: 60px; height: 60px; margin-right: 15px; border-radius: 6px; overflow: hidden; flex-shrink: 0;">
                                    @php
                                        $photos = explode(',', (string)($item->product->image_url ?? ''));
                                        $img = trim($photos[0] ?? '');
                                        $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://'])
                                            ? $img
                                            : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $item->product->name ?? 'N/A' }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="flex: 1;">
                                    <h6 style="margin: 0 0 5px 0; font-weight: 600; color: #1a1a1a;">
                                        {{ $item->product->name ?? 'N/A' }}
                                    </h6>
                                    <p style="margin: 0; font-size: 14px; color: #666;">
                                        Số lượng: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}₫
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <strong style="color: #D4AF37; font-size: 16px;">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}₫
                                    </strong>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Order Info -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <h6 style="font-weight: 600; margin-bottom: 10px; color: #1a1a1a;">
                                        <i class="fa fa-truck mr-2" style="color: #D4AF37;"></i>
                                        Thông tin giao hàng
                                    </h6>
                                    <p style="margin: 5px 0; font-size: 14px;">
                                        <strong>Người nhận:</strong> {{ $order->shipping_name }}<br>
                                        <strong>SĐT:</strong> {{ $order->shipping_phone }}<br>
                                        <strong>Địa chỉ:</strong> {{ $order->shipping_address }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <h6 style="font-weight: 600; margin-bottom: 10px; color: #1a1a1a;">
                                        <i class="fa fa-credit-card mr-2" style="color: #D4AF37;"></i>
                                        Thanh toán
                                    </h6>
                                    <p style="margin: 5px 0; font-size: 14px;">
                                        <strong>Phương thức:</strong> 
                                        @if($order->payment_method == 'vnpay')
                                            VNPay
                                        @elseif($order->payment_method == 'cod')
                                            Thanh toán khi nhận hàng (COD)
                                        @else
                                            {{ ucfirst($order->payment_method) }}
                                        @endif
                                        <br>
                                        <strong>Trạng thái:</strong> 
                                        @if($order->status === 'paid' || ($order->payments && $order->payments->where('status', 'success')->count() > 0))
                                            <span style="color: #28a745;">Đã thanh toán</span>
                                        @elseif($order->status === 'pending_payment')
                                            <span style="color: #ffc107;">Chờ thanh toán</span>
                                        @else
                                            <span style="color: #ffc107;">Chưa thanh toán</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Footer -->
                        <div class="order-footer" style="border-top: 2px solid #eee; padding-top: 20px; margin-top: 20px;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    @if($order->status == 'pending' || $order->status == 'processing')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-times"></i> Hủy đơn hàng
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                <div class="col-md-6 text-right">
                                    <div style="font-size: 18px; font-weight: 600; color: #1a1a1a;">
                                        Tổng tiền: 
                                        <span style="color: #D4AF37; font-size: 24px;">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa fa-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $orders->links() }}
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-12">
                <div class="empty-orders" style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px;">
                    <i class="fa fa-shopping-bag" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">Bạn chưa có đơn hàng nào</h3>
                    <p style="color: #999; margin-bottom: 30px;">Hãy bắt đầu mua sắm ngay hôm nay!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%); border: none; padding: 12px 40px; font-weight: 600; color: #1a1a1a;">
                        <i class="fa fa-shopping-cart mr-2"></i> Mua sắm ngay
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@push('styles')
<style>
.badge-status-pending,
.badge-status-pending_payment {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge-status-paid,
.badge-status-delivered {
    background-color: #28a745 !important;
    color: #fff !important;
}

.badge-status-processing {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

.badge-status-shipped {
    background-color: #007bff !important;
    color: #fff !important;
}

.badge-status-cancelled {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.order-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15) !important;
}

.timeline-steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.timeline-step {
    position: relative;
}

.timeline-step .step-icon {
    transition: all 0.3s ease;
}

.timeline-step.active .step-icon {
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
}

.timeline-step.current .step-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(212, 175, 55, 0.1);
    }
}

.filter-card {
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .timeline-steps {
        flex-wrap: wrap;
    }
    
    .timeline-step {
        flex: 0 0 50%;
        margin-bottom: 15px;
    }
    
    .step-line {
        display: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const cards = Array.from(document.querySelectorAll('.order-card-item'));
    const searchInput = document.getElementById('orderSearch');
    const statusSelect = document.getElementById('orderStatusFilter');
    const sortSelect = document.getElementById('orderSort');
    const resetBtn = document.getElementById('orderReset');
    const resultBox = document.getElementById('orderFilterResult');

    function applyFilters() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        const st = statusSelect?.value || '';
        const sort = sortSelect?.value || 'created_at_desc';

        let visible = cards.filter(card => {
            const id = (card.dataset.orderId || '').toLowerCase();
            const status = (card.dataset.status || '').toLowerCase();
            const matchId = !q || id.includes(q);
            const matchStatus = !st || status === st;
            return matchId && matchStatus;
        });

        // sort
        visible.sort((a,b)=>{
            const ta = parseFloat(a.dataset.total || '0');
            const tb = parseFloat(b.dataset.total || '0');
            const ca = parseInt(a.dataset.created || '0',10);
            const cb = parseInt(b.dataset.created || '0',10);
            const sa = (a.dataset.status || '').localeCompare(b.dataset.status || '');
            switch(sort) {
                case 'created_at_asc': return ca - cb;
                case 'total_desc': return tb - ta;
                case 'total_asc': return ta - tb;
                case 'status': return sa;
                case 'created_at_desc':
                default: return cb - ca;
            }
        });

        // hide all, show visible
        cards.forEach(c => c.style.display = 'none');
        visible.forEach(c => c.style.display = '');

        if (resultBox) {
            resultBox.textContent = `Đang hiển thị ${visible.length} / ${cards.length} đơn hàng`;
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (statusSelect) statusSelect.addEventListener('change', applyFilters);
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);
    if (resetBtn) resetBtn.addEventListener('click', function() {
        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = '';
        if (sortSelect) sortSelect.value = 'created_at_desc';
        applyFilters();
    });

    document.addEventListener('DOMContentLoaded', applyFilters);
})();
</script>
@endpush

@endsection
