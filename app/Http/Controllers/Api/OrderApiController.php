<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class OrderApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"User - Orders (REST)"},
     *     summary="Danh sách đơn hàng của người dùng (REST)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with(['items.product', 'payments'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"User - Orders (REST)"},
     *     summary="Chi tiết đơn hàng (REST)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user();

        $order = Order::with(['items.product', 'items.variant', 'payments'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"User - Orders (REST)"},
     *     summary="Tạo đơn hàng mới từ giỏ hàng hiện tại",
     *     description="Sử dụng giỏ hàng session hiện tại (giống web), trả về chi tiết đơn và URL thanh toán nếu là VNPay hoặc Sepay.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_method","shipping_name","shipping_phone","shipping_address"},
     *             @OA\Property(property="payment_method", type="string", enum={"vnpay","cod","sepay"}, example="cod"),
     *             @OA\Property(property="shipping_name", type="string", example="Nguyễn Văn A"),
     *             @OA\Property(property="shipping_phone", type="string", example="0123456789"),
     *             @OA\Property(property="shipping_address", type="string", example="Số 1 Trần Duy Hưng, Hà Nội"),
     *             @OA\Property(property="shipping_email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="notes", type="string", example="Giao trong giờ hành chính")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tạo đơn hàng thành công"),
     *     @OA\Response(response=400, description="Giỏ hàng trống hoặc tổng tiền không hợp lệ"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:vnpay,cod,sepay',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng của bạn đang trống',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $total = 0;
            $orderItems = [];

            foreach ($cart as $item) {
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
                        return response()->json([
                            'success' => false,
                            'message' => "Sản phẩm {$product->name} (biến thể) không đủ số lượng",
                        ], 400);
                    }
                } else {
                    if ($product->quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Sản phẩm {$product->name} không đủ số lượng",
                        ], 400);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Tổng tiền không hợp lệ',
                ], 400);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'pending_payment',
                'shipping_status' => 'pending_pickup',
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_email' => $request->shipping_email,
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant']->id ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'price_at_purchase' => $item['price'],
                ]);

                if ($item['variant']) {
                    $item['variant']->decrement('stock', $item['quantity']);
                } else {
                    $item['product']->decrement('quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Gửi email xác nhận (giống web)
            try {
                $email = $order->shipping_email ?? $order->user->email ?? null;
                if ($email) {
                    Mail::to($email)->queue(new OrderConfirmationMail($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email (API): ' . $e->getMessage());
            }

            $response = [
                'success' => true,
                'order' => $order->load(['items.product', 'items.variant']),
            ];

            if ($request->payment_method === 'vnpay') {
                session([
                    'vnpay_order_id' => $order->id,
                    'vnpay_amount' => $total,
                ]);

                $response['payment_method'] = 'vnpay';
                $response['payment_redirect_url'] = route('payment.vnpay.create');
            } elseif ($request->payment_method === 'sepay') {
                session()->forget('cart');

                $transferDescription = 'ORDER_' . $order->id . '_' . Str::upper(Str::random(6));
                $order->update([
                    'notes' => trim(($order->notes ?? '') . ' | Sepay: ' . $transferDescription),
                ]);

                $response['payment_method'] = 'sepay';
                $response['payment_redirect_url'] = route('payment.sepay.show', ['order' => $order->id]);
                $response['transfer_description'] = $transferDescription;
            } else {
                // COD
                session()->forget('cart');
                $response['payment_method'] = 'cod';
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order API store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"User - Orders (REST)"},
     *     summary="Hủy đơn hàng (REST)",
     *     description="Chỉ cho phép hủy khi shipping_status đang 'pending_pickup'.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Hủy thành công"),
     *     @OA\Response(response=400, description="Không thể hủy trong trạng thái hiện tại"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function cancel(Request $request, int $id)
    {
        $user = $request->user();

        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if ($order->shipping_status !== 'pending_pickup') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái "Chờ lấy hàng"',
            ], 400);
        }

        $order->update(['shipping_status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được hủy',
            'order' => $order,
        ]);
    }
}


