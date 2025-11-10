<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BannerController;
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
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            // 'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return view('admin.index', compact('stats'));
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

        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully!');
    }

    /**
     * Cập nhật shipping status
     */
    public function updateShippingStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'shipping_status' => 'required|in:pending_pickup,in_transit,delivered,cancelled,returned',
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
            $order->update(['shipping_status' => 'pending_pickup']);
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
} 