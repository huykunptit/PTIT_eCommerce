@extends('backend.layouts.master')
@section('title','Dashboard - Nhân viên kiểm toán')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Dashboard - Nhân viên kiểm toán</h1>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn hàng</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_orders'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clipboard-list fa-2x text-gray-300"></i>
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
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng doanh thu</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}₫</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
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
        <div class="card border-left-danger shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Đơn đã hủy</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['cancelled_orders'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-times-circle fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products & Recent Orders -->
    <div class="row">
      <!-- Top Products -->
      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sản phẩm bán chạy</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>Tên sản phẩm</th>
                    <th>Đã bán</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($topProducts ?? [] as $product)
                  <tr>
                    <td>{{ Str::limit($product->name, 40) }}</td>
                    <td class="text-center">{{ number_format($product->total_sold) }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="2" class="text-center">Chưa có dữ liệu</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
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
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentOrders ?? [] as $order)
                  <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                    <td>
                      <span class="badge badge-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                      </span>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center">Chưa có đơn hàng</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

