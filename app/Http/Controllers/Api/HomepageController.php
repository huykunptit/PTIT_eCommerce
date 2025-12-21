<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class HomepageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/homepage",
     *     tags={"Homepage"},
     *     summary="Cấu hình & dữ liệu hiển thị trang chủ",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="subtitle", type="string"),
     *             @OA\Property(property="hero_image", type="string"),
     *             @OA\Property(property="banner_image", type="string"),
     *             @OA\Property(property="banners", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="featured_categories", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="featured_products", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="featured_posts", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $bannerIds = $this->parseIds(SystemSetting::get('home_banner_ids', ''));
        $categoryIds = $this->parseIds(SystemSetting::get('home_category_ids', ''));
        $productIds = $this->parseIds(SystemSetting::get('home_product_ids', ''));
        $postIds = $this->parseIds(SystemSetting::get('home_post_ids', ''));

        $banners = Banner::query()
            ->when($bannerIds, fn($q) => $q->whereIn('id', $bannerIds)->orderByRaw('FIELD(id, ' . implode(',', $bannerIds) . ')'))
            ->when(!$bannerIds, fn($q) => $q->orderByDesc('id'))
            ->get();

        $categories = $categoryIds
            ? Category::whereIn('id', $categoryIds)->get()
            : Category::orderBy('name')->take(8)->get();

        $products = $productIds
            ? Product::whereIn('id', $productIds)->get()
            : Product::where('status', 'active')->orderByDesc('id')->take(12)->get();

        $posts = $postIds
            ? Post::whereIn('id', $postIds)->orderByDesc('id')->get()
            : Post::where('status', 'active')->orderByDesc('id')->take(6)->get();

        return response()->json([
            'title' => SystemSetting::get('home_title', 'PTIT Store'),
            'subtitle' => SystemSetting::get('home_subtitle', 'Welcome to our shop'),
            'hero_image' => SystemSetting::get('home_hero_image', ''),
            'banner_image' => SystemSetting::get('home_banner_image', ''),
            'banners' => $banners,
            'featured_categories' => $categories,
            'featured_products' => $products,
            'featured_posts' => $posts,
        ]);
    }

    /**
     * Parse comma-separated or JSON array of IDs into array<int>.
     *
     * @param mixed $value
     * @return array<int,int>
     */
    protected function parseIds($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('intval', $value), fn($v) => $v > 0));
        }

        $str = trim((string) $value);
        if ($str === '') {
            return [];
        }

        if (str_starts_with($str, '[')) {
            $decoded = json_decode($str, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('intval', $decoded), fn($v) => $v > 0));
            }
        }

        return array_values(array_filter(array_map('intval', preg_split('/[\s,]+/', $str)), fn($v) => $v > 0));
    }
}


