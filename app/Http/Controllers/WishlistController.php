<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use OpenApi\Annotations as OA;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = session()->get('wishlist', []);
        $wishlistItems = [];

        foreach ($wishlist as $productId) {
            $product = Product::find($productId);
            if ($product) {
                $photos = explode(',', (string)($product->image_url ?? ''));
                $img = trim($photos[0] ?? '');
                $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                    ? $img 
                    : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));

                $variants = ProductVariant::where('product_id', $product->id)
                    ->where('status', 'active')
                    ->get();

                $minPrice = $product->price;
                if ($variants->count() > 0) {
                    $minPrice = min($variants->pluck('price')->toArray());
                }

                $wishlistItems[] = [
                    'product' => $product,
                    'image' => $imgSrc,
                    'min_price' => $minPrice,
                    'variants' => $variants,
                ];
            }
        }

        return view('frontend.wishlist.index', compact('wishlistItems'));
    }

    /**
     * @OA\Post(
     *     path="/wishlist/add",
     *     tags={"User - Wishlist"},
     *     summary="Thêm sản phẩm vào danh sách yêu thích (session-based)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=19)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Đã thêm vào yêu thích"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $wishlist = session()->get('wishlist', []);

        if (!in_array($productId, $wishlist)) {
            $wishlist[] = $productId;
            session()->put('wishlist', $wishlist);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào yêu thích',
            'wishlist_count' => count($wishlist),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/wishlist/remove/{productId}",
     *     tags={"User - Wishlist"},
     *     summary="Xóa một sản phẩm khỏi danh sách yêu thích",
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Đã xóa khỏi yêu thích")
     * )
     */
    public function remove($productId)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_values(array_diff($wishlist, [$productId]));
        session()->put('wishlist', $wishlist);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi yêu thích',
            'wishlist_count' => count($wishlist),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/wishlist/clear",
     *     tags={"User - Wishlist"},
     *     summary="Xóa toàn bộ danh sách yêu thích",
     *     @OA\Response(response=200, description="Đã xóa toàn bộ yêu thích")
     * )
     */
    public function clear()
    {
        session()->forget('wishlist');
        return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ yêu thích']);
    }

    /**
     * @OA\Get(
     *     path="/wishlist/data",
     *     tags={"User - Wishlist"},
     *     summary="Lấy dữ liệu danh sách yêu thích hiện tại",
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function getWishlistData()
    {
        $wishlist = session()->get('wishlist', []);
        $wishlistItems = [];

        foreach ($wishlist as $productId) {
            $product = Product::find($productId);
            if ($product) {
                $photos = explode(',', (string)($product->image_url ?? ''));
                $img = trim($photos[0] ?? '');
                $imgSrc = $img && \Illuminate\Support\Str::startsWith($img, ['http://','https://']) 
                    ? $img 
                    : ($img ? asset($img) : asset('backend/img/thumbnail-default.jpg'));

                $wishlistItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'image' => $imgSrc,
                ];
            }
        }

        return response()->json([
            'count' => count($wishlist),
            'items' => $wishlistItems,
        ]);
    }
}

