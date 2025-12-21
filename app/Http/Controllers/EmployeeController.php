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
                ->where('status', 'pending')
                ->count(),
            'processing_orders' => Order::where('assigned_to', Auth::id())
                ->where('status', 'processing')
                ->count(),
            'completed_today' => Order::where('assigned_to', Auth::id())
                ->where('status', 'delivered')
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
        $ordersToPack = Order::where('status', 'processing')
            ->where(function($q) {
                $q->where('assigned_packer', Auth::id())
                  ->orWhereNull('assigned_packer');
            })
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_to_pack' => Order::where('status', 'processing')
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
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
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
        $user = Auth::user();
        $userRole = $user->getRole;
        $roleCode = $userRole->role_code ?? '';

        // Phân quyền chi tiết cho thao tác cập nhật đơn
        if ($roleCode === 'shipper') {
            if (!$user->hasPermission('shipping.update')) {
                return response()->json(['error' => 'Bạn không có quyền cập nhật giao hàng'], 403);
            }
        } else {
            if (!$user->hasPermission('orders.manage')) {
                return response()->json(['error' => 'Bạn không có quyền chỉnh sửa đơn hàng'], 403);
            }
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,processing,shipped,delivered',
            'shipping_status' => 'nullable|in:pending_pickup,in_transit,delivered',
            'note' => 'nullable|string|max:500',
        ]);

        // Phân quyền theo vai trò
        switch ($roleCode) {
            case 'sales':
                if (isset($validated['status'])) {
                    $order->update(['status' => $validated['status']]);
                    if (!$order->assigned_to) {
                        $order->update(['assigned_to' => $user->id]);
                    }
                }
                break;

            case 'packer':
                if (isset($validated['status']) && $validated['status'] == 'shipped') {
                    $order->update([
                        'status' => 'shipped',
                        'assigned_packer' => $user->id
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
}

