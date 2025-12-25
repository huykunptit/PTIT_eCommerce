@extends('backend.layouts.master')
@section('title','PTIT  || Bảng điều khiển')
@section('main-content')
<div class="container-fluid">
    @include('backend.layouts.notification')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Bảng điều khiển</h1>
      <div>
        <div class="btn-group mr-2">
          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-download"></i> Xuất dữ liệu
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('admin.export.orders', ['format' => 'excel']) }}">
              <i class="fa fa-file-excel"></i> Xuất đơn hàng (Excel)
            </a>
            <a class="dropdown-item" href="{{ route('admin.export.products') }}">
              <i class="fa fa-file-excel"></i> Xuất sản phẩm (Excel)
            </a>
            <a class="dropdown-item" href="{{ route('admin.export.users') }}">
              <i class="fa fa-file-excel"></i> Xuất người dùng (Excel)
            </a>
          </div>
        </div>
        <button id="toggleDarkMode" class="btn btn-sm btn-outline-secondary mr-2">
          <i class="fa fa-moon" id="darkModeIcon"></i> <span id="darkModeText">Dark Mode</span>
        </button>
        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-primary">
          <i class="fa fa-clipboard-list"></i> Xem tất cả đơn hàng
        </a>
      </div>
    </div>

    <!-- Revenue Stats Row -->
    <div class="row mb-4">
      <!-- Total Revenue -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 revenue-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng doanh thu</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}₫</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-dollar-sign fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Today Revenue -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 revenue-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doanh thu hôm nay</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayRevenue ?? 0, 0, ',', '.') }}₫</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-calendar-day fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Month Revenue -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 revenue-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Doanh thu tháng này</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($monthRevenue ?? 0, 0, ',', '.') }}₫</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-calendar-alt fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 revenue-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng đơn hàng</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_orders'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-shopping-cart fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <!-- Category -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stats-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Danh mục</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_categories'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-sitemap fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stats-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sản phẩm</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_products'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-cubes fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Users -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 stats-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Người dùng</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-users fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Orders -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 stats-card">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Đơn chờ xử lý</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($orderStatusData['pending'] ?? 0) }}</div>
              </div>
              <div class="col-auto">
                <i class="fa fa-clock fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
      <!-- Revenue Chart -->
      <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4 chart-card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Doanh thu 7 ngày qua</h6>
            <div class="dropdown no-arrow">
              <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                <i class="fa fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
              </a>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    
      <!-- Order Status Pie Chart -->
      <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4 chart-card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng</h6>
          </div>
          <div class="card-body">
            <canvas id="orderStatusChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row mb-4">
      <!-- Orders Chart -->
      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4 chart-card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Số đơn hàng 7 ngày qua</h6>
          </div>
          <div class="card-body">
            <canvas id="ordersChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue Chart -->
      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4 chart-card">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Doanh thu 6 tháng qua</h6>
          </div>
          <div class="card-body">
            <canvas id="monthlyRevenueChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products & Recent Orders Row -->
    <div class="row">
      <!-- Top Selling Products -->
      <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4 chart-card">
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
                    <th>Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($topProducts ?? [] as $product)
                  <tr>
                    <td>{{ Str::limit($product->name, 30) }}</td>
                    <td class="text-center">{{ number_format($product->total_sold) }}</td>
                    <td class="text-right">{{ number_format($product->total_revenue, 0, ',', '.') }}₫</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="3" class="text-center">Chưa có dữ liệu</td>
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
        <div class="card shadow mb-4 chart-card">
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
                  @forelse($stats['recent_orders'] ?? [] as $order)
                  <tr>
                    <td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->id }}</a></td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                    <td>
                      @php
                        $label = \App\Helpers\StatusLabel::orderStatus($order->status);
                        $badge = in_array($order->status, ['paid', 'completed'], true) ? 'success' : (in_array($order->status, ['canceled', 'cancelled', 'cancel'], true) ? 'danger' : 'warning');
                      @endphp
                      <span class="badge badge-{{ $badge }}">{{ $label }}</span>
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

