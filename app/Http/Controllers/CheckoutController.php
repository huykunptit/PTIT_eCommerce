<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
        }
        
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $key => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $variant = null;
                if (isset($item['variant_id']) && $item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                }
                
                $price = $variant ? $variant->price : $product->price;
                $quantity = $item['quantity'] ?? 1;
                $subtotal = $price * $quantity;
                $total += $subtotal;
                
                $photos = explode(',', (string)($product->image_url ?? ''));
                $img = trim($photos[0] ?? '');
                $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                    ? $img 
                    : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                
                $cartItems[] = [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'image' => $imgSrc,
                ];
            }
        }
        
        $user = Auth::user();
        
        return view('frontend.checkout.index', compact('cartItems', 'total', 'user'));
    }
    

    public function store(Request $request)
    {
        try {
            $request->validate([
                'payment_method' => 'required|in:vnpay,cod,sepay',
                'shipping_name' => 'required|string|max:255',
                'shipping_phone' => 'required|string|max:20',
                'shipping_address' => 'required|string|max:500',
                'shipping_email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
        }
        
        DB::beginTransaction();
        try {
           
            $total = 0;
            $orderItems = [];
            
            foreach ($cart as $key => $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    continue;
                }
                
                $variant = null;
                if (isset($item['variant_id']) && $item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if (!$variant || $variant->product_id != $product->id) {
                        continue;
                    }
                }
                
                $price = $variant ? $variant->price : $product->price;
                $quantity = $item['quantity'] ?? 1;
                $subtotal = $price * $quantity;
                $total += $subtotal;
                
                
                if ($variant) {
                    if ($variant->stock < $quantity) {
                        DB::rollBack();
                        $attrs = is_array($variant->attributes) ? $variant->attributes : (is_string($variant->attributes) ? json_decode($variant->attributes, true) : []);
                        $size = $attrs['size'] ?? 'N/A';
                        $option = $attrs['option'] ?? 'N/A';
                        return redirect()->back()->with('error', "Sản phẩm {$product->name} - Size: {$size}, Option: {$option} không đủ số lượng");
                    }
                } else {
                    if ($product->quantity < $quantity) {
                        DB::rollBack();
                        return redirect()->back()->with('error', "Sản phẩm {$product->name} không đủ số lượng");
                    }
                }
                
                $orderItems[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }
            
            if ($total <= 0) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tổng tiền không hợp lệ');
            }
            

            try {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total_amount' => $total,
                    'status' => $request->payment_method == 'cod' ? 'pending' : 'pending_payment',
                    'shipping_status' => 'pending_pickup', // Mặc định: Chờ lấy hàng
                    'shipping_name' => $request->shipping_name,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_email' => $request->shipping_email,
                    'notes' => $request->notes,
                    'payment_method' => $request->payment_method,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                \Log::error('Order creation error: ' . $e->getMessage());
                if (strpos($e->getMessage(), 'Unknown column') !== false) {
                    return redirect()->back()
                        ->with('error', 'Lỗi database: Các cột mới chưa được tạo. Vui lòng chạy migration: php artisan migrate')
                        ->withInput();
                }
                throw $e;
            }
            
            // Tạo order items và cập nhật tồn kho
            foreach ($orderItems as $item) {
                try {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'variant_id' => $item['variant']->id ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                        'price_at_purchase' => $item['price'],
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    DB::rollBack();
                    \Log::error('OrderItem creation error: ' . $e->getMessage());
                    if (strpos($e->getMessage(), 'Unknown column') !== false) {
                        return redirect()->back()
                            ->with('error', 'Lỗi database: Các cột mới chưa được tạo. Vui lòng chạy migration: php artisan migrate')
                            ->withInput();
                    }
                    throw $e;
                }
                
                // Cập nhật tồn kho
                if ($item['variant']) {
                    $item['variant']->decrement('stock', $item['quantity']);
                } else {
                    $item['product']->decrement('quantity', $item['quantity']);
                }
            }
            
            DB::commit();
            
            // Gửi email xác nhận đơn hàng (qua queue nếu QUEUE_CONNECTION != sync)
            try {
                $email = $order->shipping_email ?? $order->user->email ?? null;
                if ($email) {
                    Mail::to($email)->queue(new OrderConfirmationMail($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                // Không throw exception, chỉ log lỗi để không ảnh hưởng đến quá trình đặt hàng
            }

            // Tạo notification cho admin về đơn hàng mới
            try {
                $admins = User::whereHas('getRole', function($q) {
                    $q->where('role_code', 'admin');
                })->get();
                
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\NewOrderNotification($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create notification: ' . $e->getMessage());
            }
            
            // Xử lý thanh toán
            if ($request->payment_method == 'vnpay') {
                // Lưu thông tin vào session để VNPayController có thể lấy
                // KHÔNG xóa giỏ hàng ở đây - chỉ xóa sau khi thanh toán thành công
                session([
                    'vnpay_order_id' => $order->id,
                    'vnpay_amount' => $total,
                ]);
                
                return redirect()->route('payment.vnpay.create');
            } elseif ($request->payment_method == 'sepay') {
                // Thanh toán Sepay - chuyển sang trang hiển thị QR
                session()->forget('cart');

                $transferDescription = 'ORDER_'.$order->id.'_'.Str::upper(Str::random(6));
                $order->update([
                    'notes' => trim(($order->notes ?? '').' | Sepay: '.$transferDescription),
                ]);

                return redirect()->route('payment.sepay.show', ['order' => $order->id])
                    ->with('transfer_description', $transferDescription);
            } else {
                // COD - thanh toán khi nhận hàng, xóa giỏ hàng ngay vì đã đặt hàng thành công
                session()->forget('cart');
                return redirect()->route('checkout.success', ['order_id' => $order->id])
                    ->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            \Log::error('Checkout error trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Trang thành công
     */
    public function success(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = $orderId ? Order::with('items.product')->find($orderId) : null;
        
        return view('frontend.checkout.success', compact('order'));
    }

    /**
     * Trang hiển thị QR thanh toán Sepay
     */
    public function sepay(Request $request, $orderId)
    {
         $order = Order::findOrFail($orderId);

         // Số tài khoản VCB: lấy từ ENV, hoặc default là 1022752041 nếu chưa cấu hình
         $account  = env('SEPAY_ACCOUNT', '1022752041');             
         $bank     = env('SEPAY_BANK', 'VCB');         
         $amount   = (int) ($order->total_amount ?? 0); 
        $template = env('SEPAY_TEMPLATE', 'compact');
        $download = env('SEPAY_DOWNLOAD', 0);

        $des = $request->session()->get('transfer_description');
        if (!$des) {
            $des = 'ORDER_'.$order->id.'_'.Str::upper(Str::random(6));
        }

        $qrUrl = sprintf(
            'https://qr.sepay.vn/img?acc=%s&bank=%s&amount=%d&des=%s&template=%s&download=%s',
            urlencode($account),
            urlencode($bank),
            $amount,
            urlencode($des),
            urlencode($template),
            urlencode($download)
        );

        return view('frontend.payment.sepay', compact('order', 'qrUrl', 'account', 'bank', 'des', 'amount'));
    }

    /**
     * API: Lấy trạng thái đơn hàng (dùng cho trang Sepay polling realtime)
     */
    public function getOrderStatus($orderId)
    {
        $order = Order::findOrFail($orderId);

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'shipping_status' => $order->shipping_status,
        ]);
    }
}

