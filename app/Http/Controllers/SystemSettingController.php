<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $data = [
            'home_title' => SystemSetting::get('home_title', 'PTIT Store'),
            'home_subtitle' => SystemSetting::get('home_subtitle', 'Welcome to our shop'),
            'home_hero_image' => SystemSetting::get('home_hero_image', ''),
            'home_banner_image' => SystemSetting::get('home_banner_image', ''),
            'home_banner_ids' => SystemSetting::get('home_banner_ids', ''),
            'home_post_ids' => SystemSetting::get('home_post_ids', ''),
            'home_category_ids' => SystemSetting::get('home_category_ids', ''),
            'home_product_ids' => SystemSetting::get('home_product_ids', ''),
        ];
        $lists = [
            'banners' => Banner::orderByDesc('id')->get(['id','title','photo','status']),
            'categories' => Category::orderBy('name')->get(['id','name']),
            'products' => Product::orderByDesc('id')->take(50)->get(['id','name','price']),
            'posts' => Post::where('status', 'active')->orderByDesc('id')->take(50)->get(['id','title','status']),
        ];

        return view('admin.system_settings.edit', [
            'data' => $data,
            'lists' => $lists,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'home_title' => 'nullable|string|max:255',
            'home_subtitle' => 'nullable|string|max:500',
            'home_hero_image' => 'nullable|string|max:1000',
            'home_banner_image' => 'nullable|string|max:1000',
            'home_banner_ids' => 'nullable|string|max:2000',
            'home_post_ids' => 'nullable|string|max:2000',
            'home_category_ids' => 'nullable|string|max:2000',
            'home_product_ids' => 'nullable|string|max:2000',
        ]);

        foreach ($validated as $k => $v) {
            SystemSetting::set($k, $v ?? '');
        }

        return redirect()->route('admin.system_settings.edit')->with('success', 'Đã cập nhật cấu hình trang chủ.');
    }
}


