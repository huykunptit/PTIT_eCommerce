@extends('backend.layouts.master')
@section('title','Dashboard - Nhân viên đóng hàng')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard - Nhân viên đóng hàng</h1>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn cần đóng gói</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_to_pack'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-box fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã đóng hôm nay</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['packed_today'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-check-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đã đóng</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_packed'] ?? 0) }}</div>
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
        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng cần đóng gói</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Số lượng sản phẩm</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders ?? [] as $order)
              <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $order->items->sum('quantity') ?? 0 }}</td>
                <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                <td>
                  <span class="badge badge-{{ $order->status == 'shipped' ? 'success' : 'warning' }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
                <td>
                  @if($order->status == 'processing')
                  <form action="{{ route('employee.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="shipped">
                    <button type="submit" class="btn btn-sm btn-success">
                      <i class="fa fa-check"></i> Xác nhận đã đóng gói
                    </button>
                  </form>
                  @else
                  <span class="badge badge-success">Đã đóng gói</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">Chưa có đơn hàng nào</td>
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

