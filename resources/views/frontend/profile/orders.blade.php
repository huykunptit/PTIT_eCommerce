@extends('frontend.layouts.master')

@section('main-content')
<div class="container" style="padding: 50px 0;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: var(--gradient-primary, linear-gradient(135deg, #667eea 0%, #764ba2 100%)); color: white;">
                    <h4 style="margin:0;">Đơn đặt hàng của tôi</h4>
                </div>
                <div class="card-body" style="padding: 30px;">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Thanh toán</th>
                                        <th>Vận chuyển</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="color: var(--gold-color, #D4AF37); font-weight: 600;">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                        <td>
                                            @if($order->status == 'paid')
                                                <span class="badge badge-success">Đã thanh toán</span>
                                            @elseif($order->status == 'pending_payment')
                                                <span class="badge badge-warning">Chờ thanh toán</span>
                                            @else
                                                <span class="badge badge-secondary">Chưa thanh toán</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $shippingStatusMap = [
                                                    'pending_pickup' => ['text' => 'Chờ lấy hàng', 'class' => 'warning'],
                                                    'in_transit' => ['text' => 'Đang vận chuyển', 'class' => 'info'],
                                                    'delivered' => ['text' => 'Đã nhận hàng', 'class' => 'success'],
                                                    'cancelled' => ['text' => 'Đã hủy', 'class' => 'danger'],
                                                    'returned' => ['text' => 'Đã hoàn trả', 'class' => 'secondary'],
                                                ];
                                                $shippingStatus = $shippingStatusMap[$order->shipping_status ?? 'pending_pickup'] ?? ['text' => 'Chờ lấy hàng', 'class' => 'warning'];
                                            @endphp
                                            <span class="badge badge-{{ $shippingStatus['class'] }}">{{ $shippingStatus['text'] }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center" style="padding: 50px 0;">
                            <i class="ti-shopping-cart" style="font-size: 60px; color: #ddd;"></i>
                            <p style="margin-top: 20px; color: #666;">Bạn chưa có đơn đặt hàng nào</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

