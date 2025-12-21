<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
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

        return view('frontend.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        /**
         * @OA\Post(
         *     path="/api/cart/items",
         *     tags={"User - Cart (REST)"},
         *     summary="Thêm sản phẩm vào giỏ hàng (REST)",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"product_id"},
         *             @OA\Property(property="product_id", type="integer", example=19),
         *             @OA\Property(property="quantity", type="integer", example=1),
         *             @OA\Property(property="variant_id", type="integer", nullable=true, example=3)
         *         )
         *     ),
         *     @OA\Response(response=200, description="Đã thêm vào giỏ hàng"),
         *     @OA\Response(response=400, description="Số lượng không đủ hoặc dữ liệu không hợp lệ")
         * )
         */
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $quantity = $request->quantity ?? 1;

        $product = Product::findOrFail($productId);
        
        // Check variant if provided
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            if ($variant->product_id != $productId) {
                return response()->json(['error' => 'Variant không thuộc sản phẩm này'], 400);
            }
            if ($variant->stock < $quantity) {
                return response()->json(['error' => 'Số lượng không đủ'], 400);
            }
        } else {
            if ($product->quantity < $quantity) {
                return response()->json(['error' => 'Số lượng không đủ'], 400);
            }
        }

        $cart = session()->get('cart', []);
        $key = $productId . '_' . ($variantId ?? 'default');

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        $cartCount = count($cart);
        $cartTotal = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 0, ',', '.') . '₫',
        ]);
    }

    public function update(Request $request, $key)
    {
        /**
         * @OA\Put(
         *     path="/api/cart/items/{key}",
         *     tags={"User - Cart (REST)"},
         *     summary="Cập nhật số lượng một item trong giỏ hàng",
         *     @OA\Parameter(
         *         name="key",
         *         in="path",
         *         required=true,
         *         @OA\Schema(type="string")
         *     ),
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"quantity"},
         *             @OA\Property(property="quantity", type="integer", example=2)
         *         )
         *     ),
         *     @OA\Response(response=200, description="Cập nhật thành công"),
         *     @OA\Response(response=400, description="Số lượng không đủ"),
         *     @OA\Response(response=404, description="Không tìm thấy item trong giỏ")
         * )
         */
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }

        $item = $cart[$key];
        $product = Product::find($item['product_id']);
        
        if (isset($item['variant_id']) && $item['variant_id']) {
            $variant = ProductVariant::find($item['variant_id']);
            if ($variant->stock < $request->quantity) {
                return response()->json(['error' => 'Số lượng không đủ'], 400);
            }
        } else {
            if ($product->quantity < $request->quantity) {
                return response()->json(['error' => 'Số lượng không đủ'], 400);
            }
        }

        $cart[$key]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        $cartTotal = $this->calculateTotal($cart);
        $item = $cart[$key];
        $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
        $price = $variant ? $variant->price : $product->price;
        $subtotal = $price * $request->quantity;

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal, 0, ',', '.') . '₫',
            'total' => number_format($cartTotal, 0, ',', '.') . '₫',
        ]);
    }

    public function remove($key)
    {
        /**
         * @OA\Delete(
         *     path="/api/cart/items/{key}",
         *     tags={"User - Cart (REST)"},
         *     summary="Xóa một item khỏi giỏ hàng",
         *     @OA\Parameter(
         *         name="key",
         *         in="path",
         *         required=true,
         *         @OA\Schema(type="string")
         *     ),
         *     @OA\Response(response=200, description="Đã xóa khỏi giỏ hàng")
         * )
         */
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        $cartCount = count($cart);
        $cartTotal = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cart_count' => $cartCount,
            'total' => number_format($cartTotal, 0, ',', '.') . '₫',
        ]);
    }

    public function clear()
    {
        /**
         * @OA\Delete(
         *     path="/api/cart",
         *     tags={"User - Cart (REST)"},
         *     summary="Xóa toàn bộ giỏ hàng hiện tại",
         *     @OA\Response(response=200, description="Đã xóa toàn bộ giỏ hàng")
         * )
         */
        session()->forget('cart');
        return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng']);
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
                $price = $variant ? $variant->price : $product->price;
                $quantity = $item['quantity'] ?? 1;
                $total += $price * $quantity;
            }
        }
        return $total;
    }

    public function getCartData()
    {
        /**
         * @OA\Get(
         *     path="/api/cart",
         *     tags={"User - Cart (REST)"},
         *     summary="Lấy dữ liệu giỏ hàng hiện tại (REST)",
         *     @OA\Response(response=200, description="OK")
         * )
         */
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
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
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant' => $variant ? $variant->attributes : null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'image' => $imgSrc,
                ];
            }
        }

        return response()->json([
            'count' => count($cart),
            'items' => $cartItems,
            'total' => $total,
            'total_formatted' => number_format($total, 0, ',', '.') . '₫',
        ]);
    }
}