@push('styles')
<style>
  /* Dark Mode Styles */
  body.dark-mode {
    background-color: #1a1a1a;
    color: #e0e0e0;
  }

  body.dark-mode .card {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
  }

  body.dark-mode .card-header {
    background-color: #252525;
    border-bottom-color: #404040;
  }

  body.dark-mode .text-gray-800 {
    color: #e0e0e0 !important;
  }

  body.dark-mode .text-gray-600 {
    color: #b0b0b0 !important;
  }

  body.dark-mode .table {
    color: #e0e0e0;
  }

  body.dark-mode .table thead th {
    border-color: #404040;
  }

  body.dark-mode .table tbody td {
    border-color: #404040;
  }

  body.dark-mode .border-left-primary {
    border-left-color: #4e73df !important;
  }

  body.dark-mode .border-left-success {
    border-left-color: #1cc88a !important;
  }

  body.dark-mode .border-left-info {
    border-left-color: #36b9cc !important;
  }

  body.dark-mode .border-left-warning {
    border-left-color: #f6c23e !important;
  }

  body.dark-mode .border-left-danger {
    border-left-color: #e74a3b !important;
  }

  .revenue-card, .stats-card, .chart-card {
    transition: transform 0.2s;
  }

  .revenue-card:hover, .stats-card:hover, .chart-card:hover {
    transform: translateY(-2px);
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Dark Mode Toggle
(function() {
  const darkMode = localStorage.getItem('darkMode') === 'true';
  if (darkMode) {
    document.body.classList.add('dark-mode');
    updateDarkModeIcon(true);
  }

  document.getElementById('toggleDarkMode').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark);
    updateDarkModeIcon(isDark);
    // Re-render charts with new colors
    setTimeout(() => {
      renderCharts();
    }, 100);
  });

  function updateDarkModeIcon(isDark) {
    const icon = document.getElementById('darkModeIcon');
    const text = document.getElementById('darkModeText');
    if (isDark) {
      icon.className = 'fa fa-sun';
      text.textContent = 'Light Mode';
    } else {
      icon.className = 'fa fa-moon';
      text.textContent = 'Dark Mode';
    }
  }
})();

// Chart Colors
const isDarkMode = document.body.classList.contains('dark-mode');
const chartColors = {
  primary: isDarkMode ? '#4e73df' : '#4e73df',
  success: isDarkMode ? '#1cc88a' : '#1cc88a',
  info: isDarkMode ? '#36b9cc' : '#36b9cc',
  warning: isDarkMode ? '#f6c23e' : '#f6c23e',
  danger: isDarkMode ? '#e74a3b' : '#e74a3b',
  text: isDarkMode ? '#e0e0e0' : '#858796',
  grid: isDarkMode ? '#404040' : '#eaecf4'
};

let revenueChart, ordersChart, orderStatusChart, monthlyRevenueChart;

function renderCharts() {
  const isDark = document.body.classList.contains('dark-mode');
  const textColor = isDark ? '#e0e0e0' : '#858796';
  const gridColor = isDark ? '#404040' : '#eaecf4';

  // Revenue Chart
  const revenueCtx = document.getElementById('revenueChart');
  if (revenueChart) revenueChart.destroy();
  revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode(array_column($revenueData ?? [], 'date')) !!},
      datasets: [{
        label: 'Doanh thu (₫)',
        data: {!! json_encode(array_column($revenueData ?? [], 'revenue')) !!},
        borderColor: chartColors.success,
        backgroundColor: chartColors.success + '20',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: textColor }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: textColor },
          grid: { color: gridColor }
        },
        x: {
          ticks: { color: textColor },
          grid: { color: gridColor }
        }
      }
    }
  });

  // Orders Chart
  const ordersCtx = document.getElementById('ordersChart');
  if (ordersChart) ordersChart.destroy();
  ordersChart = new Chart(ordersCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode(array_column($ordersData ?? [], 'date')) !!},
      datasets: [{
        label: 'Số đơn hàng',
        data: {!! json_encode(array_column($ordersData ?? [], 'count')) !!},
        backgroundColor: chartColors.info,
        borderColor: chartColors.info,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: textColor }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: textColor, stepSize: 1 },
          grid: { color: gridColor }
        },
        x: {
          ticks: { color: textColor },
          grid: { color: gridColor }
        }
      }
    }
  });

  // Order Status Pie Chart
  const orderStatusCtx = document.getElementById('orderStatusChart');
  if (orderStatusChart) orderStatusChart.destroy();
  orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
      labels: ['Chờ xử lý', 'Đang xử lý', 'Đã gửi', 'Đã giao', 'Đã hủy'],
      datasets: [{
        data: [
          {{ $orderStatusData['pending'] ?? 0 }},
          {{ $orderStatusData['processing'] ?? 0 }},
          {{ $orderStatusData['shipped'] ?? 0 }},
          {{ $orderStatusData['delivered'] ?? 0 }},
          {{ $orderStatusData['cancelled'] ?? 0 }}
        ],
        backgroundColor: [
          chartColors.warning,
          chartColors.info,
          chartColors.primary,
          chartColors.success,
          chartColors.danger
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: textColor }
        }
      }
    }
  });

  // Monthly Revenue Chart
  const monthlyCtx = document.getElementById('monthlyRevenueChart');
  if (monthlyRevenueChart) monthlyRevenueChart.destroy();
  monthlyRevenueChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode(array_column($monthlyRevenue ?? [], 'month')) !!},
      datasets: [{
        label: 'Doanh thu (₫)',
        data: {!! json_encode(array_column($monthlyRevenue ?? [], 'revenue')) !!},
        backgroundColor: chartColors.primary,
        borderColor: chartColors.primary,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: textColor }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: textColor },
          grid: { color: gridColor }
        },
        x: {
          ticks: { color: textColor },
          grid: { color: gridColor }
        }
      }
    }
  });
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
  renderCharts();
});
</script>
@endpush
@endsection
