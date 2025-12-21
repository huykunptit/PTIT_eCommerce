@extends('backend.layouts.master')
@section('title','Dashboard - Nhân viên bán hàng')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard - Nhân viên bán hàng</h1>
      <div>
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa fa-home"></i> Về trang chủ
        </a>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn được phân công</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_assigned'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-list fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn chờ xử lý</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_orders'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clock fa-2x text-gray-300"></i>
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
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Đơn đang xử lý</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['processing_orders'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-cog fa-2x text-gray-300"></i>
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
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hoàn thành hôm nay</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['completed_today'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-check-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Đơn hàng được phân công</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders ?? [] as $order)
              <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                <td>
                  <span class="badge badge-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  <form action="{{ route('employee.orders.update-status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                      <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                      <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                      <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đã gửi</option>
                    </select>
                  </form>
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

