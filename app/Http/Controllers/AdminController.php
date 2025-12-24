<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BannerController;
use App\Mail\OrderStatusUpdateMail;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Dashboard Admin
    public function dashboard()
    {
        // Basic stats
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
        ];

        // Revenue statistics - Last 7 days
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $revenueData[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'revenue' => (float) $revenue
            ];
        }

        // Orders count - Last 7 days
        $ordersData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Order::whereDate('created_at', $date)->count();
            $ordersData[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'count' => $count
            ];
        }

        // Order status distribution
        $orderStatusData = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Top selling products
        $topProducts = \DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', \DB::raw('SUM(order_items.quantity) as total_sold'), \DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue (Last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $revenue = Order::whereYear('created_at', now()->subMonths($i)->year)
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $monthlyRevenue[] = [
                'month' => now()->subMonths($i)->format('M Y'),
                'revenue' => (float) $revenue
            ];
        }

        // Total revenue
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $monthRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        return view('admin.index', compact(
            'stats',
            'revenueData',
            'ordersData',
            'orderStatusData',
            'topProducts',
            'monthlyRevenue',
            'totalRevenue',
            'todayRevenue',
            'monthRevenue'
        ));
    }




    // Management Orders
    public function orders()
    {
        $orders = Order::with(['user', 'items.product', 'items.variant', 'cancellation', 'return'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.order.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variant', 'payments', 'cancellation', 'return'])->findOrFail($id);
        return view('admin.order.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        // Gửi email thông báo cập nhật trạng thái (queue)
        try {
            $email = $order->shipping_email ?? $order->user->email ?? null;
            if ($email) {
                Mail::to($email)->queue(new OrderStatusUpdateMail($order, $validated['status']));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send order status update email: ' . $e->getMessage());
        }

        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully!');
    }

    /**
     * Xác nhận đã thanh toán (cho các đơn chờ thanh toán như Sepay/VNPay)
     */
    public function confirmPayment($id)
    {
        $order = Order::findOrFail($id);

        // Chỉ cho phép xác nhận nếu đang ở trạng thái chờ thanh toán
        if ($order->status !== 'pending_payment') {
            return redirect()->back()->with('error', 'Chỉ có thể xác nhận thanh toán cho đơn đang ở trạng thái chờ thanh toán.');
        }

        $order->update([
            'status' => 'paid',
        ]);

        // Gửi email thông báo đã thanh toán
        try {
            $email = $order->shipping_email ?? $order->user->email ?? null;
            if ($email) {
                Mail::to($email)->queue(new OrderStatusUpdateMail($order, 'paid'));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment confirmed email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Đơn hàng đã được đánh dấu là đã thanh toán.');
    }

    /**
     * Cập nhật shipping status
     */
    public function updateShippingStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'shipping_status' => 'required|in:pending_confirmation,pending_pickup,in_transit,delivered,cancelled,returned',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'Trạng thái vận chuyển đã được cập nhật!');
    }

    /**
     * Xử lý yêu cầu hủy đơn hàng
     */
    public function handleCancellation(Request $request, $orderId)
    {
        $order = Order::with('cancellation')->findOrFail($orderId);
        $cancellation = $order->cancellation;

        if (!$cancellation) {
            return redirect()->back()->with('error', 'Không tìm thấy yêu cầu hủy đơn hàng');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        if ($request->action == 'approve') {
            $cancellation->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
            ]);
            $order->update(['shipping_status' => 'cancelled']);
        } else {
            $cancellation->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
            ]);
            // Khôi phục trạng thái ban đầu
            $order->update(['shipping_status' => 'pending_confirmation']);
        }

        return redirect()->back()->with('success', 'Yêu cầu hủy đơn hàng đã được xử lý!');
    }

    /**
     * Xử lý yêu cầu hoàn trả
     */
    public function handleReturn(Request $request, $orderId)
    {
        $order = Order::with('return')->findOrFail($orderId);
        $return = $order->return;

        if (!$return) {
            return redirect()->back()->with('error', 'Không tìm thấy yêu cầu hoàn trả');
        }

        $request->validate([
            'action' => 'required|in:approve,reject,processing,completed',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $return->update([
            'status' => $request->action,
            'admin_note' => $request->admin_note,
        ]);

        if ($request->action == 'approve') {
            $order->update(['shipping_status' => 'returned']);
        } elseif ($request->action == 'completed') {
            $order->update(['shipping_status' => 'returned']);
        }

        return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã được xử lý!');
    }

    /**
     * API: Get notifications for admin
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        // Get unread notifications (last 10)
        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Thông báo',
                    'message' => $notification->data['message'] ?? null,
                    'type' => $notification->data['type'] ?? 'info',
                    'url' => $notification->data['url'] ?? null,
                    'unread' => true,
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Export Orders to Excel/PDF
     */
    public function exportOrders(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel or pdf
        
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'pdf') {
            return $this->exportOrdersToPDF($orders);
        } else {
            return $this->exportOrdersToExcel($orders);
        }
    }

    /**
     * Export Products to Excel
     */
    public function exportProducts(Request $request)
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->exportProductsToExcel($products);
    }

    /**
     * Export Users to Excel
     */
    public function exportUsers(Request $request)
    {
        $users = User::with('getRole')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->exportUsersToExcel($users);
    }

    /**
     * Export Orders to Excel
     */
    private function exportOrdersToExcel($orders)
    {
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['ID', 'Khách hàng', 'Email', 'SĐT', 'Tổng tiền', 'Trạng thái', 'Ngày đặt']);
            
            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->shipping_email ?? $order->user->email ?? 'N/A',
                    $order->shipping_phone ?? 'N/A',
                    number_format($order->total_amount, 0, ',', '.') . '₫',
                    $order->status,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Orders to PDF (Simple HTML to PDF)
     */
    private function exportOrdersToPDF($orders)
    {
        $html = view('admin.exports.orders-pdf', compact('orders'))->render();
        
        // For now, return HTML view. In production, use DomPDF or similar
        return response()->make($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="orders_' . date('Y-m-d_His') . '.html"');
    }

    /**
     * Export Products to Excel
     */
    private function exportProductsToExcel($products)
    {
        $filename = 'products_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['ID', 'Tên sản phẩm', 'Danh mục', 'Giá', 'Số lượng', 'Trạng thái', 'Tags', 'Ngày tạo']);
            
            foreach ($products as $product) {
                $tags = $product->tags->pluck('name')->join(', ');
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->category->name ?? 'N/A',
                    number_format($product->price, 0, ',', '.') . '₫',
                    $product->quantity,
                    $product->status,
                    $tags ?: 'N/A',
                    $product->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Users to Excel
     */
    private function exportUsersToExcel($users)
    {
        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['ID', 'Tên', 'Email', 'SĐT', 'Địa chỉ', 'Vai trò', 'Trạng thái', 'Ngày đăng ký']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone_number ?? 'N/A',
                    $user->address ?? 'N/A',
                    $user->getRole->role_name ?? 'N/A',
                    $user->status ?? 'active',
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Trang danh sách thông báo cho admin
     */
    public function notificationIndex()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.notification.index', compact('notifications'));
    }
} 