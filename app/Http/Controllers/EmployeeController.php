<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('employee');
    }

    /**
     * Dashboard cho nhân viên
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasPermission('employee.access')) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập khu vực nhân viên.');
        }
        $userRole = $user->getRole;
        $roleCode = $userRole->role_code ?? '';

        $data = [
            'role' => $roleCode,
            'roleName' => $userRole->role_name ?? 'Nhân viên',
        ];

        switch ($roleCode) {
            case 'sales':
                return $this->salesDashboard($data);
            case 'shipper':
                return $this->shipperDashboard($data);
            case 'packer':
                return $this->packerDashboard($data);
            case 'auditor':
                return $this->auditorDashboard($data);
            default:
                return redirect()->route('home')->with('error', 'Vai trò không hợp lệ');
        }
    }

    /**
     * Dashboard cho nhân viên bán hàng
     */
    private function salesDashboard($data)
    {
        // Đơn hàng được phân công cho nhân viên này
        $assignedOrders = Order::where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhereNull('assigned_to'); // Đơn chưa được phân công
            })
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Thống kê
        $stats = [
            'total_assigned' => Order::where('assigned_to', Auth::id())->count(),
            'pending_orders' => Order::where('assigned_to', Auth::id())
                ->where(function ($q) {
                    $q->where('shipping_status', 'pending_confirmation')->orWhereNull('shipping_status');
                })
                ->count(),
            'processing_orders' => Order::where('assigned_to', Auth::id())
                ->where('shipping_status', 'pending_pickup')
                ->count(),
            'completed_today' => Order::where('assigned_to', Auth::id())
                ->where('shipping_status', 'delivered')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $data['stats'] = $stats;
        $data['orders'] = $assignedOrders;

        return view('employee.sales.dashboard', $data);
    }

    /**
     * Dashboard cho nhân viên giao hàng
     */
    private function shipperDashboard($data)
    {
        // Đơn hàng cần giao
        $ordersToShip = Order::where(function($q) {
                $q->where('shipping_status', 'pending_pickup')
                  ->orWhere('shipping_status', 'in_transit');
            })
            ->where(function($q) {
                $q->where('assigned_shipper', Auth::id())
                  ->orWhereNull('assigned_shipper');
            })
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_to_ship' => Order::where('shipping_status', 'pending_pickup')
                ->where(function($q) {
                    $q->where('assigned_shipper', Auth::id())
                      ->orWhereNull('assigned_shipper');
                })
                ->count(),
            'in_transit' => Order::where('shipping_status', 'in_transit')
                ->where('assigned_shipper', Auth::id())
                ->count(),
            'delivered_today' => Order::where('shipping_status', 'delivered')
                ->where('assigned_shipper', Auth::id())
                ->whereDate('updated_at', today())
                ->count(),
            'total_delivered' => Order::where('shipping_status', 'delivered')
                ->where('assigned_shipper', Auth::id())
                ->count(),
        ];

        $data['stats'] = $stats;
        $data['orders'] = $ordersToShip;

        return view('employee.shipper.dashboard', $data);
    }

    /**
     * Dashboard cho nhân viên đóng hàng
     */
    private function packerDashboard($data)
    {
        // Đơn hàng cần đóng gói
        $ordersToPack = Order::whereIn('shipping_status', ['pending_confirmation', 'pending_pickup'])
            ->whereIn('status', ['pending', 'pending_payment', 'paid'])
            ->where(function($q) {
                $q->where('assigned_packer', Auth::id())
                  ->orWhereNull('assigned_packer');
            })
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_to_pack' => Order::whereIn('shipping_status', ['pending_confirmation', 'pending_pickup'])
                ->whereIn('status', ['pending', 'pending_payment', 'paid'])
                ->where(function($q) {
                    $q->where('assigned_packer', Auth::id())
                      ->orWhereNull('assigned_packer');
                })
                ->count(),
            'packed_today' => Order::where('status', 'shipped')
                ->where('assigned_packer', Auth::id())
                ->whereDate('updated_at', today())
                ->count(),
            'total_packed' => Order::where('status', 'shipped')
                ->where('assigned_packer', Auth::id())
                ->count(),
        ];

        $data['stats'] = $stats;
        $data['orders'] = $ordersToPack;

        return view('employee.packer.dashboard', $data);
    }

    /**
     * Dashboard cho nhân viên kiểm toán
     */
    private function auditorDashboard($data)
    {
        // Thống kê tổng quan
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'canceled')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'cancelled_orders' => Order::where('status', 'canceled')->count(),
        ];

        // Đơn hàng gần đây
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Top sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        $data['stats'] = $stats;
        $data['recentOrders'] = $recentOrders;
        $data['topProducts'] = $topProducts;

        return view('employee.auditor.dashboard', $data);
    }

    /**
     * Cập nhật trạng thái đơn hàng (cho nhân viên)
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userRole = $user->getRole;
        $roleCode = $userRole->role_code ?? '';

        // Phân quyền chi tiết cho thao tác cập nhật đơn
        if ($roleCode === 'shipper') {
            if (!$user->hasPermission('shipping.update')) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Bạn không có quyền cập nhật giao hàng'], 403);
                }
                return redirect()->back()->with('error', 'Bạn không có quyền cập nhật giao hàng');
            }
        } else {
            if (!$user->hasPermission('orders.manage')) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Bạn không có quyền chỉnh sửa đơn hàng'], 403);
                }
                return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa đơn hàng');
            }
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,pending_payment,paid,shipped,completed,canceled',
            'shipping_status' => 'nullable|in:pending_confirmation,pending_pickup,in_transit,delivered,cancelled,returned',
            'note' => 'nullable|string|max:500',
        ]);

        // Phân quyền theo vai trò
        switch ($roleCode) {
            case 'sales':
                if (isset($validated['shipping_status'])) {
                    $order->update(['shipping_status' => $validated['shipping_status']]);
                    if (!$order->assigned_to) {
                        $order->update(['assigned_to' => $user->id]);
                    }
                }
                break;

            case 'packer':
                // Gán packer nếu chưa có
                if (!$order->assigned_packer) {
                    $order->update(['assigned_packer' => $user->id]);
                }

                // Packer chỉ xác nhận đã đóng gói (status=shipped)
                if (isset($validated['status']) && $validated['status'] === 'shipped') {
                    $nextShippingStatus = $order->shipping_status;
                    if ($nextShippingStatus === null || $nextShippingStatus === '' || $nextShippingStatus === 'pending_confirmation') {
                        $nextShippingStatus = 'pending_pickup';
                    }
                    $order->update([
                        'status' => 'shipped',
                        // Ensure it enters the shipper queue
                        'shipping_status' => $nextShippingStatus,
                    ]);
                }
                break;

            case 'shipper':
                if (isset($validated['shipping_status'])) {
                    $order->update([
                        'shipping_status' => $validated['shipping_status'],
                        'assigned_shipper' => $user->id
                    ]);
                    if ($validated['shipping_status'] == 'delivered') {
                        $order->update(['status' => 'delivered']);
                    }
                }
                break;
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * Nhân viên tự nhận/phân công đơn cho mình (sales/packer/shipper)
     */
    public function assignOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userRole = $user->getRole;
        $roleCode = $userRole->role_code ?? '';

        if (!$user->hasPermission('employee.access')) {
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập khu vực nhân viên.');
        }

        switch ($roleCode) {
            case 'sales':
                if ($order->assigned_to && $order->assigned_to !== $user->id) {
                    return redirect()->back()->with('error', 'Đơn này đã được phân công cho nhân viên khác.');
                }
                $order->update(['assigned_to' => $user->id]);
                break;

            case 'packer':
                if ($order->assigned_packer && $order->assigned_packer !== $user->id) {
                    return redirect()->back()->with('error', 'Đơn này đã được phân công cho nhân viên khác.');
                }
                $order->update(['assigned_packer' => $user->id]);
                break;

            case 'shipper':
                if ($order->assigned_shipper && $order->assigned_shipper !== $user->id) {
                    return redirect()->back()->with('error', 'Đơn này đã được phân công cho nhân viên khác.');
                }
                $order->update(['assigned_shipper' => $user->id]);
                break;

            default:
                return redirect()->back()->with('error', 'Vai trò không hỗ trợ nhận đơn.');
        }

        return redirect()->back()->with('success', 'Đã nhận đơn thành công!');
    }
}

