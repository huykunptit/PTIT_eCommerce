<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

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

    public function clear()
    {
        session()->forget('wishlist');
        return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ yêu thích']);
    }

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

