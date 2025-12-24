<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    /**
     * Lấy đơn hàng gần đây cho dropdown
     *
     * @OA\Get(
     *     path="/orders/recent",
     *     tags={"User - Orders"},
     *     summary="Lấy danh sách đơn hàng gần đây của người dùng",
     *     description="Trả về tối đa 5 đơn gần nhất cho người dùng đang đăng nhập.",
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getRecentOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'shipping_status' => $order->shipping_status,
                    'payment_status' => $this->getPaymentStatus($order),
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'items_count' => $order->items->count(),
                    'items' => $order->items->take(3)->map(function($item) {
                        $product = $item->product;
                        $photos = explode(',', (string)($product->image_url ?? ''));
                        $img = trim($photos[0] ?? '');
                        $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                            ? $img 
                            : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));
                        
                        return [
                            'product_name' => $product->name,
                            'quantity' => $item->quantity,
                            'image' => $imgSrc,
                            'variant' => $item->variant ? $item->variant->attributes : null,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Lấy trạng thái thanh toán
     */
    private function getPaymentStatus($order)
    {
        if ($order->status == 'paid') {
            return 'Đã thanh toán';
        } elseif ($order->status == 'pending_payment') {
            return 'Chờ thanh toán';
        } else {
            return 'Chưa thanh toán';
        }
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'payments', 'cancellation', 'return'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Shopee-like: cho phép hủy khi còn ở "Chờ xác nhận" hoặc "Chờ lấy hàng"
        if (!in_array($order->shipping_status, ['pending_confirmation', 'pending_pickup'], true)) {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ xác nhận" hoặc "Chờ lấy hàng"');
        }

        $request->validate([
            'reason' => 'required|in:changed_mind,found_cheaper,wrong_item,delivery_too_long,payment_issue,other',
            'reason_detail' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Tạo cancellation request
            OrderCancellation::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'reason_detail' => $request->reason_detail,
                'status' => 'pending',
            ]);

            // Cập nhật trạng thái đơn hàng
            $order->update(['shipping_status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Yêu cầu hủy đơn hàng đã được gửi. Vui lòng chờ xác nhận từ admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order cancellation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng thử lại.');
        }
    }

    /**
     * Hoàn trả đơn hàng
     */
    public function return(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Chỉ cho phép hoàn trả khi đã nhận hàng
        if ($order->shipping_status != 'delivered') {
            return redirect()->back()->with('error', 'Chỉ có thể hoàn trả khi đã nhận hàng');
        }

        $request->validate([
            'reason' => 'required|in:defective,wrong_item,not_as_described,damaged_during_shipping,size_issue,color_issue,other',
            'reason_detail' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Tạo return request
            OrderReturn::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'reason_detail' => $request->reason_detail,
                'status' => 'pending',
            ]);

            // Cập nhật trạng thái đơn hàng
            $order->update(['shipping_status' => 'returned']);

            DB::commit();

            return redirect()->back()->with('success', 'Yêu cầu hoàn trả đã được gửi. Vui lòng chờ xác nhận từ admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order return error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hoàn trả. Vui lòng thử lại.');
        }
    }
}
