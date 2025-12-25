@extends('backend.layouts.master')

@section('title','Chi Tiết Đơn Hàng')

@section('main-content')
<div class="card">
    <h5 class="card-header">
        Chi Tiết Đơn Hàng #{{ $order->id }}
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-secondary shadow-sm float-right">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </h5>
    <div class="card-body">
        @include('backend.layouts.notification')

        @if($order)
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Sản phẩm trong đơn hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            @php
                                                $product = $item->product;
                                                $photos = explode(',', (string)($product->image_url ?? ''));
                                                $img = trim($photos[0] ?? '');
                                                $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                                                    ? $img 
                                                    : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                                            @endphp
                                            <img src="{{ $imgSrc }}" style="width:60px;height:60px;object-fit:cover;" referrerpolicy="no-referrer">
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if($item->variant)
                                            <br><small>
                                                @php
                                                    $attrs = is_array($item->variant->attributes) ? $item->variant->attributes : (is_string($item->variant->attributes) ? json_decode($item->variant->attributes, true) : []);
                                                @endphp
                                                Size: {{ $attrs['size'] ?? 'N/A' }}
                                                @if(isset($attrs['option']))
                                                | Option: {{ $attrs['option'] }}
                                                @endif
                                            </small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                        <td>{{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}₫</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Tổng cộng:</th>
                                        <th>{{ number_format($order->total_amount, 0, ',', '.') }}₫</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Thông tin giao hàng</h6>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td><strong>Người nhận:</strong></td>
                                <td>{{ $order->shipping_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Số điện thoại:</strong></td>
                                <td>{{ $order->shipping_phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $order->shipping_email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ:</strong></td>
                                <td>{{ $order->shipping_address }}</td>
                            </tr>
                            @if($order->notes)
                            <tr>
                                <td><strong>Ghi chú:</strong></td>
                                <td>{{ $order->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Trạng thái đơn hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label><strong>Trạng thái thanh toán:</strong></label>
                            <div>
                                @if($order->status == 'paid')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                @elseif($order->status == 'pending_payment')
                                    <span class="badge badge-warning">Chờ thanh toán</span>
                                @else
                                    <span class="badge badge-secondary">Chưa thanh toán</span>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('admin.orders.update-shipping-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label><strong>Trạng thái vận chuyển:</strong></label>
                                <select name="shipping_status" class="form-control" required>
                                    <option value="pending_confirmation" {{ ($order->shipping_status ?? '') == 'pending_confirmation' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="pending_pickup" {{ ($order->shipping_status ?? 'pending_pickup') == 'pending_pickup' ? 'selected' : '' }}>Chờ lấy hàng</option>
                                    <option value="in_transit" {{ ($order->shipping_status ?? '') == 'in_transit' ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option value="delivered" {{ ($order->shipping_status ?? '') == 'delivered' ? 'selected' : '' }}>Đã nhận hàng</option>
                                    <option value="cancelled" {{ ($order->shipping_status ?? '') == 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
                                    <option value="returned" {{ ($order->shipping_status ?? '') == 'returned' ? 'selected' : '' }}>Hoàn trả</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Cập nhật trạng thái</button>
                        </form>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Phân công nhân viên</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.assign', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label><strong>Nhân viên bán hàng:</strong></label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">-- Chưa phân công --</option>
                                    @foreach(($salesStaff ?? []) as $u)
                                        <option value="{{ $u->id }}" {{ (int)($order->assigned_to ?? 0) === (int)$u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label><strong>Nhân viên đóng hàng:</strong></label>
                                <select name="assigned_packer" class="form-control">
                                    <option value="">-- Chưa phân công --</option>
                                    @foreach(($packerStaff ?? []) as $u)
                                        <option value="{{ $u->id }}" {{ (int)($order->assigned_packer ?? 0) === (int)$u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label><strong>Nhân viên giao hàng:</strong></label>
                                <select name="assigned_shipper" class="form-control">
                                    <option value="">-- Chưa phân công --</option>
                                    @foreach(($shipperStaff ?? []) as $u)
                                        <option value="{{ $u->id }}" {{ (int)($order->assigned_shipper ?? 0) === (int)$u->id ? 'selected' : '' }}>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">Lưu phân công</button>
                        </form>

                        <hr>

                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label><strong>Trạng thái xử lý (nội bộ):</strong></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ ($order->status ?? '') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="pending_payment" {{ ($order->status ?? '') == 'pending_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="paid" {{ ($order->status ?? '') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="shipped" {{ ($order->status ?? '') == 'shipped' ? 'selected' : '' }}>Đã đóng gói</option>
                                    <option value="completed" {{ ($order->status ?? '') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                                    <option value="canceled" {{ ($order->status ?? '') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-block">Cập nhật trạng thái xử lý</button>
                        </form>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>Thông tin thanh toán</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Phương thức:</td>
                                <td>
                                    @if($order->payment_method == 'vnpay')
                                        VNPay
                                    @elseif($order->payment_method == 'cod')
                                        Thanh toán khi nhận hàng
                                    @else
                                        {{ $order->payment_method }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tổng tiền:</td>
                                <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></td>
                            </tr>
                            @if($order->payments && $order->payments->count() > 0)
                            @foreach($order->payments as $payment)
                            <tr>
                                <td>Mã giao dịch:</td>
                                <td>{{ $payment->transaction_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Trạng thái:</td>
                                <td>
                                    @if($payment->status == 'success')
                                        <span class="badge badge-success">Thành công</span>
                                    @else
                                        <span class="badge badge-danger">Thất bại</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </table>

                        @if($order->status === 'pending_payment')
                            <div class="mt-3">
                                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST"
                                      onsubmit="return confirm('Xác nhận đã nhận đủ tiền cho đơn hàng này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-check-circle mr-1"></i> Xác nhận đã thanh toán
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">
                                    Trạng thái hiện tại: <strong>Đang chờ thanh toán</strong>. Sau khi xác nhận, trạng thái đơn sẽ chuyển sang <strong>paid</strong>.
                                </small>
                            </div>
                        @elseif($order->status === 'paid')
                            <div class="mt-3">
                                <span class="badge badge-success"><i class="fa fa-check mr-1"></i>Đã thanh toán</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Cancellation Request -->
                @if($order->cancellation)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-white">
                        <h6>Yêu cầu hủy đơn hàng</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Lý do:</strong> {{ $order->cancellation->reason_text }}</p>
                        @if($order->cancellation->reason_detail)
                        <p><strong>Chi tiết:</strong> {{ $order->cancellation->reason_detail }}</p>
                        @endif
                        <p><strong>Trạng thái:</strong> 
                            @if($order->cancellation->status == 'pending')
                                <span class="badge badge-warning">Đang chờ</span>
                            @elseif($order->cancellation->status == 'approved')
                                <span class="badge badge-success">Đã duyệt</span>
                            @else
                                <span class="badge badge-danger">Đã từ chối</span>
                            @endif
                        </p>
                        @if($order->cancellation->status == 'pending')
                        <form action="{{ route('admin.orders.handle-cancellation', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label>Hành động:</label>
                                <select name="action" class="form-control" required>
                                    <option value="approve">Duyệt</option>
                                    <option value="reject">Từ chối</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ghi chú:</label>
                                <textarea name="admin_note" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Xử lý</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Return Request -->
                @if($order->return)
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6>Yêu cầu hoàn trả</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Lý do:</strong> {{ $order->return->reason_text }}</p>
                        @if($order->return->reason_detail)
                        <p><strong>Chi tiết:</strong> {{ $order->return->reason_detail }}</p>
                        @endif
                        <p><strong>Trạng thái:</strong> 
                            @if($order->return->status == 'pending')
                                <span class="badge badge-warning">Đang chờ</span>
                            @elseif($order->return->status == 'approved')
                                <span class="badge badge-success">Đã duyệt</span>
                            @elseif($order->return->status == 'processing')
                                <span class="badge badge-info">Đang xử lý</span>
                            @elseif($order->return->status == 'completed')
                                <span class="badge badge-success">Hoàn thành</span>
                            @else
                                <span class="badge badge-danger">Đã từ chối</span>
                            @endif
                        </p>
                        @if(in_array($order->return->status, ['pending', 'approved', 'processing']))
                        <form action="{{ route('admin.orders.handle-return', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label>Hành động:</label>
                                <select name="action" class="form-control" required>
                                    @if($order->return->status == 'pending')
                                    <option value="approve">Duyệt</option>
                                    <option value="reject">Từ chối</option>
                                    @elseif($order->return->status == 'approved')
                                    <option value="processing">Đang xử lý</option>
                                    <option value="completed">Hoàn thành</option>
                                    @elseif($order->return->status == 'processing')
                                    <option value="completed">Hoàn thành</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ghi chú:</label>
                                <textarea name="admin_note" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Xử lý</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        margin-bottom: 20px;
    }
</style>
@endpush
