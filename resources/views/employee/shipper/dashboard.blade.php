@extends('backend.layouts.master')
@section('title','Dashboard - Nhân viên giao hàng')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard - Nhân viên giao hàng</h1>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn cần giao</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_to_ship'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-truck fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đang giao</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['in_transit'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-shipping-fast fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã giao hôm nay</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['delivered_today'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-check-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đã giao</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_delivered'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-check fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng cần giao</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Địa chỉ giao</th>
                <th>SĐT</th>
                <th>Trạng thái</th>
                <th>Phân công</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders ?? [] as $order)
              <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ Str::limit($order->shipping_address ?? 'N/A', 50) }}</td>
                <td>{{ $order->shipping_phone ?? 'N/A' }}</td>
                <td>
                  <span class="badge badge-{{ $order->shipping_status == 'delivered' ? 'success' : ($order->shipping_status == 'cancelled' ? 'danger' : 'warning') }}">
                    {{ \App\Helpers\StatusLabel::shippingStatus($order->shipping_status) }}
                  </span>
                </td>
                <td>
                  @if($order->assigned_shipper)
                    <span class="badge badge-success">Đã nhận</span>
                  @else
                    <form action="{{ route('employee.orders.assign', $order->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-hand-paper"></i> Nhận đơn
                      </button>
                    </form>
                  @endif
                </td>
                <td>
                  <form action="{{ route('employee.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <select name="shipping_status" class="form-control form-control-sm" onchange="this.form.submit()">
                      <option value="pending_pickup" {{ $order->shipping_status == 'pending_pickup' ? 'selected' : '' }}>Chờ lấy hàng</option>
                      <option value="in_transit" {{ $order->shipping_status == 'in_transit' ? 'selected' : '' }}>Đang giao</option>
                      <option value="delivered" {{ $order->shipping_status == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                    </select>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">Chưa có đơn hàng nào</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if(isset($orders) && $orders->hasPages())
        <div class="mt-3">
          {{ $orders->links() }}
        </div>
        @endif
      </div>
    </div>
</div>
@endsection

