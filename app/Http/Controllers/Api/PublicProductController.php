<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min(20, $limit));

        $query = Product::query()
            ->where('status', 'active');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'name', 'price', 'quantity', 'image_url']);

        return response()->json([
            'success' => true,
            'items' => $products,
        ]);
    }
}
