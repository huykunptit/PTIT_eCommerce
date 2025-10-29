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
        $orders = Order::with(['user', 'items.product'])->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
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
} 